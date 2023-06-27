<?php

class WEB {

	protected $theme, $pilot;
	public $vars, $data;

	/* Set defaults for theme variables in constructor */
	public function __construct($pilot=null) {
		$this->theme = new Theme();
		$this->pilot = $pilot;
		$urls = explode('/',$_SERVER['REQUEST_URI']);

		if (!empty($urls[1])) {
			$current_url = $urls[1];
			if ($urls[1] == "users") {
				if (!empty($urls[2]) && $urls[2] == "edit") {
					if (!empty($urls[3]) && $urls[3] == $this->pilot->get('id')) {
						$current_url = "profile";
					}
				}
			}
		} else {
			$current_url = "";
		}
		$this->vars['current_url'] = $current_url;
		$this->vars['pilot'] = $this->pilot;
	}

	public function setVars($vars) {
		foreach ($vars as $key => $val) {
			$this->vars[$key] = $val;
		}
	}

	public function setData($data) {
		foreach ($data as $key => $val) {
			$this->data[$key] = $val;
		}
	}

	/* Print addon */
	public function __toString() {
		return $this->theme->render($this->vars, $this->data);
	}
}
