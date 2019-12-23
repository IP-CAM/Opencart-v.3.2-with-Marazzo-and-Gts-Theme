<?php
class ControllerGtsmenuSitemenu extends Controller {
    private $error = array();

    public function index()
    {

        $this->load->language('gtsmenu/sitemenu');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addStyle(DIR_STYLE.'gts/style.gts.css');

        $this->getList();
    }

    public function getList()
    {

        /**
         * Ccылки
         */
        $data['sitemenu']               = $this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true);
        $data['add_menu']               = $this->url->link('gtsmenu/sitemenu/add', 'user_token=' . $this->session->data['user_token'], true);
        $data['links']                  = $this->url->link('gtsmenu/links', 'user_token=' . $this->session->data['user_token'], true);
        $data['delete']                 = $this->url->link('gtsmenu/sitemenu/delete', 'user_token=' . $this->session->data['user_token'], true);

        /**
         * Тексты
         */
        $data['button_add']             = $this->language->get('button_add');
        $data['name_link_menu']         = $this->language->get('name_link_menu');
        $data['text_name_title_link']   = $this->language->get('text_name_title_link');
        $data['name_page_menu']         = $this->language->get('name_page_menu');

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

        /**
         * Ошибки
         */
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('gtsmenu/sitemenu');

        /**
         * Получаем меню
         */
        $menu_list = $this->model_gtsmenu_sitemenu->getAllMenu();
        /**
         * Формируем список
         */
        if($menu_list){
            foreach ( $menu_list as $menu){
                $data['menu_list'][] = [
                    'id_menu'       => $menu['id_menu'],
                    'name'          => $menu['name'],
                    'status'        => $menu['status'],
                    'date_added'    => $menu['date_added'],
                    'date_modified' => $menu['date_modified'],
                    'url'           => $this->url->link('gtsmenu/sitemenu/edit', 'user_token=' . $this->session->data['user_token'].'&id_menu='.$menu['id_menu'], true)
                ];
            }
        }

        /**
         * подключаем другие елементы админки
         */
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        /**
         * Выводим
         */
        $this->response->setOutput($this->load->view('gtsmenu/sitemenu', $data));
    }

    public function add()
    {

        $this->load->language('gtsmenu/addmenu');

        $this->document->setTitle($this->language->get('heading_title'));

        /**
         * Првоеряем и записіваем меню
         */
        $this->load->model('gtsmenu/sitemenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_gtsmenu_sitemenu->addMenu($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            /**
             * Если все ок перенаправляем на страницу меню
             */
            $this->response->redirect($this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true));
        }

        /**
         * Cсылки
         */
        $data['action']         = $this->url->link('gtsmenu/sitemenu/add', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel']         = $this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true);

        /**
         * Название ссылок
         */
        $data['button_save']    = $this->language->get('save');
        $data['entry_menu']     = $this->language->get('entry_menu');

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

        /**
         * Ошибки
         */
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['user_token'] = $this->session->data['user_token'];

        /**
         * подключаем другие елементы админки
         */
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        /**
         * Ошибки
         */
        if(isset($this->error['warning'])){
            $data['warning'] = $this->error['warning'];
        }
        if(isset($this->error['error_name_menu'])){
            $data['error_name_menu'] = $this->error['error_name_menu'];
        }

        /**
         * Выводим шаблон
         */
        $this->response->setOutput($this->load->view('gtsmenu/addmenu', $data));
    }

    /**
     * Редактирование меню
     */
    public function edit()
    {
        $this->load->language('gtsmenu/sitemenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('gtsmenu/sitemenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_gtsmenu_sitemenu->editMenu($this->request->get['id_menu'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }


    protected function getForm()
    {
        $this->load->language('gtsmenu/sitemenu');

        /**
         * првоеряем создангие нового или редактирование
         */
        $data['text_form'] = !isset($this->request->get['id_menu']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $this->document->setTitle($data['text_form']);


        /**
         * Сыслки
         */
        $data['cancel'] = $this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'] , true);


        /**
         * Ошибки
         */
        if (isset($this->error['warning'])) {
            $data['warning'] = $this->error['warning'];
        } else {
            $data['warning'] = '';
        }
        if (isset($this->error['error_name_menu'])) {
            $data['error_name_menu'] = $this->error['error_name_menu'];
        } else {
            $data['error_name_menu'] = '';
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
            'text' => $data['text_form'],
            'href' => $this->url->link('gtsmenu/sitemenu/add', 'user_token=' . $this->session->data['user_token'], true)
        );

        /**
         * Подключем нужный екшн
         */
        if (!isset($this->request->get['id_menu'])) {
            $data['action'] = $this->url->link('gtsmenu/sitemenu/add', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('gtsmenu/sitemenu/edit', 'user_token=' . $this->session->data['user_token'] . '&id_menu=' . $this->request->get['id_menu'], true);
        }

        $this->load->model('gtsmenu/sitemenu');

        if (isset($this->request->get['id_menu']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $menu_info = $this->model_gtsmenu_sitemenu->getMenu($this->request->get['id_menu']);
        }


        $data['user_token'] = $this->session->data['user_token'];


        if (isset($this->request->post['menu_id'])) {
            $data['id_menu'] = $this->request->post['id_menu'];
        } elseif (!empty($menu_info)) {
            $data['id_menu'] = $menu_info['id_menu'];
        } else {
            $data['id_menu'] = '';
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($menu_info)) {
            $data['name'] = $menu_info['name'];
        } else {
            $data['name'] = '';
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

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('gtsmenu/addmenu', $data));
    }



    public function delete()
    {
        $this->load->language('gtsmenu/sitemenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('gtsmenu/sitemenu');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $menu_id) {
                $this->model_gtsmenu_sitemenu->deleteMenu($menu_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getList();
    }


    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'gtsmenu/sitemenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 164)) {
            $this->error['error_name_menu'] = $this->language->get('error_name');
        }
        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'gtsmenu/sitemenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}