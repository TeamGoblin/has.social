<?php

class RootWeb extends WEB {

	/* Set defaults for theme variables in constructor */
	public function __construct($pilot) {
		parent::__construct($pilot);
	}

	/* Login HTML page */
	public function front() {
		//$this->theme->setDir('');
		//$this->setVars(array('users' => $users));
		$this->setData(array('jwt' => $this->pilot->get('jwt')));
		
		//$this->theme->addJS('https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js');
		//$this->theme->addJS('https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js');
		//$this->theme->addJS('/js/list.js');
		//$this->theme->addCSS('https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css');
	}
}