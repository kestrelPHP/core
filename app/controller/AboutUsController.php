<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/17/2016
 * Time: 12:31 AM
 */
class AboutUsController extends Controller
{
    public function index(){
        $this->document->setTitle('Home | Sximone');
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        $this->document->addStyle(DIR_STATIC . "font-awesome/css/font-awesome.min.css");
        $this->document->addStyle(DIR_STATIC . "js/fancybox/source/jquery.fancybox.css");
        $this->document->addStyle(DIR_STATIC . "js/fancybox/source/helpers/jquery.fancybox-thumbs.css");
        $this->document->addStyle(DIR_STATIC . "js/plugins/select2/select2.css");
        $this->document->addStyle(DIR_STATIC . "css/sximone.css");
        $this->document->addStyle(DIR_STATIC . "css/animate.css");

        $this->document->addScript(DIR_STATIC . "js/fancybox/source/jquery.fancybox.js");
        $this->document->addScript(DIR_STATIC . "js/fancybox/source/helpers/jquery.fancybox-thumbs.js");
        $this->document->addScript(DIR_STATIC . "js/fancybox/source/jquery.fancybox.js");
        $this->document->addScript(DIR_STATIC . "js/plugins/prettify.js");
        $this->document->addScript(DIR_STATIC . "js/plugins/parsley.js");
        $this->document->addScript(DIR_STATIC . "js/plugins/parsley.js");
        $this->document->addScript(DIR_STATIC . "js/sximone.js");

        $data = $this->document->getData();

        $data['content_header'] = $this->load->controller('Header');
        $data['content_footer'] = $this->load->controller('Footer');
        $dataNews = $this->load->controller("News@view");
        $dataBlog = $this->load->controller("Blog@view");
        $data['content_main'][] = $dataNews;
        $data['content_main'][] = $dataBlog;

        $this->response->setOutput($this->load->view('master.page.tpl', $data));
    }
}