<?php

class Notification {
	public $type, $msg;
	
	public function __construct($type="", $msg="") {
		$this->type = $type;
		$this->msg = $msg;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function setMsg($msg) {
		$this->msg = $msg;
	}

	public function getType() {
		if (empty($this->type)) {
			if (empty($_SESSION['notification']['type'])) {
				return NULL;
			} else {
				$this->type = $_SESSION['notification']['type'];
			}
		}
		return $this->type;
	}

	public function getMsg() {
		if (empty($this->msg)) {
			if (empty($_SESSION['notification']['msg'])) {
				return NULL;
			} else {
				$this->msg = $_SESSION['notification']['msg'];
			}
		}
		return $this->msg;
	}

	public static function t($type) {
		$_SESSION['notification']['type'] = $type;
	}

	public static function m($msg) {
		$_SESSION['notification']['msg'] = $msg;
	}
}