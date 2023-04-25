<?php

class Theme {
	public $files;
	public $js;
	public $css;
	public $output;
	
	public function __construct() {
		/* Setup the default paths to be included if nothing else is specificed */
		$this->files = array(
			'template' => JET::base() . '/theme/templates/base/chassis/template.php',
			'html' => JET::base() . '/theme/templates/base/chassis/html.php',
			'page' => JET::base() . '/theme/templates/base/chassis/page.php',
			'engine' => JET::base() . '/theme/templates/base/chassis/engine.php',
			'content' => JET::base() . '/theme/templates/base/default/content.php',
			'alpha' => JET::base() . '/theme/templates/base/default/alpha.php',
			'head' => JET::base() . '/theme/templates/base/default/head.php',
			'footer' => JET::base() . '/theme/templates/base/default/footer.php',
			'js' => JET::base() . '/theme/templates/base/default/js.php',
			'omega' => JET::base() . '/theme/templates/base/default/omega.php',
			'notification' => JET::base() . '/theme/templates/base/default/notification.php',
			'navigation' => JET::base() . '/theme/templates/base/default/navigation.php',
		);
		$this->js = array();
		$this->css = array();
		$this->output = "";
	}

	/* Search given dir for files to override defaults */
	public function setDir($dir) {
		$dh  = opendir(__DIR__."/../theme/templates/".$dir);
		while (false !== ($filename = readdir($dh))) {
			if ($filename != "." && $filename != "..") {
				$f = explode(".", $filename);
				$this->files[$f[0]] = __DIR__."/../theme/templates/".$dir."/".$filename;
			}
		}
	}

	/* Allow customization of theme files */
	public function setFile($name, $file) {
		$this->files[$name] = $file;
	}

	public function getFile($name) {
		return $this->files[$name];
	}

	public function addJS($file) {
		$this->js[] = $file;
	}

	public function addCSS($file) {
		$this->css[] = $file;
	}

	public function setOutput($output) {
		$this->output = $output;
	}

	public function render($vars=array(), $data=array()) {
		$vars['_jet_js'] = $this->js;
		$vars['_jet_css'] = $this->css;
		
		$vars['_content'] = JET::inject($this->files['content'], $vars);
		$vars['_engine'] = JET::inject($this->files['engine'], $vars);
		$vars['_navigation'] = JET::inject($this->files['navigation'], $vars);
		$vars['_notification'] = JET::inject($this->files['notification'], $vars);

		$vars['_alpha'] = JET::inject($this->files['alpha'], $vars);
		$vars['_head'] = JET::inject($this->files['head'], $vars);
		$vars['_page'] = JET::inject($this->files['page'], $vars);
		$vars['_footer'] = JET::inject($this->files['footer'], $vars);
		$vars['_js'] = JET::inject($this->files['js'], $vars, $data);
		$vars['_omega'] = JET::inject($this->files['omega'], $vars);

		$vars['_html'] = JET::inject($this->files['html'], $vars);

		$_template = JET::inject($this->files['template'], $vars);
		return $_template;
	}

	public function print() {
		return $this->output;
	}
}