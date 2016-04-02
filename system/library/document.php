<?php
class Document {
	private $domain = "";
	private $language = array("code"=>"", "direction"=>"");
	private $title = "";
	private $description = "";
	private $keywords = "";
	private $links = array();
	private $styles = array();
	private $scripts = array();

    public function setLanguage($language) {
        $array['code'] = $language['code'];
        $array['direction'] = $language['direction'];
        $this->language = $array;
    }

    public function getLanguge() {
        return $this->language;
    }

    public function setBase($domain) {
        $this->domain = $domain;
    }

    public function getBase() {
        return $this->domain;
    }

    public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function addLink($href, $rel) {
		$this->links[$href] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}

	public function getLinks() {
		return $this->links;
	}

	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[$href] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	public function getStyles() {
		return $this->styles;
	}

	public function addScript($href, $postion = 'header') {
		$this->scripts[$postion][$href] = $href;
	}

	public function getScripts($postion = 'header') {
		if (isset($this->scripts[$postion])) {
			return $this->scripts[$postion];
		} else {
			return array();
		}
	}

	public function getData() {
		$data['direction']      = $this->language['direction'];
		$data['lang']           = $this->language['code'];
		$data['base']           = $this->domain;
		$data['title']          = $this->title;
		$data['description']    = $this->description;
		$data['keywords']      = $this->keywords;
		$data['links']          = $this->getLinks();
		$data['styles']         = $this->getStyles();
		$data['scripts']        = $this->getScripts();

        return $data;
	}
}
