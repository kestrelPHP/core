<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 4:04 PM
 */
class NewsModel extends Model
{
    public function getList(){
        $query = "SELECT * FROM " . TABLE_NEWS . " WHERE parent_id=2 LIMIT 0, 10";

        $result = $this->db->query($query);

        return $result->rows;
    }

    public function get($id){
        $query = "SELECT * FROM " . TABLE_NEWS . " WHERE parent_id=2 AND id='".$id."'";

        $result = $this->db->query($query);

        return $result->row;
    }
}