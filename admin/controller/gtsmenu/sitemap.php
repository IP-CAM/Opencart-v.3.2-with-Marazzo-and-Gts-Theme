<?php
class ControllerGtsMenuSiteMap extends Controller {
    public function index() {

        $this->load->language('gtsmenu/addmenu');

        $this->load->model('gtsmenu/sitemenu');

        $link = HTTP_CATALOG . 'index.php?route=';

        $data['categories'] = array();

        $categories_1 = $this->model_gtsmenu_sitemenu->getLinksCategories(0,0);

        foreach ($categories_1 as $category_1) {

            $count_iner = $this->model_gtsmenu_sitemenu->getLinksCategoriesCountInner($category_1['category_id']);

            if(!$count_iner){
                $count_ittem = $this->model_gtsmenu_sitemenu->getLinksProductCountInner($category_1['category_id']);
            }

            $count_iner = $count_iner ?: 0;

            if(!empty($count_ittem) && $count_ittem != 0){
                $type = 'ittem';
            }elseif(!empty($count_iner) && $count_iner != 0){
                $type = 'cat';
            }else{
                $type = false;
            }

            $data['categories'][] = array(
                'id'        => $category_1['category_id'],
                'parent_id' => $category_1['parent_id'],
                'name'      => $category_1['name'],
                'inner'     => $count_iner,
                'type'      => $type,
                'href'      => $link.'product/category&path='.$category_1['category_id']
            );
        }


        $data['blog'] = array();

        $blog_cats_1 = $this->model_gtsmenu_sitemenu->getLinksBlogs(0,0);

        foreach ($blog_cats_1 as $blog_cat_1) {

            $count_iner = $this->model_gtsmenu_sitemenu->getLinksBlogsCountInner($blog_cat_1['category_id']);

            if(!$count_iner){
                $count_ittem = $this->model_gtsmenu_sitemenu->getLinksArticleCountInner($blog_cat_1['category_id']);
            }


            if(!empty($count_ittem) && $count_ittem != 0){
                $type  = 'ittem';
                $inner = $count_ittem;
            }elseif(!empty($count_iner) && $count_iner != 0){
                $type  = 'cat';
                $inner = $count_iner;
            }else{
                $type  = false;
                $inner = false;
            }

            $data['blogs'][] = array(
                'id'        => $blog_cat_1['category_id'],
                'parent_id' => $blog_cat_1['parent_id'],
                'name'      => $blog_cat_1['name'],
                'inner'     => $inner,
                'type'      => $type,
                'href'      => $link.'blog/category&blog_category_id='.$blog_cat_1['category_id']
            );
        }

        $data['special']        = $link.'product/special';
        $data['text_special']   = $this->language->get('text_special');

        $data['account']        = $link.'account/account';
        $data['text_account']   = $this->language->get('text_account');

        $data['edit']           = $link.'account/edit';
        $data['text_edit']      = $this->language->get('text_edit');

        $data['password']       = $link.'account/password';
        $data['text_password']  = $this->language->get('text_password');

        $data['address']        = $link.'account/address';
        $data['text_address']   = $this->language->get('text_address');

        $data['history']        = $link.'account/order';
        $data['text_history']   = $this->language->get('text_history');

        $data['download']       = $link.'account/download';
        $data['text_download']  = $this->language->get('text_download');

        $data['cart']           = $link.'checkout/cart';
        $data['text_cart']      = $this->language->get('text_cart');

        $data['checkout']       = $link.'checkout/checkout';
        $data['text_checkout']  = $this->language->get('text_checkout');

        $data['search']         = $link.'product/search';
        $data['text_search']    = $this->language->get('text_search');

        $data['contact']        = $link.'information/contact';
        $data['text_cart']      = $this->language->get('text_cart');

        $data['text_modal_name']    = $this->language->get('text_modal_name');
        $data['text_category_name'] = $this->language->get('text_category_name');
        $data['text_blog_name']     = $this->language->get('text_blog_name');
        $data['text_informations']  = $this->language->get('text_informations');
        $data['entry_close']        = $this->language->get('entry_close');


        $this->load->model('catalog/information');

        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            $data['informations'][] = array(
                'title' => $result['title'],
                'type'  => 'inf',
                'href'  => $link.'information/information&information_id=' . $result['information_id']
            );
        }

        $this->response->setOutput($this->load->view('gtsmenu/html/sitemap', $data));
    }
}