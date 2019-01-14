

本文主要介绍slim和swoole的整合，docker只做简单介绍
---
**docker**

  Docker 是一个开源的应用容器引擎，让开发者可以打包他们的应用以及依赖包到一个可移植的容器中，然后发布到任何流行的 Linux 机器上，也可以实现虚拟化。容器是完全使用沙箱机制，相互之间不会有任何接口

优点
    1. 简化配置:同一个Docker的配置可以在不同的环境环境中使用, 这样就降低了硬件要求和应用环境之间耦合度.
    2. 代码流水化管理：代码从开发者的机器到最终在生产环境上的部署, 需要经过很多的中坚环境. 而每一个中间环境都有自己微小的差别, Docker
    给应用提供了一个从开发到上线均一致的环境, 让代码的流水线变得简单不少.
    3. 提升开发效率：快速搭建开发环境，开发环境尽量贴近生产环境。

**slim**
___
    
  slim是一个非常小的框架，非常适合写api。这个框架主要做了俩件事，路由的分发和依赖的注入。
    
slim简单入门

```
require __DIR__ . '/../vendor/autoload.php';
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
$app->get('/', function (Request $request, Response $response, array $args) {
    echo 132;
});
$app->run();
```
在浏览器中输入http://127.0.0.1 即可看见输出132

**swoole**

