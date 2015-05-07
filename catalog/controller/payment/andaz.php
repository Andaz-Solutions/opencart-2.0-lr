<?php
class ControllerPaymentAndaz extends Controller {
	public function index() {
		$this->load->language('payment/andaz');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wait'] = $this->language->get('text_wait');

		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/andaz.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/andaz.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/andaz.tpl', $data);
		}
	}

	public function send() {

		$url = "https://secure.andazsolutions.com/post-web-service/process";

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$request = array(
			'client_id' => $this->config->get('andaz_client_id'),
			'client_username' => $this->config->get('andaz_username'),
			'client_password' => $this->config->get('andaz_password'),
			'client_token' => $this->config->get('andaz_token'),
			'account_number' => html_entity_decode(preg_replace('/[^0-9]/', '', $this->request->post['cc_number']), ENT_QUOTES, 'UTF-8'),
			'cvv2' => html_entity_decode($this->request->post['cc_cvv2'], ENT_QUOTES, 'UTF-8'),
			'expiration_month' => html_entity_decode($this->request->post['cc_expire_date_month'], ENT_QUOTES, 'UTF-8'),
			'expiration_year' => html_entity_decode($this->request->post['cc_expire_date_year'], ENT_QUOTES, 'UTF-8'),
			'amount' => $this->currency->format($order_info['total'], $order_info['currency_code'], false, false),
			'billing_first_name' => html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'),
			'billing_last_name' => html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'),
			'billing_date_of_birth' => (empty($this->session->data['guest']['birthday']) ? (empty($this->session->data['birthday']) ? '' : $this->session->data['birthday']) : $this->session->data['guest']['birthday']),
			'billing_email_address' => html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'),
			'billing_address_line_1' => html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'),
			'billing_phone_number' => html_entity_decode(preg_replace('/[^0-9]/', '', $order_info['telephone']), ENT_QUOTES, 'UTF-8'),
			'billing_city' =>  html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'),
			'billing_state' => ($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'),
			'billing_country' => html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8'),
			'billing_postal_code' => html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8'),
			'remote_address' => $this->request->server['REMOTE_ADDR'],
			'domain' => $_SERVER['HTTP_HOST'],
		);

		if ($this->cart->hasShipping()) {
			$shipping_info = array(
				'shipping_first_name' => html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8'),
				'shipping_last_name' => html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8'),
				'shipping_email_address' => html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'),
				'shipping_address_line_1' => html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8'),
				'shipping_phone_number' => html_entity_decode(preg_replace('/[^0-9]/','', $order_info['telephone']), ENT_QUOTES, 'UTF-8'),
				'shipping_city' => html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8'),
				'shipping_state' => ($order_info['shipping_iso_code_2'] != 'US') ? $order_info['payment_zone'] : html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'),
				'shipping_country' => html_entity_decode($order_info['shipping_iso_code_2'], ENT_QUOTES, 'UTF-8'),
				'shipping_postal_code' => html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8'),
			);
		} else {
			$shipping_info = array(
				'shipping_first_name' => html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'),
				'shipping_last_name' => html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'),
				'shipping_email_address' => html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'),
				'shipping_address_line_1' => html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'),
				'shipping_phone_number' => html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8'),
				'shipping_city' => html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'),
				'shipping_state' => ($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'),
				'shipping_country' => html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8'),
				'shipping_postal_code' => html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8'),
			);
		}

		$request = array_merge($request, $shipping_info);

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request, '', '&'));

		$response = curl_exec($curl);

		$json = array();

		if (curl_error($curl)) {
			$json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);

			$this->log->write('AUTHNET AIM CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {
			$response_object = (array)json_decode($response);

			$fp = fopen('/tmp/purchase', 'a+');
			fwrite($fp, "Response: ".print_r($response_object, true));

			if ($response_object['status'] == 'approved') {
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('andaz_order_status_id'), $response_object['status'], false);

				$json['redirect'] = $this->url->link('checkout/success', '', 'SSL');
			} else {
				$json['error'] = 'Your transaction was declined, please use another card.';
				preg_match('/<remark>([^<]+)/', $response_object['raw_message'], $match);
				if (count($match)) {
					$json['error'] = $match[1];
				}
			}
		} else {
			$json['error'] = 'Empty Gateway Response';

			$this->log->write('Andaz CURL ERROR: Empty Gateway Response');
		}

		curl_close($curl);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

