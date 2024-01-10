<?php
class ControllerExtensionModuleCustomSlider extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/module/custom_slider');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('custom_slider', $this->request->post);
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
				'href' => $this->url->link('extension/module/custom_slider', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/custom_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/custom_slider', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/custom_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
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

		if (isset($this->error['language_image'])) {
			$data['error_language_image'] = $this->error['language_image'];
		} else {
			$data['error_language_image'] = array();
		}

		$data['language_image'] = $module_info['language_image'];

		$arr = $data['language_image'];

		$data['language_image'] = $this->addmasimage($arr);
		

		$this->load->model('localisation/language');


		$data['languages']= $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/custom_slider/custom_slider', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/custom_slider')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	
	protected function addmasimage($arr){
		
		$this->load->model('tool/image');
		
		foreach ($arr as &$user) {
			foreach ($user as &$subArray) {
				if(is_file(DIR_IMAGE . $subArray['image_pc'])){ 
					$thumb_pc = $subArray['image_pc'];
				}
				else {
					$thumb_pc = 'no_image.png';
				}
				if(is_file(DIR_IMAGE . $subArray['image_mobile'])){
					$thumb_mobile = $subArray['image_mobile'];
				}else{
					$thumb_mobile = 'no_image.png';
				}
				$subArray['thumb_pc'] = $this->model_tool_image->resize($thumb_pc, 100, 100);
				$subArray['thumb_mobile'] = $this->model_tool_image->resize($thumb_mobile, 100, 100);
			}
		}

		return $arr;
	}
}
