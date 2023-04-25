<?php
/* Include JET Class */
include_once('jet.php');

$_template = JET::inject(JET::base() . '/theme/templates/base/install.php', []);
echo $_template;