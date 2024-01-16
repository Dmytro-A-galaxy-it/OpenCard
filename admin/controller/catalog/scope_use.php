<?php 
class ControllerCatalogScopeUse extends Controller{

    private $error = array();

    public function index(){

        $this->load->language('catalog/scope_use');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/scope_use');

        $this->getList();
    }

    public function getList() {

        $url = '';

        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/scope_use', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/scope_use/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/scope_use/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['scope_uses'] = array();

        $results = $this->model_catalog_scope_use->getScopeUses();

		foreach ($results as $result) {
			$data['scope_uses'][] = array(
				'sphere_id' => $result['sphere_id'],
				'name'            => $result['name'],
                'edit'            => $this->url->link('catalog/scope_use/edit', 'user_token=' . $this->session->data['user_token'] . '&scope_use_id=' . $result['sphere_id'] . $url, true)
            );
		}

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/scope_use_list', $data));
    }

    public function add() {

		$this->load->language('catalog/scope_use');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/scope_use');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_scope_use->addScopeUse($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			$this->response->redirect($this->url->link('catalog/scope_use', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

    public function edit() {

		$this->load->language('catalog/scope_use');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/scope_use');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_catalog_scope_use->editScopeUse($this->request->get['scope_use_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			$this->response->redirect($this->url->link('catalog/scope_use', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

    public function delete() {
		$this->load->language('catalog/scope_use');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/scope_use');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $scope_use_id) {
				$this->model_catalog_scope_use->deleteScopeUse($scope_use_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
		
			$this->response->redirect($this->url->link('catalog/scope_use', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

    protected function getForm() {
		$data['text_form'] = !isset($this->request->get['scope_use_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/scope_use', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['scope_use_id'])) {
			$data['action'] = $this->url->link('catalog/scope_use/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/scope_use/edit', 'user_token=' . $this->session->data['user_token'] . '&scope_use_id=' . $this->request->get['scope_use_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/scope_use', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['scope_use_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$scope_use_info = $this->model_catalog_scope_use->getScopeUse($this->request->get['scope_use_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($scope_use_info)) {
			$data['name'] = $scope_use_info['name'];
		} else {
			$data['name'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/scope_use_form', $data));
	}

    protected function validateForm() {

		if (!$this->user->hasPermission('modify', 'catalog/scope_use')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}

    protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/scope_use')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}