<?php

for ($i = 0; $i < 1000; $i++)
	usleep(500);

require_once('simple-api.php');
$api = new SimpleApi();
$api->run();