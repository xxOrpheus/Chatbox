<?php
define('SQL_DATA_SOURCE', 'mysql:host=localhost;dbname=chatbox');
define('SQL_USERNAME', 'root');
define('SQL_PASSWORD', '');

define('CHAT_MESSAGE_MAX_LENGTH', 256);
define('CHAT_GUESTS_ALLOWED', true);
define('CHAT_GUESTNAME_SALT', '!&%1$/\'":\[\'"');
define('CHAT_VIEW_LIMIT', 25);
define('CHAT_API_AUTHENTICATION', false);
define('CHAT_MESSAGE_COOLDOWN', 3);

$language = array();

$language['resp_codes'] = array(
	'MSG_TOO_LONG' => array(
		'EN' => 'That message is too long'
	),
	'INVALID_USERNAME' => array(
		'EN' => 'Invalid username (alphanumeric only)'
	),
	'INVALID_EMAIL' => array(
		'EN' => 'Invalid e-mail'
	),
	'INVALID_USERNAME_PASSWORD' => array(
		'EN' => 'Invalid username or password'
	),
	'USERNAME_TOO_LONG' => array(
		'EN' => 'That usename is too long'
	),
	'USERNAME_EMAIL_OCCUPIED' => array(
		'EN' => 'That username or e-mail is in use'
	),
	'INVALID_ROOM' => array(
		'EN' => 'That room does not exist'
	),
	'INVALID_ROOM_NAME' => array(
		'EN' => 'Invalid room name'
	),
	'NEED_LOGIN' => array(
		'EN' => 'You must login to perform this action'
	),
	'MISSING_PARAMETERS' => array(
		'EN' => 'Missing parameters. Please see documentation for this command'
	),
	'MESSAGE_COOLDOWN' => array(
		'EN' => 'Please wait ' . CHAT_MESSAGE_COOLDOWN . ' seconds'
	)
);
?>
