<?php
class Config {
	public $data = array();

	public function get($key) {
		return $this->has($key) ? $this->data[$key] : null;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function has($key) {
		return isset($this->data[$key]);
	}

	public function load($filename) {
		$file = DIR_CONFIG . $filename . '.php';

		if (!file_exists($file)) {
            $file = DIR_APP_CONFIG . $filename . '.php';
		}

        if (file_exists($file)) {
            $_ = array();

            require($file);

            $this->data = array_merge($this->data, $_);
        } else {
            trigger_error('Error: Could not load config ' . $filename . '!');
            exit();
        }
	}
}