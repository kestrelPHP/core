<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/17/2016
 * Time: 12:51 AM
 */
class SideBarController extends Controller
{
    public function index() {
        try {
            $sidebar = new stdClass();

            $pages = array();
            $loader = $this->load;
            $langList = $loader->model('Language', 'fetchAll');
            $pageList = $loader->model('Page', 'fetchAll');

            $curr_lang = new stdClass();
            $curr_lang->id = 2;
            $curr_lang->code = "vn";
            $curr_lang->name = "vietnamese";

            $lang_items = array();
            foreach ($langList as $lang) {
                $item = new stdClass();
                $item->id = $lang['language_id'];
                $item->name = $lang['name'];
                $item->code = $lang['code'];
                $lang_items[] = $item;
            }

            $menu_items = array();

            $item = new stdClass();
            $item->link = "website/website-development/setting";
            $item->title = "Setting";
            $menu_items[] = $item;

            foreach ($pageList as $page) {
                $item = new stdClass();
                $item->link = "website/website-development/" . strtolower($page['controller']);
                $item->title = get_lang_text($page['title'], $curr_lang->code);
                $item->desc = get_lang_text($page['description'], $curr_lang->code);
                $item->keyword = get_lang_text($page['keywords'], $curr_lang->code);
                $menu_items[] = $item;
            }

            $sidebar->menu_items = $menu_items;
            $sidebar->lang_items = $lang_items;
            $sidebar->current_lang = $curr_lang;

            return $sidebar;
        } catch ( Exception $e ) {
            throw $e;
        }
    }
}