<?php
    namespace App\Controllers;

    use Slim\Container;
    use Slim\Http\Request;
    use Slim\Http\Response;

    class TestController{
        public $container;
        //以来容器的注入
        public function __construct(Container $container) {
            $this->container = $container;
        }

        public function index(Request $request, Response $response){
            echo $this->container->get('aa');
            var_dump($_GET);
            var_dump($_SERVER);
        }
    }
