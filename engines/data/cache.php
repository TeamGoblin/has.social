<?php

class Cache {
	private $items; // Cache items array

	/**
	* Construct method
	*
	* @param $arg array - Values used to create object
	* @return null
	*/
	function __construct($arg = array()) {
		$this->items = array();
	}

	function get($name) {
		if (!empty($this->items[$name])) {
			return $this->items[$name];
		} else {
			return null;
		}
	}

	function set($item) {
		$name = get_class($item) . "_" . $item->get('id');
		$this->items[$name] = $item;
	}
}

global $_cache;
$_cache = new Cache();