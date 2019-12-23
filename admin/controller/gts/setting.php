<?php
class ControllerGtsSetting extends Controller {
    private $error = array();

    public function index()
    {
        $this->load->language('gts/setting');

        $this->document->addStyle(DIR_STYLE.'gts/style.gts.css');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->getList();
    }

    public function getList()
    {

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_template'),
            'href' => $this->url->link('gts/setting', 'user_token=' . $this->session->data['user_token'], true)
        );
        /**
         * Cсылки на разделы
         */
        $data['menu_link']                   = $this->url->link('gtsmenu/sitemenu', 'user_token=' . $this->session->data['user_token'], true);
        $data['menu_link_name']              = $this->language->get('menu_link_name');


        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['user_token'] = $this->session->data['user_token'];


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['content'] = '';

        $this->response->setOutput($this->load->view('gts/setting', $data));
    }

}