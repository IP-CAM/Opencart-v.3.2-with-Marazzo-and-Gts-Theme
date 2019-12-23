<?php

class ModelGtsMenuMenu extends Model
{
    public function getMenu($id_menu)
    {
    if ($this->customer->isLogged()) {
        $customer_group_id = $this->customer->getGroupId();
    } else {
        $customer_group_id = $this->config->get('config_customer_group_id');
    }


        $cache = 'menu.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . md5(($id_menu));

        $menu = $this->cache->get($cache);

        if (!$menu) {
            $menu = $this->db->query("SELECT * FROM " . DB_PREFIX . "link_menu l 
                    LEFT JOIN " . DB_PREFIX . "link_menu_description lmd
                    ON lmd.link_menu_id = l.link_id 
                    WHERE l.id_menu = '" . (int)$id_menu . "' 
                    AND l.status='1' 
                    AND lmd.language_id='".(int)$this->config->get('config_language_id')."'
                    GROUP BY l.link_id  
                    ORDER BY l.sort_order  ASC");


            $cat = array();

            foreach ($menu->rows as $rows){
                $cat[$rows['link_id']] = $rows;
            }

            $menu = $this->getTree($cat);

            $this->cache->set($cache, $menu);
        }
        return $menu;
    }


    /**
     * Функция построения дерева из массива
     */
    protected function getTree($data)
    {
        $tree = array();
        foreach ($data as $id => &$node) {
            //Если нет вложений


            if (@!$node['parent_id']) {
                $tree[$id] = &$node;
            } else {
                //Если есть потомки то перебераем массив
                $data[$node['parent_id']]['children'][$id] = &$node;
            }
        }

        return $tree;

    }

}