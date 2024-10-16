<?php
if (!defined('DEF_JUI_THEME'))
	define('DEF_JUI_THEME', 'ui-lightness');

define('JS_JQUERY',				0);
define('JS_DGR_COMMON',			1);
define('JS_MASKED_INPUT',		2);
define('JS_MASKED_INPUT_EXT',	3);
define('JS_NUMERIC',			4);
define('JS_SCROLLTO',			5);
define('JS_JQUERY_UI',			6);
define('JS_DRAGGABLE',			7);
define('JS_CPAINT',				8);
define('JS_CALENDAR',			9);
define('JS_TABBER',				10);
define('JS_TINYMCE',			11);
define('JS_DIMENSIONS',			13);
define('JS_WATERMARK',			15);
define('JS_TABLE_SORTER',		16);
define('JS_HIGHLIGHTER',		17);
define('JS_FANCYBOX',			18);
define('JS_JQUERY_CHOSEN',		19);
define('JS_CODE_MIRROR',		20);
define('JS_CODE_SQL',			21);
define('JS_CODE_PHP',			22);
define('JS_DEVELOPER',			23);
define('JS_TOOLBAR',			24);
define('JS_DATATABLES',			25);
define('JS_DATATABLESV1_10',	26);
define('JS_JQGRID',				27);
define('JS_JQDATETIMEPICKER',	28);
define('JS_JQDATETIMEPICKER2',	29);
define('JS_JQTIMEPICKER',		30);
define('JS_DBMODEL_FORM',		31);
define('JS_DBMODEL_LIST',		32);
define('JS_JQKENDO',			33);
define('JS_ERS_TABLE',			34);
define('JS_JQUERY_TREE',		35);
define('JS_SPLITTER',			36);
define('JS_FILEUPLOAD',			37);
define('JS_NIVO_SLIDER',		38);
define('JS_TREETABLE',			39);
define('JUI_THEME',				40);
define('JUI_THEME_CUSTOM',		41);
define('JS_JQCONTEXTMENU',		42);
define('JS_MONTHPICKER',		43);
define('JS_PLOT_PIE',			44);
define('JS_JQ_SIMULATE',		45);
define('JS_BOOTSTRAP',			46);
define('JS_FONT_AWESOME',		47);
define('JS_JQUERY_POPOVER',		48);
define('JS_SMART_WIZARD',		49);
define('JS_PDFMAKE',			50);
define('JS_THREAD',				51);
define('JS_REQUIRE',			52);
define('JS_SPRINTF',			53);
define('JS_BOOTSTRAP_SWEETALERT', 54);
define('JsThemeCSS', 'others/jquery/ui/css/'.DEF_JUI_THEME.'/jquery-ui.css');
define('JsThemeCustomCSS', 'others/jquery/ui/css/'.DEF_JUI_THEME.'/custom.css');
class JsDependency
{
	public static $Files = array(
		JS_JQUERY => array(
			'others/jquery/jquery.js'
			),
		JS_DGR_COMMON	=> array(
			JS_JQUERY,
			JS_NUMERIC,
			JS_JQDATETIMEPICKER,
			'dogru/js/common.min.js'
			),
		JS_MASKED_INPUT	=> array(
			JS_JQUERY,
			'others/jquery/jquery.maskedinput.js'
			),
		JS_MASKED_INPUT_EXT => array(
			JS_JQUERY,
			'others/jquery/inputmask/inputmask.min.js'
			),
		JS_CPAINT => array(
			'others/cpaint/cpaint2.inc.js'
		),
		JS_JQKENDO => array(
			JS_JQUERY,
			'others/jquery/kendo/css/common.css',
			'others/jquery/kendo/css/default.css',
			'others/jquery/kendo/web.min.js',
			'dogru/js/kendo.js'
			),
		JS_JQUERY_TREE  => array(
			JS_JQUERY,
			'others/jquery/jstree/themes/default/style.css',
			'others/jquery/jstree/jstree.js'
			),
		JS_SPLITTER => array(
			JS_JQUERY,
			'others/jquery/splitter/splitter.js'
			),
		JS_JQ_SIMULATE => array(
			JS_JQUERY,
			'others/jquery/jquery.simulate.js'
			),
		JS_NUMERIC => array(
			JS_JQUERY,
			'others/jquery/autoNumeric.js'
			),
		JS_SCROLLTO => array(
			JS_JQUERY,
			'others/jquery/jquery.scrollto.js'
			),
		JS_DIMENSIONS => array(
			JS_JQUERY,
			'others/jquery/jquery.dimensions.js'
			),
		JUI_THEME => array(
			JsThemeCSS
			),
		JUI_THEME_CUSTOM => array(
			JsThemeCustomCSS
			),
		JS_JQUERY_UI => array(
			JS_JQUERY,
			JUI_THEME,
			JUI_THEME_CUSTOM,
			'others/jquery/ui/ui.custom.min.js',
			'others/jquery/dialogextend/dialogextend.min.js'
			),
		JS_DRAGGABLE => array(
			JS_JQUERY,
			'others/jquery/draggable.js'
			),
		JS_WATERMARK => array(
			JS_JQUERY,
			'others/jquery/jquery.watermark.js'
			),
		JS_TABLE_SORTER	=> array(
			JS_JQUERY,
			'others/jquery/jquery.tablesorter.js'
			),
		JS_HIGHLIGHTER => array(
			JS_JQUERY,
			'others/jquery/jquery.highlight-4.js'
			),
		JS_DATATABLES => array(
			JS_JQUERY,
			'others/jquery/dataTables/jquery.dataTables.min.js',
			'others/jquery/dataTables/demo_page.css',
			'others/jquery/dataTables/jquery.dataTables.css',
			'others/jquery/dataTables/demo_table.css',
			'others/jquery/dataTables/ColVis/ColVis.css',
			'others/jquery/dataTables/ColVis/ColVisAlt.css',
			'others/jquery/dataTables/ColReorder/ColReorder.css',
			'others/jquery/dataTables/ColReorder/ColReorder.min.js',
			'dogru/js/dataTable.js',
			'others/jquery/dataTables/ColVis/ColVis.min.js',
            'dogru/css/dataTable.css',
			JS_REQUIRE
			),
		JS_DATATABLESV1_10 => array(
			JS_JQUERY_UI,
			'others/jquery/dataTablesV1.10/datatables.min.css',
			'dogru/js/dataTableV1.10.js',
			'others/jquery/dataTablesV1.10/datatables.min.js'
			),
		JS_JQGRID => array(
			JS_JQUERY_UI,
			'others/jquery/jqGrid/i18n/grid.locale-tr.js',
			'others/jquery/jqGrid/ui.jqgrid.css',
			'others/jquery/jqGrid/jquery.jqGrid.min.js'
		),
		JS_CALENDAR => array(
			'others/calendar/calendar.js',
			'others/calendar/calendar-win2k-1.css',
			'others/calendar/lang/calendar-en.js',
			'others/calendar/calendar-setup.js'
			),
		JS_TABBER => array(
			'others/tabber/tabber.css',
			'others/tabber/tabber.js'
			),
		JS_TINYMCE => array(
			'others/tiny_mce/tiny_mce.js'
		),
		JS_FANCYBOX => array(
			'others/fancybox/jquery.mousewheel-3.0.6.pack.js',
			'others/fancybox/jquery.fancybox.css',
			'others/fancybox/jquery.fancybox-buttons.css',
			'others/fancybox/jquery.fancybox.pack.js',
			'others/fancybox/helpers/jquery.fancybox-thumbs.js',
			'others/fancybox/helpers/jquery.fancybox-thumbs.css'
			),
		JS_JQUERY_CHOSEN => array(
			JS_JQUERY,
			'others/jquery/chosen/chosen.min.css',
			'others/jquery/chosen/chosen.min.js'
			),
		JS_TOOLBAR => array(
			JS_JQUERY,
			'others/jquery/toolbar/jquery.toolbar.css',
			'others/jquery/toolbar/bootstrap.icons.css',
			'others/jquery/toolbar/jquery.toolbar.min.js'
			),
		JS_CODE_MIRROR => array(
			'others/codemirror/codemirror.css',
			'others/codemirror/codemirror.js'
			),
		JS_CODE_SQL => array(
			JS_CODE_MIRROR,
			'others/codemirror/neat.css',
			'others/codemirror/sql.js'
			),
		JS_CODE_PHP => array(
			JS_CODE_MIRROR,
			'others/codemirror/neat.css',
			'others/codemirror/php.js'
			),
		JS_JQDATETIMEPICKER => array(
			JS_JQUERY_UI,
			'others/jquery/DTPicker/datetimepicker.css',
			'others/jquery/DTPicker/datetimepicker.js'
			),
		JS_JQDATETIMEPICKER2 => array(
			JS_JQUERY_UI,
			'others/jquery/DTPicker2/jquery.datetimepicker.min.css',
			'others/jquery/DTPicker2/jquery.datetimepicker.full.min.js'
			),
		JS_FILEUPLOAD	=> array(
			JS_JQUERY,
			'others/jquery/jquery.fileupload.js',
			'dogru/js/fileUpload.js',
			'others/WebCam/webcam.js',
			'others/jquery/jquery.iframe-transport.js',
			'others/cropper/cropper.min.css',
			'others/cropper/cropper.min.js'
			),
		JS_NIVO_SLIDER => array(
			'others/jquery/nivoSlider/nivo.css',
			'others/jquery/nivoSlider/nivo.slider.js'
			),
		JS_TREETABLE => array(
			JS_JQUERY,
			'others/jquery/treeTable/screen.css',
			'others/jquery/treeTable/jquery.treetable.css',
			'others/jquery/treeTable/jquery.treetable.theme.default.css',
			'others/jquery/treeTable/jquery.treetable.js'
			),
		JS_ERS_TABLE => array(
			JS_FILEUPLOAD,
			JS_JQTIMEPICKER,
			JS_JQDATETIMEPICKER2,
			'dogru/js/jsTable.js'
			),
		JS_JQTIMEPICKER	=> array(
			JS_JQUERY_UI,
			'others/jquery/timepicker/jquery.ui.timepicker-tr.js',
			'others/jquery/timepicker/jquery.ui.timepicker.css',
			'others/jquery/timepicker/jquery.ui.timepicker.js'
			),
		JS_JQCONTEXTMENU => array(
			JS_JQUERY_UI,
			'others/jquery/jquery.ui-contextmenu.min.js'
			),
		JS_MONTHPICKER => array(
			JS_JQUERY_UI,
			'others/jquery/jquery.mtz.monthpicker.js'
			),
		JS_PLOT_PIE => array(
			JS_JQUERY,
			'others/jquery/jquery.jqplot.min.css',
			'others/jquery/jquery.jqplot.min.js',
			'others/jquery/jqplot.pieRenderer.min.js'
			),
		JS_BOOTSTRAP => array(
			JS_JQUERY,
			'others/bootstrap/css/bootstrap.min.css',
			'others/bootstrap/js/bootstrap.min.js',
			'others/bootstrap/switch/css/bootstrap-switch.min.css',
			'others/bootstrap/switch/js/bootstrap-switch.min.js',
			'others/bootstrap/bs_select/css/bootstrap-select.min.css',
			'others/bootstrap/bs_select/js/bootstrap-select.min.js',
			'others/bootstrap/bs_validator/validator.min.js',
			JS_JQUERY_UI
		),
		JS_BOOTSTRAP_SWEETALERT => array(
			JS_BOOTSTRAP,
			'others/bootstrap-sweetalert/sweetalert.css',
			'others/bootstrap-sweetalert/sweetalert.min.js'
		),
		JS_DEVELOPER => array(
			'dogru/js/developer.js'
		),
		JS_DBMODEL_FORM => array(
			'dogru/js/dbModelForm.js'
		),
		JS_DBMODEL_LIST => array(
			'dogru/Templates/bases/arama_form.js'
		),
		JS_FONT_AWESOME => array(
			'others/font_awesome/css/font-awesome.min.css',
			'others/font_awesome/css/build.css'
		),
		JS_JQUERY_POPOVER => array(
			JS_JQUERY,
			'others/jquery/popover/jquery.webui-popover.min.css',
			'others/jquery/popover/jquery.webui-popover.min.js'
		),
		JS_SMART_WIZARD => array(
			JS_JQUERY,
			'others/smartwizard/smartWizard.min.js',
			'others/smartwizard/smartWizard.min.css'
		),
		JS_PDFMAKE => array(
			JS_THREAD,
			'others/pdfmake/pdfmake.min.js',
			'others/pdfmake/vfs_fonts.js'
		),
		JS_THREAD => array(
			'others/jsthread/vkthread.min.js'
		),
		JS_REQUIRE => array(
			'others/require/require.min.js'
		),
		JS_SPRINTF => array(
			'others/jquery/sprintf/sprintf.min.js'
		)
	);
}

if(defined('USE_NEW_JQUERY'))
	JsDependency::$Files[JS_JQUERY] = array(
		'others/jquery/jquery_new.js',
		'others/jquery/jquery_migrate.js'
	);

if (file_exists(FullPath('resources.php')))
	require_once FullPath('resources.php');
