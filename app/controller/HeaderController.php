<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 3:59 PM
 */
class HeaderController extends Controller
{

    public function index(){
        $routes = $this->url;
        $language = $this->language;

        $data['link_current']       = $routes->getCurrentRoute();
        $data['link_home_vn']       = $routes->getRoute(PAGE_HOME, "vn");
        $data['link_home_en']       = $routes->getRoute(PAGE_HOME, "en");
        $data['link_service']       = $routes->getRoute(PAGE_SERVICE, $language->get('current'));
        $data['link_blog']          = $routes->getRoute(PAGE_BLOG, $language->get('current'));
        $data['link_about']         = $routes->getRoute(PAGE_ABOUT, $language->get('current'));
        $data['link_home']          = $routes->getRoute(PAGE_HOME, $language->get('current'));
        $data['link_faq']           = $routes->getRoute(PAGE_FAQ, $language->get('current'));
        $data['link_portfolio']     = $routes->getRoute(PAGE_PORTFOLIO, $language->get('current'));
        $data['link_contact']       = $routes->getRoute(PAGE_CONTACT, $language->get('current'));

        $data['lang_home_vn']       = $language->get('lang_home_vn');
        $data['lang_home_en']       = $language->get('lang_home_en');
        $data['lang_home']          = $language->get('lang_home');
        $data['lang_blog']          = $language->get('lang_blog');
        $data['lang_about']         = $language->get('lang_about');
        $data['lang_service']       = $language->get('lang_service');
        $data['lang_faq']           = $language->get('lang_faq');
        $data['lang_contact']       = $language->get('lang_contact');
        $data['lang_portfolio']     = $language->get('lang_portfolio');

        return $this->load->view('header/header.tpl', $data);
    }
}