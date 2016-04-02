<?php
class Url {
	private $domain;
	private $ssl;
    private $route;
    private $current;
    private $args;
    private $id = 0;
	private $rewrite = array();

	public function __construct($request, $domain, $ssl = '') {
        $this->request = $request;
		$this->domain = $domain;
		$this->ssl = $ssl;
        $this->setParam();
	}

	public function addRewrite($rewrite) {
		//$this->rewrite[] = $rewrite;
        foreach ($rewrite as $item) {
            $this->rewrite[$item['id']][$item['lang']] = $item['code'];
        }
	}

	public function link($route, $args = '', $secure = false) {
		if (!$secure) {
			$url = $this->domain;
		} else {
			$url = $this->ssl;
		}

		$url .= rtrim($route, "/") . "/";

//		if ($args) {
//			if (is_array($args)) {
//				$url .= '&amp;' . http_build_query($args);
//			} else {
//				$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
//			}
//		}

		if ($args) {
            $url .= $this->http_build_query($args);
		}

		foreach ($this->rewrite as $rewrite) {
			//$url = $rewrite->rewrite($url);
		}

		return $url;
	}

    private function setParam() {
        $uri = isset($this->request->server['REDIRECT_URL']) ? $this->request->server['REDIRECT_URL'] : $this->request->server['REQUEST_URI'];
        $request = trim($uri, "/");
        $parts = explode('/', str_replace('../', '', $request));

        // Break apart the route
        while ($parts) {
            if( $parts[0] == trim(APP_NAME, "/") || strtolower($parts[0]) == "admin" ) unset($parts[0]);

            $this->args[] = array_pop($parts);
        }

        $current = array_pop($this->args);

        $this->route = $this->current = $current;

        if(count($this->args)){
            $param = array_values($this->args)[0];
            $ids = explode('-', str_replace('../', '', $param));
            $id = 0;

            if($ids[count($ids)-1] > 0) {
                $id = $ids[count($ids)-1];
            } else if($ids[0] > 0) {
                $id = $ids[0];
            }

            $this->id = $id;
        }
    }

    public function getRoute($page_id, $lang_code="vn") {

        return isset($this->rewrite[$page_id][$lang_code]) ? $this->rewrite[$page_id][$lang_code] : "";
    }

    public function getCurrentRoute(){

        return $this->route=="" ? "dashboard" : strtolower($this->route);
    }
    public function getParam(){

        return $this->args;
    }
    public function getId(){
        if($this->route == $this->current)
            return $this->id;

        return 0;
    }

	private function http_build_query($args){
        $str = implode(' ', $args);
        //vn
//        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
//        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
//        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
//        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
//        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
//        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
//        $str = preg_replace("/(đ)/", 'd', $str);
//        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
//        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
//        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
//        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
//        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
//        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
//        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ|ä|å)/", 'a', $str);
        $str = preg_replace("/(æ)/", 'ae', $str);
        $str = preg_replace("/(ç)/", 'c', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ|ë)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ|î|ï)/", 'i', $str);
        $str = preg_replace("/(đ|ð)/", 'd', $str);
        $str = preg_replace("/(ñ)/", 'n', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ|ö|ő|ø)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ|ü|ű)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ|ÿ)/", 'y', $str);
        $str = preg_replace("/(þ)/", 'th', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ|Ä|Å)/", 'A', $str);
        $str = preg_replace("/(Æ)/", 'AE', $str);
        $str = preg_replace("/(Ç)/", 'C', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ|Ë)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ|Î|Ï)/", 'I', $str);
        $str = preg_replace("/(Ñ)/", 'N', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ|Ö|Ő|Ø)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ|Û|Ü|Ű)/", 'U', $str);
        $str = preg_replace("/(Þ)/", 'TH', $str);
        $str = preg_replace("/(ß)/", 'ss', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace("/\W+/", '-', $str);
        //$str = preg_replace("/(©)/", '(c)', $str);

        return strtolower($str);
	}

	public function callback($route, $args = '', $secure = false) {
		if (!$secure) {
			$url = $this->domain;
		} else {
			$url = $this->ssl;
		}

		$url .= 'index.php?callback=' . $route;

		if ($args) {
			if (is_array($args)) {
				$url .= '&amp;' . http_build_query($args);
			} else {
				$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
			}
		}

		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}

		return $url;
	}
}
