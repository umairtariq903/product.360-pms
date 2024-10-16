<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 02:34:23
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/vendor_exports/detay/detay.tpl" */ ?>
<?php /*%%SmartyHeaderCode:375763510670c670f740359-98408140%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9b3d25e10cf3a4dfb8d96064446a5104c4292a26' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/ui/pages/admin/vendor_exports/detay/detay.tpl',
      1 => 1728274186,
      2 => 'file',
    ),
    '3fa4a2c3f4eefd5413dd312c220cd41ef520d107' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/knjiz/dogru/Templates/bases/edit_form2.tpl',
      1 => 1727293607,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '375763510670c670f740359-98408140',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Page' => 0,
    'DbModelForm' => 0,
    'CustomSaveFunc' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670c670f763535_97636431',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670c670f763535_97636431')) {function content_670c670f763535_97636431($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['Page']->value->Title){?>
	<div class="ers-page-header"><?php echo $_smarty_tpl->tpl_vars['Page']->value->Title;?>
</div>
<?php }?>
<div class="clearfix">
	

	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#genel" tab-title="aktif_general_tab" tab-name="genel" data-toggle="tab"> General </a>
		</li>
		<li>
			<a href="#rules" tab-title="aktif_general_tab" tab-name="rules" data-toggle="tab"> Rules </a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade active in" id="genel">
			<?php echo $_smarty_tpl->tpl_vars['DbModelForm']->value->GetTableBS();?>

			<div class="button_panel ui-corner-all manuel" style="position: relative; margin-top: 5px">
				<button class="jui-button" icon='ui-icon-disk' onclick="DbModelForm_Save('<?php echo $_smarty_tpl->tpl_vars['CustomSaveFunc']->value;?>
')">Save</button>
			</div>
		</div>
		<div class="tab-pane fade" id="rules">
			<div class="col-md-12 margin-bottom-20">
				<button class="btn btn-primary btn-sm" onclick="ShowAddRule()">
					<i class="fa fa-plus"></i>
				</button>
				<div id="AddRuleDiv" style="display: none;" title="Add Rule">
					<label>Rule Name</label>
					<input type="text" id="AddRuleDiv_Name" value=""/>
				</div>
			</div>
			<div class="col-md-3">
				<ul class="ver-inline-menu tabbable margin-bottom-10" id="rules-menu">
					<?php  $_smarty_tpl->tpl_vars['rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rule']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['Data']->value->RulesInfo; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rule']->key => $_smarty_tpl->tpl_vars['rule']->value){
$_smarty_tpl->tpl_vars['rule']->_loop = true;
?>
						<li>
							<a data-toggle="tab" tab-title="aktif_rule_tab" tab-name="rule_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
" href="#rule_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
">
								<i class="fa fa-filter"></i> <?php echo $_smarty_tpl->tpl_vars['rule']->value->Name;?>
 </a>
							<span class="after"> </span>
						</li>
					<?php } ?>
					
				</ul>
			</div>
			<div class="col-md-9">
				<div class="tab-content">
					<?php  $_smarty_tpl->tpl_vars['rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rule']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['Data']->value->RulesInfo; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rule']->key => $_smarty_tpl->tpl_vars['rule']->value){
$_smarty_tpl->tpl_vars['rule']->_loop = true;
?>
						<div id="rule_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
" class="tab-pane">
							<div class="col-xs-12 rule-detail-content">
								<div class="margin-bottom-10">
									<label>Rule Name : </label>
									<input type="text" id="RuleName_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['rule']->value->Name;?>
" />
									<?php if ($_smarty_tpl->tpl_vars['rule']->value->Aktif){?>
										<button class="btn btn-warning btn-xs" title="Pause" onclick="ChangeStatusRule('<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
')"><i class="fa fa-pause"></i></button>
									<?php }else{ ?>
										<button class="btn btn-success btn-xs" title="Run" onclick="ChangeStatusRule('<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
')"><i class="fa fa-play"></i></button>
									<?php }?>
									<button class="btn btn-danger btn-xs" title="Delete Rule" onclick="DeleteRule('<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
')"><i class="fa fa-trash"></i></button>
								</div>
							</div>
							<div class="col-xs-12 filter-list-content">
								<h3>Filters</h3>
								<div class="form-group form-group-sm">
									
									
									<div field_name="Filters_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
">


										<div id="Filters_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
"></div>
									</div>
								</div>
							</div>
							<div class="col-xs-12">
								<label>Exclude : </label>
								<select id="RuleTransaction_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
" rule_id="<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
" class="RuleTransactionSelect" style="width: 200px;">
									<option value="0" <?php if ($_smarty_tpl->tpl_vars['rule']->value->Transaction==0){?>selected<?php }?>>No</option>
									<option value="1" <?php if ($_smarty_tpl->tpl_vars['rule']->value->Transaction==1){?>selected<?php }?>>Yes</option>
								</select>
							</div>
							<div class="col-xs-12 transaction-list-content" rule_id="<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
">
								<h3>Transactions</h3>
								<div class="form-group form-group-sm">
									<div field_name="Transactions_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
">
										<div id="Transactions_<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
"></div>
									</div>
								</div>
							</div>
							<div class="col-xs-12 summary-content">
								<div class="col-xs-6 margin-bottom-20 text-left rule-summary-<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
">
									<span class="total-count"></span>
									<br/><span class="before-count"></span>
									<br/><span class="after-count"></span>
								</div>
								<div class="col-xs-6 margin-bottom-20 text-right">
									<button class="btn btn-success btn-sm" onclick="SaveRule('<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
',0)">
										<i class="fa fa-save"></i> Save
									</button>
									<button class="btn btn-danger btn-sm" onclick="SaveRule('<?php echo $_smarty_tpl->tpl_vars['rule']->value->Id;?>
',1)">
										<i class="fa fa-suitcase"></i> Save & Summary
									</button>
								</div>
							</div>
						</div>
					<?php } ?>
					
				</div>
			</div>
		</div>
	</div>

</div>
<div class="button_panel ui-corner-all" style="position: relative; margin-top: 5px">
	

</div>
<?php }} ?>