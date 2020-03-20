<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2/15/19
 * Time: 10:59 PM
 */

namespace Application\Http;

use Application\Foundation\Request;

class Router
{
    private $routes; 
    private $prefix; 
    private $middleware; 
    public $route;

    public function __construct()
    {
        $this->routes = []; 
        require(APPLICATION_ROOT."routes/router.php");
        foreach($this->routes as $route){
            if($route->method == Request::getMethod() && $route->url == self::url()){ 
                $this->route = $route; 
            break; 
            }else{
                http_response_code(404);
                require_once (APPLICATION_ROOT."app/Resource/views/error/404.php");
                die();
            }
        }
    }

    private function loadRouter(){
       return $this->routes; 
    }

    public static function url(){
        if(isset($_GET['url'])){
            return $_GET["url"];
        }else{
            return (isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:"/"); 
        }
    }

    public static function params(){
        $method = Request::getMethod();
        if(isset(self::loadRouter()[$method][self::url()])){
            return self::loadRouter()[$method][self::url()];
        }else{
            http_response_code(404);
            require_once (APPLICATION_ROOT."app/Resource/views/error/404.php");
            die();
        }
    }

    public static function middlewares(){
        return self::params()["middleware"];
    }

    public function route(string $method, string $url, string $controller, $midelware = [])
    {
        $route = new Route; 
        $route->method = $method; 
        $route->url = ($this->prefix == null)? $url: $this->prefix . $url; 
        $route->controller = $controller; 
        $route->middlewares = ($this->middleware == null)? $midelware: array_merge($this->middleware, $midelware); 
    
        array_push($this->routes, $route); 

        return $route; 
    }

    public function get(string $url, string $controller, $midelware = []){
        return $this->route('GET', $url, $controller, $midelware); 
    }

    public function post(string $url, string $controller, $midelware = []){
        return $this->route('POST', $url, $controller, $midelware); 
    }

    public function put(string $url, string $controller, $midelware = []){
        return $this->route('PUT', $url, $controller, $midelware); 
    }

    public function delte(string $url, string $controller, $midelware = []){
        return $this->route('DELETE', $url, $controller, $midelware); 
    }

    public function group($params, $callback){
        $this->prefix = $params['prefix']; 
        $this->middleware = $params['middleware']; 
        $callback(); 
        $this->prefix = null; 
        $this->middleware = null; 
    }
}