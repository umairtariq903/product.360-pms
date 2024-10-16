<?php
//array_unshift(JsDependency::$Files[JS_JQUERY], 'common.js');
//return;
JsDependency::$Files[JS_JQUERY] = array(
		'js/libs/jquery.min.js',
		'js/libs/jquery-migrate.min.js',
		'js/libs/client.min.js'
		);
if (Config('app.USE_METRONIC'))
{
	JsDependency::$Files[JS_BOOTSTRAP] = array(
		JS_JQUERY,
		'dodatak/metronic/global/plugins/bootstrap/css/bootstrap.min.css',
		'dodatak/metronic/global/plugins/bootstrap/js/bootstrap.min.js',
		'others/bootstrap/bs_select/css/bootstrap-select.min.css',
		'others/bootstrap/bs_select/js/bootstrap-select.min.js',
		'others/bootstrap/bs_validator/validator.min.js',
		JS_JQUERY_UI
	);
}
