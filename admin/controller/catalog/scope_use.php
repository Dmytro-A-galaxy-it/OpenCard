<?php 
class ControllerCatalogScopeUse extends Controller{

    private $error = array();

    public function index(){

        $this->load->language('catalog/scope_use');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/scope_use');

		// $this->autocomplete();

        $this->getList();
    }

    public function getList() {

        if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
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

		$data['add'] = $this->url->link('catalog/scope_use/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/scope_use/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['scope_uses'] = array();

        $filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

        $scope_use_total = $this->model_catalog_scope_use->getTotalScopeUse();

        $results = $this->model_catalog_scope_use->getScopeUses($filter_data);

		foreach ($results as $result) {
			$data['scope_uses'][] = array(
				'sphere_id' => $result['sphere_id'],
				'name'            => $result['name'],
                'edit'            => $this->url->link('catalog/scope_use/edit', 'user_token=' . $this->session->data['user_token'] . '&scope_use_id=' . $result['sphere_id'] . $url, true)
            );
		}

        $pagination = new Pagination();
		$pagination->total = $scope_use_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/scope_use', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($scope_use_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($scope_use_total - $this->config->get('config_limit_admin'))) ? $scope_use_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $scope_use_total, ceil($scope_use_total / $this->config->get('config_limit_admin')));


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

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/scope_use');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_scope_use->getScopeUses($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'sphere_id' => $result['sphere_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		
		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}