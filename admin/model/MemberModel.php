<?php
class MemberModel extends Model {
    public function get_member_list() {
        $query = $this->db->query("SELECT * FROM " . TABLE_MEMBER );

        return $query;
    }

    public function getPage($page_code){
        if(!empty($page_code)){
            $query = $this->db->query("SELECT pid, code FROM " . TABLE_PAGE);

            foreach ($query->rows as $page) {
                if($page_code == $page['code']){
                    return $page['pid'];
                }
            }
        }

        return PAGE_ERROR;
    }

    public function getPageInfo($id) {
        $query = $this->db->query("SELECT * FROM " . TABLE_PAGE . " WHERE id=`$id`");

        return $query->row;
    }
}