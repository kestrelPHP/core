<?php
class AuthModel extends Model {
    public static $user = null;

    public function checkUser($login_name, $password) {
        $password_md5 = md5($password);
        $result = $this->db->query("SELECT * FROM " . TABLE_USER . " where `enable`=1 and `login_name`='".$login_name."' and  `password`='".$password_md5."'");

        if( $result ){
            self::$user = $result->rows;
        }
    }

    public function getAccountinfo($info){
        $user = new stdClass();
        $user->perm = 0;

        if( isset($info['username']) && !empty($info['username']) && !empty($info['password']) ){
            $password_md5 = md5($info['password']);
            $result = $this->db->query("SELECT * FROM " . TABLE_USER . " where `enable`=1 and `login_name`='".$info['username']."' and  `password`='".$password_md5."'");

            if( $result ){
                $user->perm = $result->row['type'];
                $user->username = $result->row['login_name'];
                $user->email = $result->row['email'];
            }
        }

        return $user;
    }

}