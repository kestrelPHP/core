<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 4:05 PM
 */
class NewsController extends Controller
{

    public function index(){
        $id = $this->url->getId();echo $id."---";
        if($id){
            return $this->detail($id);
        } else {
            return $this->view();
        }
    }

    public function detail($id){
        $new = $this->load->model("News")->get($id);

        $tpl = $this->load->template('news/detail.html');
        $tpl->prepare();

        return $tpl->GetOutputContent();
    }

    public function view(){
        $url = $this->url;
        $lang = $this->language;
        $news = $this->load->model('News')->getList();

        $tpl = $this->load->template('news/list.html');
        $tpl->prepare();
        foreach ($news as $new) {
            $tpl->newBlock('NEWS');
            $tpl->assign('title', $new['title']);
            //$tpl->assign('description', $new['description']);
            $tpl->assign('link', $url->link($url->getRoute(PAGE_SERVICE, $lang->get("current")) , array($new['title'], $new['id'])));
        }

        return $tpl->getOutputContent();
    }
}