<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 3:09 PM
 */
class ErrorController extends Controller
{
    public function not_found() {
        $url = $this->url;
        $language = $this->language;

        $this->load->language('error/not_found');

        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addStyle("app/view/error/css/bootstrap.min.css");
        $this->document->addStyle("app/view/error/css/bootstrap-responsive.min.css");
        $this->document->addStyle("app/view/error/css/error.css");
        $this->document->addScript("app/view/error/js/plax.js");

        $data = $this->document->getData();

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('home')
        );

        $data['img_location'] = 'app/view/error/';
        $data['lang_text_error'] = $language->get('text_error');
        $data['lang_home'] = $language->get('button_home');
        $data['link_home'] = $url->getRoute(PAGE_HOME, $language->get('current'));

        $data['button_or_tweet'] = $language->get('button_or_tweet');

        $this->response->setOutput($this->load->view('error/not_found.tpl', $data));
    }
}