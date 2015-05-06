<?php
class ControllerPaymentAndaz extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/andaz');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('andaz', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_test'] = $this->language->get('text_test');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_capture'] = $this->language->get('text_capture');

		$data['entry_client_id'] = $this->language->get('entry_client_id');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_token'] = $this->language->get('entry_token');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['client_id'])) {
			$data['error_client_id'] = $this->error['client_id'];
		} else {
			$data['error_client_id'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}
		
		if (isset($this->error['token'])) {
			$data['error_token'] = $this->error['token'];
		} else {
			$data['error_token'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/andaz', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/andaz', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['andaz_client_id'])) {
			$data['andaz_client_id'] = $this->request->post['andaz_client_id'];
		} else {
			$data['andaz_client_id'] = $this->config->get('andaz_client_id');
		}

		if (isset($this->request->post['andaz_username'])) {
			$data['andaz_username'] = $this->request->post['andaz_username'];
		} else {
			$data['andaz_username'] = $this->config->get('andaz_username');
		}

		if (isset($this->request->post['andaz_password'])) {
			$data['andaz_password'] = $this->request->post['andaz_password'];
		} else {
			$data['andaz_password'] = $this->config->get('andaz_password');
		}
		
		if (isset($this->request->post['andaz_token'])) {
			$data['andaz_token'] = $this->request->post['andaz_token'];
		} else {
			$data['andaz_token'] = $this->config->get('andaz_token');
		}

		if (isset($this->request->post['andaz_hash'])) {
			$data['andaz_hash'] = $this->request->post['andaz_hash'];
		} else {
			$data['andaz_hash'] = $this->config->get('andaz_hash');
		}

		if (isset($this->request->post['andaz_server'])) {
			$data['andaz_server'] = $this->request->post['andaz_server'];
		} else {
			$data['andaz_server'] = $this->config->get('andaz_server');
		}

		if (isset($this->request->post['andaz_mode'])) {
			$data['andaz_mode'] = $this->request->post['andaz_mode'];
		} else {
			$data['andaz_mode'] = $this->config->get('andaz_mode');
		}

		if (isset($this->request->post['andaz_method'])) {
			$data['andaz_method'] = $this->request->post['andaz_method'];
		} else {
			$data['andaz_method'] = $this->config->get('andaz_method');
		}

		if (isset($this->request->post['andaz_total'])) {
			$data['andaz_total'] = $this->request->post['andaz_total'];
		} else {
			$data['andaz_total'] = $this->config->get('andaz_total');
		}

		if (isset($this->request->post['andaz_order_status_id'])) {
			$data['andaz_order_status_id'] = $this->request->post['andaz_order_status_id'];
		} else {
			$data['andaz_order_status_id'] = $this->config->get('andaz_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['andaz_geo_zone_id'])) {
			$data['andaz_geo_zone_id'] = $this->request->post['andaz_geo_zone_id'];
		} else {
			$data['andaz_geo_zone_id'] = $this->config->get('andaz_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['andaz_status'])) {
			$data['andaz_status'] = $this->request->post['andaz_status'];
		} else {
			$data['andaz_status'] = $this->config->get('andaz_status');
		}

		if (isset($this->request->post['andaz_sort_order'])) {
			$data['andaz_sort_order'] = $this->request->post['andaz_sort_order'];
		} else {
			$data['andaz_sort_order'] = $this->config->get('andaz_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/andaz.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/andaz')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}


		if (!$this->request->post['andaz_client_id']) {
			$this->error['client_id'] = $this->language->get('error_client_id');
		}

		if (!$this->request->post['andaz_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['andaz_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['andaz_token']) {
			$this->error['token'] = $this->language->get('error_token');
		}

		return !$this->error;
	}
}
