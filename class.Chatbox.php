<?php
namespace Orpheus;

session_start();

$root = dirname(__FILE__);
define('CHATBOX_ROOT', $root);

require CHATBOX_ROOT . '/config.Chatbox.php';

class Chatbox {
	protected $pdo = null;

	public function __construct() {
		$this->pdo = new \PDO(SQL_DATA_SOURCE, SQL_USERNAME, SQL_PASSWORD);
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	public function register(array $options) {
		if(isset($options['username'], $options['password'], $options['email'])) {
			if(!ctype_alnum($options['username'])) {
				return 'INVALID_USERNAME';
			}
			if(!filter_var($options['email'], FILTER_VALIDATE_EMAIL)) {
				return 'INVALID_EMAIL';
			}
			$password = password_hash($options['password'], PASSWORD_DEFAULT);
			$q = $this->pdo->prepare('INSERT INTO chat_users(username, password, email, date_registered) VALUES(?, ?, ?, ?)');
			$r = $q->execute(array($options['username'], $password, $options['email'], time()));
			if(!$r) {
				return 'USERNAME_EMAIL_OCCUPIED';
			}
			return true;
		}
	}

	public function login(array $options) {
		if(isset($options['username'], $options['password'])) {
			if(!ctype_alnum($options['username'])) {
				return 'INVALID_USERNAME';
			}
			$q = $this->pdo->prepare('SELECT COUNT(*) AS userExists, id AS uid FROM chat_users WHERE username = ? AND password = ?');
			$password = password_hash($options['password'], PASSWORD_DEFAULT);
			$q->execute(array($options['username'], $password));
			$r = $q->fetch(\PDO::FETCH_ASSOC);
			if($r['userExists'] > 0) {
				$_SESSION['loggedIn'] = true;
				$_SESSION['uid'] = $r['uid'];
				return true;
			}
			$_SESSION['loggedIn'] = false;
			$_SESSION['uid'] = 0;
			return 'INVALID_USERNAME_PASSWORD';
		}
	}

	public function logout() {
		if($this->authenticated()) {
			$_SESSION['loggedIn'] = false;
			$_SESSION['uid'] = 0;
			return true;
		}

		return false;
	}

	public function createRoom(array $options) {
		if(isset($options['room']) && $this->authenticated()) {
			$name = $options['room'];
			$owner = $_SESSION['uid'];
			if(!ctype_alnum($name)) {
				return 'INVALID_ROOM_NAME';
			}
			$q = $this->pdo->prepare('INSERT INTO chat_rooms(name, owner, date_registered) VALUES(?, ?, ?)');
			$q->execute(array($name, $owner, time()));
		}
	}

	public function getMessages(array $options) {
		if(isset($options['room'], $options['limit'])) {
			if($this->getRoomName($options)) {
				$rid = $options['room'];
				$limit = $options['limit'];
				if(!ctype_digit($limit)) {
					return 'INVALID_LIMIT';
				}
				$limit = (int) $limit;
				if($limit > CHAT_VIEW_LIMIT && CHAT_VIEW_LIMIT > 0) {
					return 'MAX_LIMIT';
				}
				$q = $this->pdo->prepare('SELECT * FROM chat_messages WHERE room = ? LIMIT ' . $limit);
				$q->execute(array($rid));
				$r = $q->fetchAll(\PDO::FETCH_ASSOC);
				if($r) {
					return json_encode($r);
				}
				return 'NO_MESSAGES';
			} else {
				return 'INVALID_ROOM';
			}
		} else {
			return 'MISSING_PARAMETERS';
		}
	}

	public function sendMessage(array $options) {
		if(isset($options['room'], $options['message'])) {
			$authenticated = $this->authenticated();
			if(!CHAT_GUESTS_ALLOWED && !$authenticated) {
				return 'NEED_LOGIN';
			}
			$author = $authenticated ? $this->getUsername($_SESSION['uid']) : $this->generateName();
			$uid = $authenticated ? $_SESSION['uid'] : 0;
			$message = $options['message'];
			$room = $options['room'];
			if(strlen($message) >= CHAT_MESSAGE_MAX_LENGTH && CHAT_MESSAGE_MAX_LENGTH > 0) {
				return 'MSG_TOO_LONG';
			}
			if(!ctype_alnum($room)) {
				return 'INVALID_ROOM_NAME';
			}
			$roomName = $this->getRoomNAme($options);
			if(!$roomName) {
				return 'INVALID_ROOM';
			}
			if(CHAT_MESSAGE_COOLDOWN > 0) {
				$q = $this->pdo->prepare('SELECT lastMessage FROM chat_sessions WHERE ip = ?');
				$q->execute(array($_SERVER['REMOTE_ADDR']));
				$r = $q->fetch(\PDO::FETCH_ASSOC);
				$d = time() - $r['lastMessage'];
				if($d < CHAT_MESSAGE_COOLDOWN) {
					return 'MESSAGE_COOLDOWN';
				}
			}
			$q = $this->pdo->prepare('INSERT INTO chat_messages(message, nick, uid, room, time) VALUES(?, ?, ?, ?, ?)');
			$q->execute(array($message, $author, $uid, $room, time()));
			$q = $this->pdo->prepare('INSERT INTO chat_sessions(ip, lastMessage) VALUES(?, ?) ON DUPLICATE KEY UPDATE lastMessage = UNIX_TIMESTAMP(NOW())');
			$q->execute(array($_SERVER['REMOTE_ADDR'], time()));
			return 'MESSAGE_OK';
		} else {
			return 'MISSING_PARAMETERS';
		}
	}

	public function getRoomName(array $options) {
		if(!isset($options['room'])) {
			return 'MISSING_PARAMETERS';
		}
		$room = $options['room'];
		$q = $this->pdo->prepare('SELECT COUNT(*) AS roomExists, name FROM chat_rooms WHERE id = ?');
		$q->execute(array($room));
		$r = $q->fetch(\PDO::FETCH_ASSOC);
		if($r['roomExists'] > 0) {
			return $r['name'];
		}
		return false;
	}

	public function generateName() {
		$ip = $_SERVER['REMOTE_ADDR'];
		$id = md5($ip . CHAT_GUESTNAME_SALT);
		$id = substr($id, 0, 6);
		return 'guest_' . $id;
	}

	public function getUsername(array $options) {
		if(!isset($options['uid'])) {
			return 'MISSING_PARAMETERS';
		}
		$uid = $options['uid'];
		if(!ctype_digit($uid)) {
			return 'INVALID_PARAMETER';
		}
		$q = $this->pdo->prepare('SELECT username FROM chat_users WHERE id = ?');
		$q->execute(array($uid));
		$r = $q->fetch(\PDO::FETCH_ASSOC);
		if($r) {
			return $r['username'];
		}
		return false;
	}

	public function getResponse(array $options) {
		if(!isset($options['error'], $options['lang'])) {
			return 'null';
		}
		$error = $options['error'];
		$lang = $options['lang'];

		global $language;
		if(isset($language['resp_codes'][$error][$lang])) {
			return $language['resp_codes'][$error][$lang];
		}
		return 'null';
	}

	public function handleRequest(array $command) {
		if(CHAT_API_AUTHENTICATION && !isset($command['APP_ID'])) {
			return 'AUTHENTICATION_REQUIRED';
		} else if(CHAT_API_AUTHENTICATION && isset($command['APP_ID'])) {
			$q = $this->pdo->prepare('SELECT COUNT(*) AS appExists FROM chat_applications WHERE app_id = ?');
			$q->execute(array($command['APP_ID']));
			$r = $q->fetch(\PDO::FETCH_ASSOC);
			if($r['appExists'] <= 0) {
				return 'INVALID_APPLICATION_ID';
			}
		}
		if(!isset($command['command'], $command['options'])) {
			return 'INVALID_REQUEST';
		}
		if(!$command) {
			return 'BADLY_FORMED_COMMAND';
		}
		$permittedFunctions = array('getMessages', 'getUsername', 'getRoomName', 'getError', 'login', 'logout', 'register', 'sendMessage');
		if(!in_array($command['command'], $permittedFunctions)) {
			return 'INVALID_COMMAND';
		} else {
			$function = $command['command'];
			$result = array('command' => $function, 'result' => $this->$function($command['options']));
			return json_encode($result);
		}
	}

	public function authenticated() {
		if(isset($_SESSION['loggedIn'])) {
			return $_SESSION['loggedIn'] === true && $_SESSION['uid'] > 0;
		}
		return false;
	}
}
