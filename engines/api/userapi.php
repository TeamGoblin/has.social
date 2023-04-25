<?php
class UserAPI extends API {

	/* Set defaults for theme variables in constructor */
	public function __construct($pilot) {
		parent::__construct($pilot);
	}

	/* Return users from DB */
	public function list() {
		global $_db;

		$rows = $_db->table('users')->get();
		var_dump($rows);

		//var_dump($this->pilot->jsonSerialize());
		//$this->theme->setDir('');
		//$this->setVars(array('users' => $users));
		
		//$this->theme->addJS('https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js');
		//$this->theme->addJS('https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js');
		//$this->theme->addJS('/js/list.js');
		//$this->theme->addCSS('https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css');
	}
}