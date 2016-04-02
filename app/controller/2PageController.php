<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/22/2016
 * Time: 12:21 AM
 */
class PageController extends Controller
{
    public function index(){
        try{
            $json = array(
                'country_id'        => 'country_id',
                'name'              => 'name',
                'iso_code_2'        => 'iso_code_2',
                'iso_code_3'        => 'iso_code_3',
                'address_format'    => 'address_format',
                'postcode_required' => 'postcode_required',
                'status'            => 'status'
            );

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } catch (Exception $e){
            throw $e;
        }
    }
}