***

 [swoole](https://www.swoole.com/)使 PHP 开发人员可以编写高性能的异步并发 TCP、UDP、Unix Socket、HTTP，
    WebSocket 服务。Swoole 可以广泛应用于互联网、移动通信、企业软件、云计算、网络游戏、物联网（IOT）、车联网、智能家居等领域。 使用 
    PHP + Swoole 作为网络通信框架，可以使企业 IT 研发团队的效率大大提升，更加专注于开发创新产品。



**了解了docker、slime和swoole后我们开始结合slim-swoole**

Slim是通过当前路由(譬如/user/2，不带查询字符串)和http方法来找到正确的回调函数的。但是是怎么正确找到的呢？肯定是$_SERVER。查看$app->run()源码：
```
public function run($silent = false)
    {
        $response = $this->container->get('response');

        try {
            ob_start();
            $response = $this->process($this->container->get('request'), $response);
        } catch (InvalidMethodException $e) {
            $response = $this->processInvalidMethod($e->getRequest(), $response);
        } finally {
            $output = ob_get_clean();
        }

        if (!empty($output) && $response->getBody()->isWritable()) {
            $outputBuffering = $this->container->get('settings')['outputBuffering'];
            if ($outputBuffering === 'prepend') {
                // prepend output buffer content
                $body = new Http\Body(fopen('php://temp', 'r+'));
                $body->write($output . $response->getBody());
                $response = $response->withBody($body);
            } elseif ($outputBuffering === 'append') {
                // append output buffer content
                $response->getBody()->write($output);
            }
        }

        $response = $this->finalize($response);

        if (!$silent) {
            $this->respond($response);
        }

        return $response;
    }
```
 发现$request对象是从容器取出来的，那$request是怎么注册的呢？？，那就看App类的构造函数了，最后发现Container类的构造函数中的registerDefaultServices()方法：
  
  ```
  private function registerDefaultServices($userSettings)
    {
        $defaultSettings = $this->defaultSettings;

        /**
         * This service MUST return an array or an
         * instance of \ArrayAccess.
         *
         * @return array|\ArrayAccess
         */
        $this['settings'] = function () use ($userSettings, $defaultSettings) {
            return new Collection(array_merge($defaultSettings, $userSettings));
        };

        $defaultProvider = new DefaultServicesProvider();
        $defaultProvider->register($this);
    }

```
接着查看$defaultProvider->register()
```
 public function register($container)
    {
        if (!isset($container['environment'])) {
            /**
             * This service MUST return a shared instance
             * of \Slim\Interfaces\Http\EnvironmentInterface.
             *
             * @return EnvironmentInterface
             */
            $container['environment'] = function () {
                return new Environment($_SERVER);
            };
        }

        if (!isset($container['request'])) {
            /**
             * PSR-7 Request object
             *
             * @param Container $container
             *
             * @return ServerRequestInterface
             */
            $container['request'] = function ($container) {
                return Request::createFromEnvironment($container->get('environment'));
            };
        }
````


从上面我们可以得出，我们主要注册一个自定义的environment依赖就行，原$_server的信息可以从swoole的$request->server
中取。

**接下来我们编写一个swoole的http服务**

```
<?php
    use Slim\Http\Environment;
    class Http{
        public $host='0.0.0.0';
        public $port=666;
        public $http="";
        public function __construct()
        {
            $this->http = new Swoole\Http\Server($this->host, $this->port);
            $this->http->set([
                //'daemonize'=>1,
                'worker_num'=>4
            ]);
            $this->http->on('WorkerStart',[$this,'WorkerStart']);
            $this->http->on('request',[$this,'request']);
            $this->http->start();
        }
        public function WorkerStart($server,$worker_id){
            define("ROOT",__DIR__.'/..');
            define('APP', ROOT . '/src');
            require ROOT . '/vendor/autoload.php';
            echo $worker_id."\n";
        }
        public function request($request,$response){

            $config = require APP . '/settings.php';
            $config['environment'] = function () use($request) {
                $_SERVER = [];
                foreach ($request->server as $key => $value) {
                    $_SERVER[strtoupper($key)] = $value;
                }
                return new Environment($_SERVER);
            };
            $_GET=[];
            if(isset($request->get)){
                foreach ($request->get as $key => $value) {
                    $_GET[$key]=$value;
                }
            }
            $_POST=[];
            if(isset($request->post)){
                foreach ($request->post as $key => $value) {
                    $_POST[$key]=$value;
                }
            }
            $app = new \Slim\App($config);
            require APP . '/dependencies.php';
            require APP . '/routes.php';

            $dotenv = new Dotenv\Dotenv(dirname(__DIR__) );
            $dotenv->load();
            // Run app
            $slimResponse = $app->run(true);
            $headers = $slimResponse->getHeaders();
            foreach ($headers as $name => $values) {
                $response->header($name, implode(", ", $values));
            }
            $response->header("X-Powered-By", "Salamander");
            $response->end($slimResponse->getBody());
        }
    }
    new Http();
 ```
 
 **需要注意以下几点**
 
  1. onWorkstart事件在Worker进程/Task进程启动时发生，这里创建的对象可以在进程生命周期内使用。意思就是说，可以将每次都需要请求的资源放到这里,相当于是一个预加载。放到这里onWorkstart里面的和放到onRequest里面俩者的区别是，onWorkstart在生命周期内只请求一次，而放到onRequest里面是每次请求都要加载一遍，优点很明显，可以提升性能。
    2. 由于swoole是常驻内存的，所以每次请求的时候，我们需要将$_GET，$_POST,$_SERVER等变量清空重新赋值，这个是新手特别容易跳的坑。
    
    
**最后说一下docker下php安装swoole扩展和启动swoole扩展**
    
安装php扩展有俩种方式，第一种是使用已经集成好的扩展的php镜像，优点是方便快捷，缺点是如果安装其他扩展将会有局限性。第二种就是我使用的方式，使用官方的php-fpm镜像，优点是可以使用官方提供的安装扩展方法，缺点是生成容器时候比较耗时。根据自己的需求安装即可。
    
php的Dockerfile  如下
   
```
    FROM php:7.2-fpm
    COPY . /home
    RUN pecl install swoole-4.2.12 \
        && docker-php-ext-enable swoole
```
 docker-compose.yml中的command后的命令是指在容器生成后执行的命令，即开启Swoole
    
```
    services:
      php:
          container_name: php
          build:
            context: .
            dockerfile: docker/php/Dockerfile
          volumes:
            - ./:/home
          ports:
            - "666:666"
          command: php /home/server/server.php
          #stdin_open: true
          #tty: true
          networks:
            - slim-network
    networks:
      slim-network:
        driver: "bridge"
    
```
    