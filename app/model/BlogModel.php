<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 7:47 PM
 */
class BlogModel extends Model
{
    public function getList(){
        $query = "SELECT * FROM " . TABLE_NEWS . " WHERE parent_id=1 LIMIT 0, 10";

        $result = $this->db->query($query);

        return $result->rows;
    }

    public function get($id){
        $query = "SELECT * FROM " . TABLE_NEWS . " WHERE parent_id=1 AND id='".$id."'";

        $result = $this->db->query($query);

        return $result->row;
    }
}