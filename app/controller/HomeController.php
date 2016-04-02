<?php
use tlslib\Tls;
/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 3:08 PM
 */

class HomeController extends Controller
{
    public function index() {
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

        $data['content_main'][] = $this->load->controller("News@view");
        $data['content_main'][] = $this->load->controller("Blog@view");

        //$guideTable = Tls::get('GuideTable');print_r($guideTable);
        $this->response->setOutput($this->load->view('master.page.tpl', $data));
    }
}