<?php

class Routess
{
    private $language;

    private $config;

    private $data = array();

    public function __construct($registry) {
        //$this->language = $registry->get('language');
        $this->config = $registry->get('config');
    }


    public function get($key, $lang="") {
        if(empty($lang)){
            $lang = $this->config->get('config_language_id');
        }
        return ($this->has($key, $lang) ? $this->data[$lang][$key] : "javascript:void(0)");
    }

    public function load($value) {
        $this->data = $value;
    }

    public function has($key, $lang) {
        return isset($this->data[$lang][$key]);
    }
}