<?php
class EventModel extends Model {
    public function getEvent() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "event");

        return $query->rows;
    }
}