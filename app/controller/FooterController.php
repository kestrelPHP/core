<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 4:00 PM
 */
class FooterController extends Controller
{

    public function index(){

        return $this->load->view('footer/footer.tpl');
    }
}