<?php /* Smarty version Smarty-3.1.12, created on 2024-10-16 12:00:26
         compiled from "C:\xampp\xampp_7.2\htdocs\product.360-pms\themes\b-metronic\index_user.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1975111971670f8ebaf2afa3-05868932%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e49c4ac35d938099fb254218fd533f48893dcff4' => 
    array (
      0 => 'C:\\xampp\\xampp_7.2\\htdocs\\product.360-pms\\themes\\b-metronic\\index_user.tpl',
      1 => 1728864066,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1975111971670f8ebaf2afa3-05868932',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'girisYapan' => 0,
    'Page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670f8ebb053dc2_67298948',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670f8ebb053dc2_67298948')) {function content_670f8ebb053dc2_67298948($_smarty_tpl) {?>    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <!-- BEGIN HEADER -->
            <div class="page-header navbar navbar-fixed-top">
                <!-- BEGIN HEADER INNER -->
                <div class="page-header-inner ">
                    <!-- BEGIN LOGO -->
                    <div class="page-logo" style="    overflow: hidden;">
                        <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/ozet" style="text-align: center; color: white; font-weight: 700; width: 79%; font-size: 20px; line-height: 50px;">

                            360-PMS
                        </a>
                        <div class="menu-toggler sidebar-toggler">
                            <span></span>
                        </div>
                    </div>
                    <!-- END LOGO -->
                    <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
                    <!-- END RESPONSIVE MENU TOGGLER -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            
                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <img alt="" class="img-circle" src="<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->ResimLink;?>
" />
                                    <span class="username username-hide-on-mobile"> <?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->Email;?>
 </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
                                    <li>
                                        <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/bilgilerim">
                                            <i class="icon-user"></i> Information

                                        </a>

                                    </li>
                                    <li class="divider"> </li>
                                    <li>
                                        <a href="act/admin/cikis">
                                            <i class="icon-key"></i> Sign Out </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- END USER LOGIN DROPDOWN -->
                            <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            
                            <!-- END QUICK SIDEBAR TOGGLER -->
                        </ul>
                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END HEADER INNER -->
            </div>
            <!-- END HEADER -->
            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                <!-- BEGIN SIDEBAR -->
                <div class="page-sidebar-wrapper">
                    <!-- BEGIN SIDEBAR -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <div class="page-sidebar navbar-collapse collapse">
                        <!-- BEGIN SIDEBAR MENU -->
                        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                            <li class="sidebar-toggler-wrapper hide">
                                <div class="sidebar-toggler">
                                    <span></span>
                                </div>
                            </li>
                            
                            <!-- END SIDEBAR TOGGLER BUTTON -->
                            <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
                            
                            <li class="nav-item <?php echo IfTrue($_GET['act2']=="ozet","active open");?>
">
                                <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/ozet" class="nav-link nav-toggle">
                                    <i class="fa fa-dashboard"></i>
                                    <span class="title">Dashboard</span>
                                    <?php if ($_GET['act2']=="katalog"){?>
                                        <span class="selected"></span>
                                    <?php }?>
                                </a>
                            </li>
                            
                            <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="vendor_products","active open");?>
">
                                <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/vendor_products" class="nav-link nav-toggle">
                                    <i class="fa fa-product-hunt"></i>
                                    <span class="title">Products</span>
                                    <?php if ($_GET['act']=="admin"&&$_GET['act2']=="vendor_products"){?>
                                        <span class="selected"></span>
                                    <?php }?>
                                </a>
                            </li>
                            <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="imports","active open");?>
">
                                <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/imports" class="nav-link nav-toggle">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title">Imports</span>
                                    <?php if ($_GET['act']=="admin"&&$_GET['act2']=="imports"){?>
                                        <span class="selected"></span>
                                    <?php }?>
                                </a>
                            </li>
                            <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="import_logs","active open");?>
">
                                <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/import_logs" class="nav-link nav-toggle">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title">Import Logs</span>
                                    <?php if ($_GET['act']=="admin"&&$_GET['act2']=="import_logs"){?>
                                        <span class="selected"></span>
                                    <?php }?>
                                </a>
                            </li>
                            <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="exports","active open");?>
">
                                <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/exports" class="nav-link nav-toggle">
                                    <i class="fa fa-file-text"></i>
                                    <span class="title">Exports</span>
                                    <?php if ($_GET['act']=="admin"&&$_GET['act2']=="exports"){?>
                                        <span class="selected"></span>
                                    <?php }?>
                                </a>
                            </li>
                            <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="update_data","active open");?>
">
                                <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/update_data" class="nav-link nav-toggle">
                                    <i class="fa fa-refresh"></i>
                                    <span class="title">Update Data</span>
                                    <?php if ($_GET['act']=="admin"&&$_GET['act2']=="update_data"){?>
                                        <span class="selected"></span>
                                    <?php }?>
                                </a>
                            </li>
                            <?php if (KisiIsAdmin()){?>
                                <li class="heading">
                                    <h3 class="uppercase">Admin</h3>
                                </li>
                                <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="kullanicilar","active open");?>
">
                                    <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/kullanicilar" class="nav-link nav-toggle">
                                        <i class="fa fa-users"></i>
                                        <span class="title">Users</span>
                                        <?php if ($_GET['act']=="admin"&&$_GET['act2']=="kullanicilar"){?>
                                            <span class="selected"></span>
                                        <?php }?>
                                    </a>
                                </li>
                                <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="companies","active open");?>
">
                                    <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/companies" class="nav-link nav-toggle">
                                        <i class="fa fa-building"></i>
                                        <span class="title">Companies</span>
                                        <?php if ($_GET['act']=="admin"&&$_GET['act2']=="companies"){?>
                                            <span class="selected"></span>
                                        <?php }?>
                                    </a>
                                </li>
                                <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="vendors","active open");?>
">
                                    <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/vendors" class="nav-link nav-toggle">
                                        <i class="fa fa-list"></i>
                                        <span class="title">Vendors</span>
                                        <?php if ($_GET['act']=="admin"&&$_GET['act2']=="vendors"){?>
                                            <span class="selected"></span>
                                        <?php }?>
                                    </a>
                                </li>
                                <li class="nav-item <?php echo IfTrue($_GET['act']=="admin"&&$_GET['act2']=="p_attributes","active open");?>
">
                                    <a href="act/<?php echo $_smarty_tpl->tpl_vars['girisYapan']->value->GetAct();?>
/p_attributes" class="nav-link nav-toggle">
                                        <i class="fa fa-list"></i>
                                        <span class="title">Fields</span>
                                        <?php if ($_GET['act']=="admin"&&$_GET['act2']=="p_attributes"){?>
                                            <span class="selected"></span>
                                        <?php }?>
                                    </a>
                                </li>
                            <?php }?>
                        </ul>
                        <!-- END SIDEBAR MENU -->
                        <!-- END SIDEBAR MENU -->
                    </div>
                    <!-- END SIDEBAR -->
                </div>
                <!-- END SIDEBAR -->
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN THEME PANEL -->
                        
                        <!-- END THEME PANEL -->
                        <!-- BEGIN PAGE BAR -->
                        
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        
                        <h3 style="text-align: center; font-weight: bold; margin-top: 10px !important; color: #d64635;">
                            <?php echo $_smarty_tpl->tpl_vars['Page']->value->Title;?>

                        </h3>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
						<div class="margin-top-15">
							<?php echo $_smarty_tpl->getSubTemplate ("index_main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

						</div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
                <!-- BEGIN QUICK SIDEBAR -->
                <a href="javascript:;" class="page-quick-sidebar-toggler">
                    <i class="icon-login"></i>
                </a>
                <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
                    <div class="page-quick-sidebar">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="javascript:;" data-target="#quick_sidebar_tab_1" data-toggle="tab"> Users
                                    <span class="badge badge-danger">2</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" data-target="#quick_sidebar_tab_2" data-toggle="tab"> Alerts
                                    <span class="badge badge-success">7</span>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"> More
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-bell"></i> Alerts </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-info"></i> Notifications </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-speech"></i> Activities </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                            <i class="icon-settings"></i> Settings </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- END QUICK SIDEBAR -->
            </div>
            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <div class="page-footer" style="text-align: center;">
                <div class="page-footer-inner"> 2024 &copy;
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
        </div>
        <!-- BEGIN QUICK NAV -->
        <div class="quick-nav-overlay"></div>
</body>
<?php }} ?>