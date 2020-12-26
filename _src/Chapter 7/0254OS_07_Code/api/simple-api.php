<?php

class SimpleApi
{
	public $method = '';
	public $email = '';
	public $token = '';
	public $city = '';

	public $authorized_user = array(
		'fabien@mymodcomments.com' => '23c4380e50caf91f81793ac91d9bfde9',
	);
	public $authorized_methods = array('getRelayPoint', 'getShippingCost', 'testConnection');

	public function __construct()
	{
		// Check method
		if (!isset($_GET['method']))
			$this->renderError('You have to specify the method you want to call.');

		// Check user
		if (!isset($_GET['mca_email']) || !isset($_GET['mca_token']))
			$this->renderError('You have to provide the e-mail and token of the merchant.');

		// Store values
		$this->method = $_GET['method'];
		$this->email = $_GET['mca_email'];
		$this->token = $_GET['mca_token'];

		// Check city
		if ($this->method != 'testConnection')
		{
			if (!isset($_GET['city']) || !isset($_GET['city']))
				$this->renderError('You have to provide the delivery city name.');
			$this->city = $_GET['city'];
		}
	}

	public function run()
	{
		$this->checkLogin();
		$this->callMethod();
	}


	public function checkLogin()
	{
		if (!isset($this->authorized_user[$this->email]) || $this->authorized_user[$this->email] != $this->token)
			$this->renderError('User or token is incorrect.');
	}

	public function testConnection()
	{
		$this->renderResult('Success');
	}

	public function getRelayPoint()
	{
		$result = array(
			'Paris' => array(
				array('name' => 'Pasta & Dolce', 'address' => '23 rue de provence, 75002 Paris'),
				array('name' => 'Olympia', 'address' => '28 boulevard des Capucines, 75009 Paris'),
			),
			'Edinburgh' => array(
				array('name' => 'The Ghillie Dhu', 'address' => '2 Rutland St Edinburgh, EH1 2AD, Edinburgh'),
			),
		);
		if (isset($result[$this->city]))
			$this->renderResult($result[$this->city]);
		$this->renderResult(array());
	}

	public function getShippingCost()
	{
		$result = array(
			'Paris' => array('ClassicDelivery' => 15, 'RelayPoint' => 5),
			'Edinburgh' => array('ClassicDelivery' => 18, 'RelayPoint' => 3),
			'Barcelona' => array('ClassicDelivery' => 12),
		);
		if (isset($result[$this->city]))
			$this->renderResult($result[$this->city]);
		$this->renderResult(array());
	}

	public function callMethod()
	{
		if (!in_array($this->method, $this->authorized_methods))
			$this->renderError('Method is incorrect.');
		$this->{$this->method}();
	}

	public function renderResult($array)
	{
		echo json_encode($array);
		exit;
	}

	function renderError($error)
	{
		$this->renderResult(array('Error' => $error));
	}
}