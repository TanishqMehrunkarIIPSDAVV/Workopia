<?php

class Router
{
    protected $routes = [];

    /**
     * Register a Route
     * @param string $method
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function registerRoute($method,$uri,$controller)
    {
        $this->routes[]=[
            "method"=>$method,
            "uri"=>$uri,
            "controller"=>$controller,
        ];
    }

    /**
     * Load Error Page
     * @param int $status
     * @return void
     */
    public function error($status=404)
    {
        http_response_code($status);
        load("error/$status");
        exit;
    }
    
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
    public function route($uri,$method)
    {
        foreach($this->routes as $route)
        {
            if($route["uri"] === $uri && $route["method"] === $method)
            {
                require_once basePath($route["controller"]);
                return;
            }
        }
        $this->error();
    }
}