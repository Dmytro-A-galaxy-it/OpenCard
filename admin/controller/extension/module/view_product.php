<?php
    class ControllerExtensionModuleViewProduct extends Controller{
        
        private $error = array();

        public function index (){

            $this->load->language('extension/module/view_product');

            $this->load->model('setting/module');

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                if (!isset($this->request->get['module_id'])) {
                    $this->model_setting_module->addModule('view_product', $this->request->post);
                } else {
                    $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
                }
    
                $this->session->data['success'] = $this->language->get('text_success');
    
                $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            }

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
            );

            if (!isset($this->request->get['module_id'])) {
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('extension/module/view_product', 'user_token=' . $this->session->data['user_token'], true)
                );
            } else {
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('extension/module/view_product', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
                );
            }

            if (!isset($this->request->get['module_id'])) {
                $data['action'] = $this->url->link('extension/module/view_product', 'user_token=' . $this->session->data['user_token'], true);
            } else {
                $data['action'] = $this->url->link('extension/module/view_product', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
            }

            $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

            if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
            }

            if (isset($this->request->post['name'])) {
                $data['name'] = $this->request->post['name'];
            } elseif (!empty($module_info)) {
                $data['name'] = $module_info['name'];
            } else {
                $data['name'] = '';
            }

            if (isset($this->request->post['status'])) {
                $data['status'] = $this->request->post['status'];
            } elseif (!empty($module_info)) {
                $data['status'] = $module_info['status'];
            } else {
                $data['status'] = '';
            }

            $data['header'] = $this->load->controller('common/header');
		    $data['column_left'] = $this->load->controller('common/column_left');
		    $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/module/view_product/view_product', $data));
        }

        protected function validate() {
            if (!$this->user->hasPermission('modify', 'extension/module/view_product')) {
                $this->error['warning'] = $this->language->get('error_permission');
            }
    
            // if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            //     $this->error['name'] = $this->language->get('error_name');
            // }
    
            return !$this->error;
        }

    }

?>