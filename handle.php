<?php
if(!isset($_POST['request'])) {
	die;
}
require 'class.Chatbox.php';
$cb = new Orpheus\Chatbox();
$request = json_decode($_POST['request'], true);
if(!$request) {
	die('Badly formed JSON');
}
echo $cb->handleRequest($request);
?>
