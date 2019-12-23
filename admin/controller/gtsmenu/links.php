<?php

class ControllerGtsmenuLinks extends Controller
{
    private $error = array();


    public function index()
    {
        $this->load->language('gtsmenu/links');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addStyle(DIR_STYLE.'gts/style.gts.css');
        $this->document->addScript(DIR_STYLE.'gts/js/style.gts.js');

        $this->getList();
    }

    /**
     * Вывод списка всех ссылок
     */
    public function getList()
    {
        $language_id = $this->config->get('config_language_id');
        $id_menu = !isset($this->request->get['id_menu']) ? false : $this->request->get['id_menu'];

        if ($id_menu) {
            $url = '&id_menu=' . $id_menu;
        } else {
            $url = '';
        }
        /**
         * Ссылки
         */
        $data['sitemenu']           = $this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true);
        $data['add_menu']           = $this->url->link('gtsmenu/links/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['links']              = $this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['links_js']           = $this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'], true);
        $data['delete']             = $this->url->link('gtsmenu/links/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        /**
         * название ссылок
         */
        $data['button_add'] = $this->language->get('button_add');
        $data['name_link_menu'] = $this->language->get('name_link_menu');
        $data['text_name_title_link'] = $this->language->get('text_name_title_link');
        $data['name_page_menu'] = $this->language->get('name_page_menu');
        $data['entry_cat_all'] = $this->language->get('entry_cat_all');

        if (!empty($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        /**
         * Хлебные крошки
         */
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('name_template'),
            'href' => $this->url->link('gts/setting', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('name_sitemenu'),
            'href' => $this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('name_link_menu'),
            'href' => $this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('gtsmenu/sitemenu');

        if ($id_menu == false) {
            $menu_list = $this->model_gtsmenu_sitemenu->getAlllinks();
        } else {
            $menu_list = $this->model_gtsmenu_sitemenu->getlinks($id_menu);
        }


        $cats = $this->model_gtsmenu_sitemenu->getAllMenu();

        if ($cats) {
            foreach ($cats as $cat) {
                $data['cats'][] = [
                    'id_menu' => $cat['id_menu'],
                    'name' => $cat['name'],
                    'status' => $cat['status'],
                    'date_added' => $cat['date_added'],
                    'date_modified' => $cat['date_modified'],
                    'selected' => $cat['id_menu'] == $id_menu ? 'selected' : '',
                ];
            }
        }

        $data['menu_list'] = $this->getHtmlMenu($menu_list);



        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('gtsmenu/links', $data));

    }


    /**
     * Создаем ссылку
     */
    public function add()
    {
        $this->load->language('gtsmenu/links');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('gtsmenu/sitemenu');


        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {


            /**
             * Заполняем переменные для редактирования
             */
            $id_menu = !isset($this->request->post['id_menu']) ? false : $this->request->post['id_menu'];

            $url = '';

            if ($id_menu) {
                $url .= '&id_menu=' . $id_menu;
            }


            $this->model_gtsmenu_sitemenu->addLink($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    /**
     * редактирование ссылок
     */
    public function edit()
    {
        $this->load->language('gtsmenu/links');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('gtsmenu/sitemenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_gtsmenu_sitemenu->editLink($this->request->get['link_id'], $this->request->get['id_menu'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if ($this->request->post['id_menu']) {
                $url .= '&id_menu=' . $this->request->post['id_menu'];
            }

            $this->response->redirect($this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }


    /**
     * Вывод формы редактирования/создания ссылки
     */
    protected function getForm()
    {
        $this->document->addStyle(DIR_STYLE.'gts/style.gts.css');
        $this->document->addScript(DIR_STYLE.'gts/js/style.gts.js');
        /**
         * Подключаем язіковій файл
         */
        $this->load->language('gtsmenu/links');

        /**
         * Проверяем Редактировать или создать новую ссылку
         */
        $data['text_form'] = (!isset($this->request->get['link_id'])) ? $this->language->get('text_add') : $this->language->get('text_edit');


        $this->document->setTitle($data['text_form']);

        $data['entry_href']     = $this->language->get('entry_href');
        $data['show_sitemap']   = $this->language->get('show_sitemap');


        /**
         * Языки
         */
        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        /**
         * Заполняем переменные для редактирования
         */
        $id_menu = !isset($this->request->get['id_menu']) ? false : $this->request->get['id_menu'];
        $link_id = !isset($this->request->get['link_id']) ? false : $this->request->get['link_id'];


        $url = '';

        if ($id_menu) {
            $url .= '&id_menu=' . $id_menu;
        }

        if ($link_id) {
            $url .= '&link_id=' . $link_id;
        }
        /**
         * ФОрмируем хлебные крошки
         */
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_template'),
            'href' => $this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['text_form'],
            'href' => $this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'].$url, true)
        );

        /**
         * Заполняем екшн для формы, взависимости от того что нужно
         */
        if (isset($this->request->get['id_menu']) && !isset($this->request->get['link_id'])) {
            $data['action'] = $this->url->link('gtsmenu/links/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } elseif (!isset($this->request->get['id_menu']) && !isset($this->request->get['link_id'])) {
            $data['action'] = $this->url->link('gtsmenu/links/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('gtsmenu/links/edit', 'user_token=' . $this->session->data['user_token'] . $url, true);
        }

        $data['cancel']     = $this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'], true);
        $data['sitemap']    = $this->url->link('gtsmenu/sitemap', 'user_token=' . $this->session->data['user_token'], true);



        $this->load->model('gtsmenu/sitemenu');

        if (!empty($this->request->get['link_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $menu_info = $this->model_gtsmenu_sitemenu->getlink($this->request->get['link_id']);
        }

        $cats = $this->model_gtsmenu_sitemenu->getAllMenu();

        if ($cats) {
            foreach ($cats as $cat) {
                $data['cats'][] = [
                    'id_menu' => $cat['id_menu'],
                    'name' => $cat['name'],
                    'status' => $cat['status'],
                    'date_added' => $cat['date_added'],
                    'date_modified' => $cat['date_modified'],
                    'selected' => $cat['id_menu'] == $id_menu ? 'selected' : '',
                ];
            }
        }
        $data['user_token'] = $this->session->data['user_token'];

        /**
         * Заполняем переменные ошибок если они есть
         */

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['error_name'])) {
            $data['error_name'] = $this->error['error_name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['error_href'])) {
            $data['error_href'] = $this->error['error_href'];
        } else {
            $data['error_href'] = '';
        }


        if (isset($this->request->post['id_menu'])) {
            $data['id_menu'] = $this->request->post['id_menu'];
        } elseif (isset($this->request->get['id_menu'])) {
            $data['id_menu'] = $this->request->get['id_menu'];
        } elseif (!empty($menu_info)) {
            $data['id_menu'] = $menu_info['id_menu'];
        } else {
            $data['id_menu'] = FALSE;
        }


        if (isset($this->request->post['link_id'])) {
            $data['link_id'] = $this->request->post['link_id'];
        } elseif (!empty($menu_info)) {
            $data['link_id'] = $menu_info['link_id'];
        } else {
            $data['link_id'] = '';
        }

        if (isset($this->request->post['link_menu_description'])) {
            $data['link_menu_description'] = $this->request->post['link_menu_description'];
        } elseif (!empty($menu_info)) {
            $data['link_menu_description'] = $this->model_gtsmenu_sitemenu->getLinkMenuDescriptions($menu_info['link_id']);
        } else {
            $data['link_menu_description'] = array();
        }

        $language_id = $this->config->get('config_language_id');
        if (isset($data['link_menu_description'][$language_id]['name'])) {
            $data['heading_title'] = $data['link_menu_description'][$language_id]['name'];
        }

        if (isset($this->request->post['date_added'])) {
            $data['date_added'] = $this->request->post['date_added'];
        } elseif (!empty($menu_info)) {
            $data['date_added'] = ($menu_info['date_added'] != '0000-00-00 00:00' ? $menu_info['date_added'] : '');
        } else {
            $data['date_added'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($menu_info)) {
            $data['status'] = $menu_info['status'];
        } else {
            $data['status'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($menu_info)) {
            $data['sort_order'] = $menu_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        if (isset($this->request->post['parent_id'])) {
            $data['parent_id'] = $this->request->post['parent_id'];
        } elseif (isset($this->request->get['parent_id'])) {
            $data['parent_id'] = $this->request->get['parent_id'];
        } elseif (!empty($menu_info)) {
            $data['parent_id'] = $menu_info['parent_id'];
        } else {
            $data['parent_id'] = FALSE;
        }

        if ($data['parent_id']) {
            if(empty($this->request->get['id_menu'])){

                $this->error['warning'] = $this->language->get('error_id_menu');

                $this->response->redirect($this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'], true));
            }
            $allLinks = $this->model_gtsmenu_sitemenu->getlinks($this->request->get['id_menu']);

            if ($allLinks) {
                foreach ($allLinks as $linkParent) {
                    $data['allLinks'][] = [
                        'link_id' => $linkParent['link_id'],
                        'name' => $linkParent['name'],
                        'status' => $linkParent['status'],
                        'selected' => $linkParent['link_id'] == $data['parent_id'] ? 'selected' : '',
                    ];
                }
            }

        }
        $data['text_modal_name']    = $this->language->get('text_modal_name');
        $data['entry_insert_link']  = $this->language->get('entry_insert_link');
        $data['text_mod']           = $this->language->get('text_mod');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('gtsmenu/links_form', $data));
    }

    /**
     * Удаление ссылок
     */
    public function delete()
    {
        $this->load->language('gtsmenu/links');

        $this->document->setTitle($this->language->get('heading_title'));
        
        /**
         * Заполняем переменные для редактирования
         */
        $id_menu = !isset($this->request->get['id_menu']) ? false : $this->request->get['id_menu'];

        $url = '';

        if ($id_menu) {
            $url .= '&id_menu=' . $id_menu;
        }
        /**
         * Подключаем нужный модуль и удалем елементы
         */
        $this->load->model('gtsmenu/sitemenu');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {

            foreach ($this->request->post['selected'] as $link_id) {
                $this->model_gtsmenu_sitemenu->deleteLink($link_id);
            }

           
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->session->data['error'] = $this->language->get('text_error');

        $this->response->redirect($this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

    /**
     * Функция построения дерева из массива
     */
    protected function getTree($data)
    {
        $tree = array();
        foreach ($data as $id => &$node) {
            //Если нет вложений

            if (!$node['parent_id']) {
                $tree[$id] = &$node;
            } else {
                //Если есть потомки то перебераем массив
                $data[$node['parent_id']]['children'][$id] = &$node;
            }
        }

        return $tree;

    }

    protected function getHtmlMenu($data)
    {

        if ($data) {
            foreach ($data as $menu) {
                $menu_l[$menu['link_id']] = [
                    'link_id' => $menu['link_id'],
                    'id_menu' => $menu['id_menu'],
                    'parent_id' => $menu['parent_id'],
                    'name' => $menu['name'],
                    'status' => $menu['status'],
                    'sort_order' => $menu['sort_order'],
                    'date_added' => $menu['date_added'],
                    'date_modified' => $menu['date_modified'],
                    'url' => $this->url->link('gtsmenu/links/edit', 'user_token=' . $this->session->data['user_token'] . '&id_menu=' . $menu['id_menu'] . '&link_id=' . $menu['link_id'], true)
                ];
            }
            $tree = $this->getTree($menu_l);
            return $this->showCat($tree);
        }

        return false;
    }


//Шаблон для вывода меню в виде дерева
    protected function tplMenu($data){

        $id_menu = !isset($this->request->get['id_menu']) ? false : $this->request->get['id_menu'];

        if ($id_menu) {
            $url = '&id_menu=' . $id_menu;
        } else {
            $url = '';
        }

        $data['add_menu'] = $this->url->link('gtsmenu/links/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $menu = '<li class="item '.(isset($data['children']) ? 'parent' : '').'">';

            $menu .= $this->load->view('gtsmenu/html/links', $data);
            if(isset($data['children'])){
                $menu .= '<ul>'. $this->showCat($data['children']) .'</ul>';
            }
        $menu .= '</li>';

        return $menu;
    }

    /**
     * Рекурсивно считываем наш шаблон
     **/
    protected function showCat($data){
        $string = '';
        foreach($data as $item){
            $string .= $this->tplMenu($item);
        }
        return $string;
    }

    protected function validateEnable()
    {
        if (!$this->user->hasPermission('modify', 'gtsmenu/links')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateDisable()
    {
        if (!$this->user->hasPermission('modify', 'gtsmenu/links')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'gtsmenu/links')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'gtsmenu/links')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        foreach ($this->request->post['link_menu_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
                $this->error['error_name'][$language_id] = $this->language->get('error_name');
            }
            if ((utf8_strlen($value['href']) == false) || (utf8_strlen($value['href']) > 255)) {
                $this->error['error_href'][$language_id] = $this->language->get('error_href');
            }

        }
        if (!$this->request->post['id_menu']) {
            $this->error['id_menu'] = $this->language->get('error_id_menu');
        }
        return !$this->error;
    }


}