<?php

class Routes
{
    private $language;

    private $data = array();

    public function __construct($routes, $languages) {
        //$this->language = $language;
        $this->data = $this->build_routes($routes, $languages);
    }


    public function build_routes($routes, $languages) {
        $data = array();
        foreach ($languages as $language) {
            foreach ($routes as $route) {
                $page = new stdClass();
                $page->code = get_lang_text($route['code'], $language['code']);
                if(empty($page->code)) continue;
                $page->route = $route['id'];
                $page->lang = $language['code'];

                $data[$page->code] = $page;
            }
        }

        return $data;
    }

    public function get() {

        return $this->data;
    }

    public function getCurrentRoute($route) {

        if(in_array($route, array_keys($this->data))){

            return $this->data[$route];
        }

        return null;
    }
}