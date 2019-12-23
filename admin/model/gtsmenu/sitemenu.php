<?php
class ModelGtsmenuSitemenu extends Model {
    public function addMenu($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "menu SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "' , date_added = NOW()");

        $this->cache->delete('menu');

        return $this->db->getLastId();

    }

    public function addLink($data)
    {
        if(empty($data['window'])) $data['window'] = 0;
        if(empty($data['parent_id'])) $data['parent_id'] = 0;

        $this->db->query("INSERT INTO " . DB_PREFIX . "link_menu SET sort_order = '" . (int)$data['sort_order']. "', id_menu = '" . (int)$data['id_menu'] . "', parent_id = '" . (int)$data['parent_id'] . "', status = '" . (int)$data['status'] . "', window = '" . (int)$data['window'] . "', date_added = NOW()");
        $link_id = $this->db->getLastId();
//, status = '" . (int)$data['status'] . "'

        foreach ($data['link_menu_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "link_menu_description SET link_menu_id = '" . (int)$link_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', href = '" . $this->db->escape($value['href']) . "'");
        }

        $this->cache->delete('menu');

        return $link_id;

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
        $this->db->query("UPDATE " . DB_PREFIX . "menu SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE id_menu = '" . (int)$id_menu . "'");

        $this->cache->delete('menu');
    }

