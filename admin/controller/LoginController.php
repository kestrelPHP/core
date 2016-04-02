<?php
class LoginController extends Controller {
	public function index() {

        //$user = $this->request->get['username'];
        $json['data'] = array(
			'user' => $this->request,
//			'post' => $this->request->get['email'],
//			'ee' => $this->request->get['email'],
		);
		//print_r($request->server['REQUEST_METHOD']);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
