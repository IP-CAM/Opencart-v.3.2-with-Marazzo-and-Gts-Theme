<?php
class ModelGtsMenu extends Model {
    public function addMenu($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "menu SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "' , date_added = NOW()");

        return $this->db->getLastId();

    }

    public function getAllMenu()
    {
        $menu = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu");

        return $menu->rows;
    }

    public function getMenu($id_menu)
    {
        $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "menu  WHERE id_menu = '" . (int)$id_menu . "'");

        return $query->row;
    }



    public function editMenu($id_menu, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "menu SET name = '" . $this->db->escape($data['name']) . "',  status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE id_menu = '" . (int)$id_menu . "'");

        $this->cache->delete('article');
    }


    public function deleteMenu($id_menu)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "menu WHERE id_menu = '" . (int)$id_menu . "'");

        $this->cache->delete('menu');
    }
}