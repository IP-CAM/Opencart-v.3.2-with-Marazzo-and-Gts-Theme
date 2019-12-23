<?php
class ControllerGtsMenuAddMenu extends Controller
{
    private $error = array();


    public function index()
    {

        $this->document->addStyle(DIR_STYLE.'gts/style.gts.css');
        $this->document->addScript(DIR_STYLE.'gts/js/style.gts.js');

        $this->load->language('gts/addmenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['action'] = $this->url->link('gts/addmenu/save', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('gts/setting', 'user_token=' . $this->session->data['user_token'], true);

        $data['button_save'] = $this->language->get('save');

        $data['entry_menu'] = $this->language->get('entry_menu');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_template'),
            'href' => $this->url->link('gts/setting', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_add'),
            'href' => $this->url->link('gts/addmenu', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

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


        $data['user_token'] = $this->session->data['user_token'];


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('gts/addmenu', $data));

    }

    public function save()
    {
        $this->load->language('gts/addmenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('gts/sitemenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_gts_menu->addMenu($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            if (isset($this->request->get['filter_article'])) {
                $url .= '&filter_article=' . urlencode(html_entity_decode($this->request->get['filter_article'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('gts/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }


        $this->index();

    }


    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'gts/addmenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 164)) {
            $this->error['error_name_menu'] = $this->language->get('error_name');
        }

        return !$this->error;
    }


}