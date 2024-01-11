<?php
class ControllerExtensionModuleViewProduct extends Controller {

    public function index (){


        $this->load->language('extension/module/view_product');

        $this->load->model('catalog/product');

		$this->load->model('tool/image');

        $data['products']= array();

        if (isset($this->request->get['product_id'])) {

            $product_id = (int)$_GET['product_id'];
            session_start();

            if (!isset($_SESSION['products'])) {
                $_SESSION['products'] = array();
            }

            if (!in_array($product_id, $_SESSION['products'])) {

                array_unshift($_SESSION['products'],$product_id);

                $_SESSION['products'] = array_slice($_SESSION['products'], 0, 5);
            }

            if(count($_SESSION['products'])>0){

                foreach($_SESSION['products'] as $v){

                    $results = $this->model_catalog_product->getProduct($v);

                    if($product_id != $v){

                        if ($results) {
                        
                            if ($results['image']) {
                                            $image = $this->model_tool_image->resize($results['image'], 100,100);
                            } else {
                                $image = $this->model_tool_image->resize('placeholder.png', 100,100);
                            }
                        
                            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                                $price = $this->currency->format($this->tax->calculate($results['price'], $results['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            } else {
                                $price = false;
                            }
                            if (!is_null($results['special']) && (float)$results['special'] >= 0) {
                                $special = $this->currency->format($this->tax->calculate($results['special'], $results['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                                $tax_price = (float)$results['special'];
                            } else {
                                $special = false;
                                $tax_price = (float)$results['price'];
                            }
                            if ($this->config->get('config_tax')) {
                                $tax = $this->currency->format($tax_price, $this->session->data['currency']);
                            } else {
                                $tax = false;
                            }
                            if ($this->config->get('config_review_status')) {
                                $rating = $results['rating'];
                            } else {
                                $rating = false;
                            }
                            $data['products'][] = array(
                                                'product_id'  => $results['product_id'],
                                                'thumb'       => $image,
                                                'name'        => $results['name'],
                                                'description' => utf8_substr(trim(strip_tags(html_entity_decode($results['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                                                'price'       => $price,
                                                'special'     => $special,
                                                'tax'         => $tax,
                                                'rating'      => $rating,
                                                'href'        => $this->url->link('product/product', 'product_id=' . $results['product_id']),
                                                'viewd'       => $results['viewed']
                            );
                        }
                    }
                }
            }  
        }
        return $this->load->view('extension/module/view_product/view_product', $data);
    }

}