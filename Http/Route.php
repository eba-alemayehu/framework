<?php 
namespace Application\Http;
class Route
{   
    public $method; 
    public $url; 
    public $controller; 
    public $middlewares = []; 
    public static $param_pattern = "/{[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*}/"; 
    
    public function middelware($middleware){
        if(is_array($middleware)){
            array_merge($this->middlewares, $middleware); 
        }else{
            array_push($this->middlewares, $middleware); 
        }

        return $this; 
    }

    public function controller(){
        return explode("@",$this->controller);
    }
    public function setUrl($url){
        if(empty($url)){
            $this->url = "/"; 
        }else{
            $this->url = ($url[0] == "/")? $url: "/".$url;
        }     
    }
    public function controllerClass(){
        return "\\App\\Http\\Controllers\\".$this->controller()[0]; 
    }
    public function controllerMethod(){
        return $this->controller()[1]; 
    }
    public function match($url){
        $url_pattern = preg_replace(self::$param_pattern, "([^/]+)", $this->url); 
		$url_pattern = "/^".str_replace("/", "\/", $url_pattern)."$/"; 
   
        return preg_match($url_pattern, $url); 
	}
	
	public function params($url = null){
		preg_match_all(self::$param_pattern, $this->url, $args,PREG_OFFSET_CAPTURE); 
		$url_resources = explode("/", $this->url); 
		$args = $args[0];
		$params = []; 
		foreach($args as $arg){
			$param  = ["name" => $arg[0]]; 
			$param["index"] = array_search($arg[0], $url_resources); 
			if($url){
				$_provied_url_resources = explode("/", $url); 
				$param["value"] = $_provied_url_resources[$param["index"]]; 
			}
			array_push($params, (object)$param); 
		}

		
		return $params; 
	}
}
