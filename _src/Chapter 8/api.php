<?php

// Retrieve post values
$values = array();
foreach ($_POST as $k => $v)
	$values[$k] = addslashes(strip_tags($v));

// Retrieve Salt
$api_credentials_salt = '';
if ($values['api_credentials_id'] == 'presta')
	$api_credentials_salt = 'Hg56jk8;n?';

// Check payment token
$payment_token = md5($api_credentials_salt.$values['id_cart'].$values['total_to_pay']);
if ($payment_token != $values['payment_token'])
	die('CREDENTIALS ARE INVALID');
	

// Proceed paiement
if (isset($values['card']))
{
	$total_paid = $values['total_to_pay'];
	$transaction_id = date('YmdHis').'-'.rand();
	$postvalues = 'transaction_id='.$transaction_id;
	$postvalues .= '&id_cart='.$values['id_cart'];
	$postvalues .= '&total_paid='.$total_paid;
	$postvalues .= '&validation_token='.md5($api_credentials_salt.$values['id_cart'].$total_paid.$transaction_id);
	foreach ($values as $k => $v)
		$postvalues .= '&'.$k.'='.$v;

	// If payment succeed
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $values['validation_url']);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postvalues);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$return = curl_exec($ch);
	if (curl_errno($ch)) 
		die(curl_error($ch));
	curl_close($ch);

	$json = json_decode($return, true);

	if (isset($json['error']))
	{		
		die('ERROR:'.$json['error']);

		// If payment failed
		header('location:'.$values['cancel_url']);
		exit;
	}

	if (isset($json['return_link']))
	{
		header('location:'.$json['return_link']);
		exit;	
	}

	die($return);
}

?><html>
	<head>
		<title>Payment API</title>
	</head>

	<body>
		<pre>
		You have to pay : <?php echo $values['total_to_pay']; ?> $<br>
		Please fill up the form below:
		<form action="" method="POST">
			Card: <input type="text" name="card" /><br>
			Valid: <input type="text" name="month" maxlength="2" size="2" /> / <input type="text" name="year" maxlength="4" size="4" /><br>
			Security code: <input type="text" name="security" maxlength="3" size="3" /><br>
			<?php
				foreach ($values as $k => $v)
					echo '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
			?>
			<input type="submit" value="Pay" />
		</form>
		<p><a href="<?php echo $values['return_url']; ?>">Return</a></p>
		</pre>	
	</body>
</html>
