<?php
class ControllerExtensionModuleViewProduct extends Controller {

    public function index (){


        $this->load->language('extension/module/view_product');

        $this->load->model('catalog/product');

		$this->load->model('tool/image');

        $data['products']= array();

        if (isset($this->request->get['product_id'])) {

            $product_id = $this->request->get['product_id'];

            $this->addViewedProduct($product_id);

            $data['products'] = $this->getLastViewedProducts();
            
        }

        return $this->load->view('extension/module/view_product/view_product', $data);
    }

    public function addViewedProduct($product_id) {

        $last_viewed_products = $this->session->data['products'] ?? [];

        if (!in_array($product_id, $last_viewed_products)) {

            array_unshift($last_viewed_products, $product_id);

            $last_viewed_products = array_slice(array_unique($last_viewed_products), 0, 5);

            $this->session->data['products'] = $last_viewed_products;
        }
    }

    public function getLastViewedProducts($limit = 5) {

        $products = $this->session->data['products'] ?? [];
        $products = array_slice($products, 0, $limit);

        $products['products'] = array();
        
        foreach ($products as $product_id) {

           if($product_id != $this->request->get['product_id']) {
            
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                    if ($product_info['image']) {
                        $image = $this->model_tool_image->resize($product_info['image'], 100,100);
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', 100,100);
                    }
                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $price = false;
                    }
                    if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
                        $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        $tax_price = (float)$product_info['special'];
                    } else {
                        $special = false;
                        $tax_price = (float)$product_info['price'];
                    }
                    if ($this->config->get('config_tax')) {
                        $tax = $this->currency->format($tax_price, $this->session->data['currency']);
                    } else {
                        $tax = false;
                    }
                    if ($this->config->get('config_review_status')) {
                        $rating = $product_info['rating'];
                    } else {
                        $rating = false;
                    }
                    $products['products'][] = array(
                        'product_id' => $product_info['product_id'],
                        'thumb'       => $image,
                        'name'        => $product_info['name'],
                        'description' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                        'price'       => $price,
                        'special'     => $special,
                        'tax'         => $tax,
                        'rating'      => $rating,
                        'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                        'viewd'       => $product_info['viewed']
                    );
                }
            }
        }

        return $products['products'];
    }
}