<?php
class Module {
	private $file;
	private $class;
	private $method;
	private $args = array();

    public function __construct($route, $args = array()) {
        if (!is_bool(strpos($route, "@"))){
            $this->method = substr($route, strpos($route, "@")+strlen("@"));
			$route =  substr($route, 0, strpos($route, "@"));
        }

		$parts = explode('/', str_replace('../', '', (string)$route));
		$class = preg_replace('/[^a-zA-Z0-9]/', '', ucfirst(end($parts)))  . 'Controller';

		// Break apart the route
		while ($parts) {
			$file = DIR_APP . 'controller/' . implode('/', $parts) . '/' . $class . ".php";

			if (is_file($file)) {
				$this->file = $file;

				$this->class = $class;
				break;
			} else {
				array_push($args, array_pop($parts));
			}
		}

		if(empty($this->method)){
            $this->method = "index";
        }

		$this->args = $args;
	}

	public function execute($registry) {
		// Stop any magical methods being called
		if (substr($this->method, 0, 2) == '__') {
			return false;
		}

		if (is_file($this->file)) {
			include_once($this->file);

			$class = $this->class;

			$controller = new $class($registry);

			if (is_callable(array($controller, $this->method))) {
				return call_user_func(array($controller, $this->method), $this->args);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