    public function editLink($link_id, $id_menu, $data)
    {

        if(empty($data['window'])) $data['window'] = 0;
        if(empty($data['parent_id'])) $data['parent_id'] = 0;
        if($data['status'] == '0') $this->linksHidden($link_id,$id_menu, $data['status']);

        $this->db->query("UPDATE " . DB_PREFIX . "link_menu SET sort_order = '" . (int)$data['sort_order'] . "', window = '" . (int)$data['window'] . "', parent_id = '" . (int)$data['parent_id'] . "', id_menu = '" . (int)$data['id_menu'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE id_menu = '" . (int)$id_menu . "' AND link_id = '" . (int)$link_id . "'");

        foreach ($data['link_menu_description'] as $language_id => $value) {
            $this->db->query("UPDATE " . DB_PREFIX . "link_menu_description SET  language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', href = '" . $this->db->escape($value['href']) . "' WHERE link_menu_id = '" . (int)$link_id . "' AND language_id = '" . (int)$language_id . "'");
        }

        $this->cache->delete('menu');
    }


    public function getLinksCategories($data = array(), $parent_id = 0) {
        $sql = "SELECT cp.category_id AS category_id, cd.name AS name, c.parent_id, c.sort_order, c.noindex FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c ON (cp.category_id = c.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (cp.path_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.parent_id = '" . (int)$parent_id . "'";

        /*  if (!empty($data['filter_name'])) {
              $sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
          }*/

        $sql .= " GROUP BY cp.category_id";

        $sort_data = array(
            'name',
            'sort_order',
            'noindex'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getLinksBlogs($data = array(), $parent_id = 0) {

        $sql = "SELECT cp.blog_category_id AS category_id, cd1.name  AS name, c1.parent_id, c1.sort_order, c1.noindex FROM " . DB_PREFIX . "blog_category_path cp LEFT JOIN " . DB_PREFIX . "blog_category c1 ON (cp.blog_category_id = c1.blog_category_id) LEFT JOIN " . DB_PREFIX . "blog_category_description cd1 ON (cp.path_id = cd1.blog_category_id)  WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND c1.parent_id = '" . (int)$parent_id . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY cp.blog_category_id";

        $sort_data = array(
            'name',
            'sort_order',
            'noindex'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
//debug($query->rows,1);
        return $query->rows;
    }

    public function getLinksCategoriesCountInner($category_id){
        $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "category WHERE parent_id = '". (int)$category_id . "'");
        return (int)$query->row["COUNT(*)"];
    }

    public function getLinksBlogsCountInner($category_id){
        $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "blog_category WHERE parent_id = '". (int)$category_id . "'");
        return (int)$query->row["COUNT(*)"];
    }

    public function getLinksProductCountInner($category_id){
        $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "product_to_category WHERE category_id = '". (int)$category_id . "'");
        return (int)$query->row["COUNT(*)"];
    }

    public function getLinksArticleCountInner($category_id){
        $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "article_to_blog_category WHERE blog_category_id = '". (int)$category_id . "'");
        return (int)$query->row["COUNT(*)"];
    }

    protected function linksHidden($id, $id_menu , $status)
    {

        $results = $this->db->query("SELECT  link_id, parent_id  FROM " . DB_PREFIX . "link_menu WHERE id_menu = '" . (int)$id_menu . "' AND parent_id = '" . (int)$id . "' ");

        foreach ($results->rows as $result){
            $this->linkHidden($result['link_id'],$id_menu , $status, $result);
        }

        $this->cache->delete('menu');
    }

    protected function linkHidden($id, $id_menu , $status, $result)
    {
//debug("UPDATE " . DB_PREFIX . "link_menu SET status = '" . (int)$status . "', date_modified = NOW() WHERE link_id = '" . (int)$id . "' ");
        $this->db->query("UPDATE " . DB_PREFIX . "link_menu SET status = '" . (int)$status . "', date_modified = NOW() WHERE link_id = '" . (int)$id . "' ");

        if($result['parent_id'] != '0'){

            $this->linksHidden($id,$id_menu, $status);
        }
    }

    public function getAlllinks()
    {
        $menu = $this->db->query("SELECT  * 
                  FROM " . DB_PREFIX . "link_menu l
                  LEFT JOIN " . DB_PREFIX . "link_menu_description lmd
                  ON lmd.link_menu_id = l.link_id
                  WHERE  lmd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                  ORDER BY l.id_menu DESC, l.status DESC, l.sort_order ASC" );

        return $menu->rows;
    }

    public function getlinks($id_menu)
    {
        $menu = $this->db->query("SELECT  * 
                  FROM " . DB_PREFIX . "link_menu l
                  LEFT JOIN " . DB_PREFIX . "link_menu_description lmd
                  ON lmd.link_menu_id = l.link_id
                  WHERE l.id_menu='" . (int)$id_menu . "' AND lmd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY l.id_menu DESC, l.status DESC, l.sort_order ASC");

        return $menu->rows;
    }

    public function getlink($link_id)
    {
        $menu = $this->db->query("SELECT  * FROM " . DB_PREFIX . "link_menu l
                  LEFT JOIN " . DB_PREFIX . "link_menu_description lmd
                  ON lmd.link_menu_id = l.link_id
                  WHERE link_id='" . (int)$link_id . "'");

        return $menu->row;
    }


    public function getLinkMenuDescriptions($link_id) {
        $link_menu_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "link_menu_description WHERE link_menu_id = '" . (int)$link_id . "'");

        foreach ($query->rows as $result) {
            $link_menu_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'href'             => $result['href']
            );
        }

        return $link_menu_description_data;
    }

    public function deleteMenu($id_menu)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "menu WHERE id_menu = '" . (int)$id_menu . "'");

        $this->cache->delete('menu');
    }

    public function deleteLink($link_id)
    {
        $parent_id = $this->db->query("SELECT  COUNT(link_id) FROM " . DB_PREFIX . "link_menu WHERE parent_id=" . (int)$link_id ." " );

        if((int)$parent_id->row['COUNT(link_id)']>0){
            $this->db->query("DELETE FROM " . DB_PREFIX . "link_menu WHERE parent_id = '" . (int)$link_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "link_menu WHERE link_id = '" . (int)$link_id . "'");

        /* if ($this->customer->isLogged()) {
             $customer_group_id = $this->customer->getGroupId();
         } else {
             $customer_group_id = $this->config->get('config_customer_group_id');
         }


         $cache = 'menu.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . md5(($id_menu));*/


        $this->cache->delete('menu');
    }

}