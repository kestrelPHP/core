<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/16/2016
 * Time: 11:59 PM
 */
class TemplateController extends Controller
{

    public function detail(){
        $response = new Response();
        $response->addHeader('Content-Type', 'text/html; charset=utf-8');
        $response->setOutput($this->load->view('side_bar.html'));
        $response->output();
    }
}