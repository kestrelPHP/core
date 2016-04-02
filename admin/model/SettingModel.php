<?php
class SettingModel extends Model {
    public function getSetting() {
        $query = $this->db->query("SELECT * FROM " . TABLE_SETTING);

        return $query->rows;
    }
}