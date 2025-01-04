<?php

namespace Framework;

use App\Controllers\ErrorController;

class Router
{
    protected $routes = [];

    /**
     * Register a Route
     * @param string $method
     * @param string $uri
     * @param string $action
     * @return void
     */
    public function registerRoute($method,$uri,$action)
    {
        list($controller,$controllerMethod) = explode("@",$action);
        $this->routes[]=[
            "method"=>$method,
            "uri"=>$uri,
            "controller"=>$controller,
            "controllerMethod"=>$controllerMethod,
        ];
    }

    /**
     * Load Error Page
     * @param int $status
     * @return void
     */
    // public function error($status="notFound")
    // {
    //     $er = new ErrorController();
    //     $er->$status();
    //     exit;
    // }
    
    /**
     * Add a Get Request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri,$controller)
    {
        $this->registerRoute("GET",$uri,$controller);
    }

    /**
     * Add a Post Request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri,$controller)
    {
        $this->registerRoute("POST",$uri,$controller);
    }

    /**
     * Add a Put Request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri,$controller)
    {
        $this->registerRoute("PUT",$uri,$controller);
    }

    /**
     * Add a Delete Request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri,$controller)
    {
        $this->registerRoute("DELETE",$uri,$controller);
    }

    /**
     * Route the Request
     * @param string $method
     * @param string $uri
     * @return void
     */
    public function route($uri)
    {
        $requestMethod=$_SERVER["REQUEST_METHOD"];

        // Check _method
        if($requestMethod === 'POST' && isset($_POST["_method"]))
        {
            $requestMethod = strtoupper($_POST["_method"]);
        }

        $uriSegments = explode("/",trim($uri,"/"));
        foreach($this->routes as $route)
        {
            $routeSegments = explode("/",trim($route["uri"],"/"));
            $match = true;
            if(count($routeSegments) === count($uriSegments) && strtoupper($requestMethod === $route["method"]))
            {
                $params = [];
                $match = true;
                for($i=0;$i<count($uriSegments);$i++)
                {
                    if($routeSegments[$i] !== $uriSegments[$i] && !preg_match("/\{(.+?)\}/",$routeSegments[$i]))
                    {
                        $match = false;
                        break;
                    }
                    if(preg_match("/\{(.+?)\}/",$routeSegments[$i],$matches))
                    {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }
                if($match)
                {
                    $controller = "App\\controllers\\".$route["controller"];
                    $controllerMethod = $route["controllerMethod"];
                    $ct=new $controller();
                    $ct->$controllerMethod($params);
                    return;
                }
            }
        }
        ErrorController::notFound();
        exit;
    }
}