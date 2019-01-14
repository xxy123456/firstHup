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