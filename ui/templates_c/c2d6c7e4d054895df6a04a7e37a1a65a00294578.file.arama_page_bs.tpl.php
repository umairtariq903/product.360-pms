<?php /* Smarty version Smarty-3.1.12, created on 2024-10-14 02:33:35
         compiled from "/var/www/vhosts/product.360-pms.com/httpdocs/knjiz/dogru/Templates/bases/arama_page_bs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1094130436670c66df995f44-36203536%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c2d6c7e4d054895df6a04a7e37a1a65a00294578' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/knjiz/dogru/Templates/bases/arama_page_bs.tpl',
      1 => 1718483531,
      2 => 'file',
    ),
    'd8867f897e3b44042d0d2c1bcee65e4029ce1111' => 
    array (
      0 => '/var/www/vhosts/product.360-pms.com/httpdocs/knjiz/dogru/Templates/bases/arama_form_bs.tpl',
      1 => 1718483531,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1094130436670c66df995f44-36203536',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'DtColumns' => 0,
    'c' => 0,
    'HideSearch' => 0,
    'Page' => 0,
    'dt' => 0,
    'row' => 0,
    'tpl' => 0,
    'DbModelName' => 0,
    'col' => 0,
    'val' => 0,
    'text' => 0,
    'link' => 0,
    'class' => 0,
    'tb_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_670c66df9c8398_63608583',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_670c66df9c8398_63608583')) {function content_670c66df9c8398_63608583($_smarty_tpl) {?><style>
	table.arama td{
		white-space: nowrap;
		padding: 0px 5px;
	}
	.tbl-query .td_input_data INPUT,
	.tbl-query .td_input_data SELECT {
		width: 100%;
	}
	span.kriter {
		display: inline-block;
		float:left;
		padding: 3px;
	}
	span.kriter span{
		display: table-cell;
		padding: 2px;
		margin: 0px;
		white-space: nowrap;
	}

	TD.cr-pin {
		text-align: center;
		color: lightgrey;
		cursor: pointer;
	}

	TD.cr-pin:hover {
		color: blue;
	}

	TD.cr-pin.pinned {
		color : blue;
	}

	TD.cr-pin.pinned .fa{
		transform: rotate(45deg);
	}

	TABLE.pinned{
		table-layout: fixed;
	}

	TABLE.pinned INPUT, TABLE.pinned SELECT {
		width: 99%;
	}

	TABLE.pinned TD:first-child {
		font-weight: bold;
		text-align: right;
		background-color: #ebf5fd;
	}

	TABLE.pinned TBODY TR TD {
		padding: 3px;
	}

	#advanced_query TD.td_input_data {
		overflow: visible;
	}

	#advanced_query .chosen-container {
		width: 100% !important;
	}

	TABLE.pinned .chosen-container {
		display: block !important;
		width: 99% !important;
	}
</style>

	<?php if ($_smarty_tpl->tpl_vars['Title']->value){?>
	<div class="ers-page-header"><?php echo $_smarty_tpl->tpl_vars['Title']->value;?>
</div>
	<?php }?>

<div id="advanced" style="display: none;">

<?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['DtColumns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value){
$_smarty_tpl->tpl_vars['c']->_loop = true;
?>
<?php if ($_smarty_tpl->tpl_vars['c']->value->Searchable){?>
	<kriter type="<?php echo $_smarty_tpl->tpl_vars['c']->value->SearchType();?>
" label="<?php echo $_smarty_tpl->tpl_vars['c']->value->DisplayName;?>
" name="<?php echo $_smarty_tpl->tpl_vars['c']->value->Name;?>
"
			group="<?php echo $_smarty_tpl->tpl_vars['c']->value->GroupName;?>
" default="<?php echo $_smarty_tpl->tpl_vars['c']->value->Default;?>
" pinned="<?php echo $_smarty_tpl->tpl_vars['c']->value->Pinned;?>
">
		<?php echo $_smarty_tpl->tpl_vars['c']->value->SearchOptionsFull;?>

	</kriter>
<?php }?>
<?php } ?>

</div>
<?php if (!$_smarty_tpl->tpl_vars['HideSearch']->value){?>
<div id="advanced_query" title="Advanced Search Criteria">
	<div class="table-responsive">
		<table width="100%" class="tbl-query tb_input_base">
			<col width="5%">
			<col width="24%">
			<col width="47%">
			<col width="24%">
		</table>
	</div>
</div>



<div id='search-cover-div'>
	<div >
	<table class="pinned" width="100%" cellspacing="1" use_default_button="1" style="display: none;">
		<colgroup>
			<col width="15%">
			<col width="*">
			<col width="20%">
			<col width="130">
		</colgroup>
		<tbody>

		</tbody>
		<tfoot>
		<tr>
			<td>Search</td>
			<td colspan="2">
				<input name="sorgu" label="Içinde %s geçen"	style="width: 99%"
					   onchange="$('TABLE.arama [name=sorgu]').val(this.value);"
					   value="<?php echo $_GET['sorgu'];?>
" type="text" title="Aradığınız kelimeyi giriniz"/>
			</td>
			<td class="buttons arama_button">
			</td>
		</tr>
		</tfoot>
	</table>
	</div>
	<table class="arama" style="width: 100%; display: none;" use_default_button="1">
		<tr>
			<td width="1"></td>
			<td width="20">Search :</td>
			<td>
				<input name="sorgu" label="Içinde %s geçen"	style="width: 99%"
					   value="<?php echo $_GET['sorgu'];?>
" type="text" title="Aradığınız kelimeyi giriniz"/>
			</td>
			<td width="130" class="buttons arama_button">
			</td>
		</tr>
	</table>
</div>

<div class="kriter_templates">
<div id="kriter_sablon" style="display: none;">
	<span class="kriter">
	<div style="ui-widget">
		<div name="" class="ui-state-highlight ui-corner-all"  style="cursor: pointer">
			<span class="label" style="font-weight: bold; color: blue; font-size: 1.1em"></span>
			<span class="operator_l" style="font-weight: bold; color: green; font-size: 1.1em"></span>
			<span class="value" style="color:darkred"></span>
			<span class="operator_r" style="font-weight: bold; color: green; font-size: 1.1em"></span>
			<span class="kriter-close">
			<button class="jui-button" toolbar="1" icon="ui-icon-close" >Kapat</button>
			</span>
		</div>
	</div>
	</span>
</div>
<div id="kriterler_dialog"
	 style="display: none; position: relative; margin:10px 0px; padding: 5px; font-size: 0.9em">
</div>
<div id="kriterler" title="Search Criteria" style="font-size: 0.9em">
</div>
<div id="kriter_kaydet" title="Aramayı yeni sayfa olarak kaydet" style="display: none;">
	Bu sayfadaki arama sorgusunu yeni bir sayfa olarak kaydederek, bu aramaya daha hızlı erişebilirsiniz.
	Lütfen yeni sayfa için bir isim veriniz (maksimum 20 karakter):
	<div style="margin:15px 0px;">
		<input type="text" style="width: 100%;" maxlength="20" class="TabName" placeholder="Arama sayfasının adı...">
	</div>
</div>
</div>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['dt'])) {$_smarty_tpl->tpl_vars['dt'] = clone $_smarty_tpl->tpl_vars['dt'];
$_smarty_tpl->tpl_vars['dt']->value = reset($_smarty_tpl->tpl_vars['Page']->value->DataTables); $_smarty_tpl->tpl_vars['dt']->nocache = null; $_smarty_tpl->tpl_vars['dt']->scope = 0;
} else $_smarty_tpl->tpl_vars['dt'] = new Smarty_variable(reset($_smarty_tpl->tpl_vars['Page']->value->DataTables), null, 0);?>
<?php if ($_smarty_tpl->tpl_vars['dt']->value->StaticGrid){?>
	

	<?php if ($_smarty_tpl->tpl_vars['Page']->value->StaticRowTemplate!=''){?>
		<div style="text-align: right; margin:5px 0px; font-weight: bold;">
			Toplam <?php echo $_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->RecordCount;?>
 kayıt
		</div>
		<div class="clearfix">
		<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dt']->value->Data; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
			<?php $_smarty_tpl->tpl_vars["tpl"] = new Smarty_variable($_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['Page']->value->GetPageFileUrl($_smarty_tpl->tpl_vars['Page']->value->StaticRowTemplate), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('mode'=>"static",'DataRow'=>$_smarty_tpl->tpl_vars['row']->value), 0));?>

			<?php echo PageController::ParseTemplate($_smarty_tpl->tpl_vars['row']->value,$_smarty_tpl->tpl_vars['dt']->value->Columns,get_class($_smarty_tpl->tpl_vars['dt']->value->ModelDb),$_smarty_tpl->tpl_vars['tpl']->value);?>

		<?php } ?>
		</div>
	<?php }else{ ?>
	<table id="<?php echo $_smarty_tpl->tpl_vars['DbModelName']->value;?>
">
		<thead>
			<tr>
				<?php  $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['col']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dt']->value->Columns; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['col']->key => $_smarty_tpl->tpl_vars['col']->value){
$_smarty_tpl->tpl_vars['col']->_loop = true;
?>
					<td><?php echo $_smarty_tpl->tpl_vars['col']->value->Name;?>
</td>
				<?php } ?>
			</tr>
		</thead>
		<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dt']->value->Data; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
		<tr>
			<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value){
$_smarty_tpl->tpl_vars['val']->_loop = true;
?>
			<td><?php echo $_smarty_tpl->tpl_vars['val']->value;?>
</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</table>
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->PageCount>1){?>
	<div style="text-align: center;">
		<ul class="pagination">
			<?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_smarty_tpl->tpl_vars['text'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['Page']->value->GetBSPagingLinks(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
 $_smarty_tpl->tpl_vars['text']->value = $_smarty_tpl->tpl_vars['link']->key;
?>
			<?php if (isset($_smarty_tpl->tpl_vars['class'])) {$_smarty_tpl->tpl_vars['class'] = clone $_smarty_tpl->tpl_vars['class'];
$_smarty_tpl->tpl_vars['class']->value = ''; $_smarty_tpl->tpl_vars['class']->nocache = null; $_smarty_tpl->tpl_vars['class']->scope = 0;
} else $_smarty_tpl->tpl_vars['class'] = new Smarty_variable('', null, 0);?>
			<?php if ($_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->PageNo==$_smarty_tpl->tpl_vars['text']->value){?>
				<?php if (isset($_smarty_tpl->tpl_vars['class'])) {$_smarty_tpl->tpl_vars['class'] = clone $_smarty_tpl->tpl_vars['class'];
$_smarty_tpl->tpl_vars['class']->value = "active"; $_smarty_tpl->tpl_vars['class']->nocache = null; $_smarty_tpl->tpl_vars['class']->scope = 0;
} else $_smarty_tpl->tpl_vars['class'] = new Smarty_variable("active", null, 0);?>
			<?php }elseif($_smarty_tpl->tpl_vars['link']->value==''){?>
				<?php if (isset($_smarty_tpl->tpl_vars['class'])) {$_smarty_tpl->tpl_vars['class'] = clone $_smarty_tpl->tpl_vars['class'];
$_smarty_tpl->tpl_vars['class']->value = "disabled"; $_smarty_tpl->tpl_vars['class']->nocache = null; $_smarty_tpl->tpl_vars['class']->scope = 0;
} else $_smarty_tpl->tpl_vars['class'] = new Smarty_variable("disabled", null, 0);?>
			<?php }?>
			<li class="page-item <?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
				<a class="page-link" href="<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['text']->value;?>
</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php }?>
	
<?php }else{ ?>
	
<?php if (isset($_smarty_tpl->tpl_vars['dt'])) {$_smarty_tpl->tpl_vars['dt'] = clone $_smarty_tpl->tpl_vars['dt'];
$_smarty_tpl->tpl_vars['dt']->value = reset($_smarty_tpl->tpl_vars['Page']->value->DataTables); $_smarty_tpl->tpl_vars['dt']->nocache = null; $_smarty_tpl->tpl_vars['dt']->scope = 0;
} else $_smarty_tpl->tpl_vars['dt'] = new Smarty_variable(reset($_smarty_tpl->tpl_vars['Page']->value->DataTables), null, 0);?>
<?php if ($_smarty_tpl->tpl_vars['dt']->value->StaticGrid){?>
	<table id="<?php echo $_smarty_tpl->tpl_vars['DbModelName']->value;?>
">
		<thead>
			<tr>
				<?php  $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['col']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dt']->value->Columns; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['col']->key => $_smarty_tpl->tpl_vars['col']->value){
$_smarty_tpl->tpl_vars['col']->_loop = true;
?>
					<td><?php echo $_smarty_tpl->tpl_vars['col']->value->Name;?>
</td>
				<?php } ?>
			</tr>
		</thead>
		<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dt']->value->Data; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
		<tr>
			<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value){
$_smarty_tpl->tpl_vars['val']->_loop = true;
?>
			<td><?php echo $_smarty_tpl->tpl_vars['val']->value;?>
</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</table>
	<?php if (isset($_smarty_tpl->tpl_vars['pageSize'])) {$_smarty_tpl->tpl_vars['pageSize'] = clone $_smarty_tpl->tpl_vars['pageSize'];
$_smarty_tpl->tpl_vars['pageSize']->value = $_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->PageSize; $_smarty_tpl->tpl_vars['pageSize']->nocache = null; $_smarty_tpl->tpl_vars['pageSize']->scope = 0;
} else $_smarty_tpl->tpl_vars['pageSize'] = new Smarty_variable($_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->PageSize, null, 0);?>
	<?php if (isset($_smarty_tpl->tpl_vars['start'])) {$_smarty_tpl->tpl_vars['start'] = clone $_smarty_tpl->tpl_vars['start'];
$_smarty_tpl->tpl_vars['start']->value = ($_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->PageNo-1)*$_smarty_tpl->tpl_vars['pageSize']->value; $_smarty_tpl->tpl_vars['start']->nocache = null; $_smarty_tpl->tpl_vars['start']->scope = 0;
} else $_smarty_tpl->tpl_vars['start'] = new Smarty_variable(($_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->PageNo-1)*$_smarty_tpl->tpl_vars['pageSize']->value, null, 0);?>
	<?php if (isset($_smarty_tpl->tpl_vars['finish'])) {$_smarty_tpl->tpl_vars['finish'] = clone $_smarty_tpl->tpl_vars['finish'];
$_smarty_tpl->tpl_vars['finish']->value = $_smarty_tpl->tpl_vars['start']->value+$_smarty_tpl->tpl_vars['pageSize']->value; $_smarty_tpl->tpl_vars['finish']->nocache = null; $_smarty_tpl->tpl_vars['finish']->scope = 0;
} else $_smarty_tpl->tpl_vars['finish'] = new Smarty_variable($_smarty_tpl->tpl_vars['start']->value+$_smarty_tpl->tpl_vars['pageSize']->value, null, 0);?>
	<?php if ($_smarty_tpl->tpl_vars['finish']->value>$_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->RecordCount){?>
		<?php if (isset($_smarty_tpl->tpl_vars['finish'])) {$_smarty_tpl->tpl_vars['finish'] = clone $_smarty_tpl->tpl_vars['finish'];
$_smarty_tpl->tpl_vars['finish']->value = $_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->RecordCount; $_smarty_tpl->tpl_vars['finish']->nocache = null; $_smarty_tpl->tpl_vars['finish']->scope = 0;
} else $_smarty_tpl->tpl_vars['finish'] = new Smarty_variable($_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->RecordCount, null, 0);?>
	<?php }?>
	<div class="ui-widget-header ui-corner-bottom">
		<table width="100%">
			<tr>
				<td>
					Toplam <?php echo $_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->RecordCount;?>
 kayıttan,
						<?php echo $_smarty_tpl->tpl_vars['start']->value+1;?>
 ile <?php echo $_smarty_tpl->tpl_vars['finish']->value;?>
 arası gösteriliyor
				</td>
				<td align="right">
					<?php if (isset($_smarty_tpl->tpl_vars['var'])) {$_smarty_tpl->tpl_vars['var'] = clone $_smarty_tpl->tpl_vars['var'];
$_smarty_tpl->tpl_vars['var']->value = "iDisplayStart"; $_smarty_tpl->tpl_vars['var']->nocache = null; $_smarty_tpl->tpl_vars['var']->scope = 0;
} else $_smarty_tpl->tpl_vars['var'] = new Smarty_variable("iDisplayStart", null, 0);?>
					<?php if (isset($_smarty_tpl->tpl_vars['url'])) {$_smarty_tpl->tpl_vars['url'] = clone $_smarty_tpl->tpl_vars['url'];
$_smarty_tpl->tpl_vars['url']->value = PagedData::SayfaUrlVer($_smarty_tpl->tpl_vars['var']->value); $_smarty_tpl->tpl_vars['url']->nocache = null; $_smarty_tpl->tpl_vars['url']->scope = 0;
} else $_smarty_tpl->tpl_vars['url'] = new Smarty_variable(PagedData::SayfaUrlVer($_smarty_tpl->tpl_vars['var']->value), null, 0);?>
					<?php if (isset($_smarty_tpl->tpl_vars['prevStart'])) {$_smarty_tpl->tpl_vars['prevStart'] = clone $_smarty_tpl->tpl_vars['prevStart'];
$_smarty_tpl->tpl_vars['prevStart']->value = $_smarty_tpl->tpl_vars['start']->value-$_smarty_tpl->tpl_vars['pageSize']->value; $_smarty_tpl->tpl_vars['prevStart']->nocache = null; $_smarty_tpl->tpl_vars['prevStart']->scope = 0;
} else $_smarty_tpl->tpl_vars['prevStart'] = new Smarty_variable($_smarty_tpl->tpl_vars['start']->value-$_smarty_tpl->tpl_vars['pageSize']->value, null, 0);?>
					<?php if (isset($_smarty_tpl->tpl_vars['nextStart'])) {$_smarty_tpl->tpl_vars['nextStart'] = clone $_smarty_tpl->tpl_vars['nextStart'];
$_smarty_tpl->tpl_vars['nextStart']->value = $_smarty_tpl->tpl_vars['finish']->value; $_smarty_tpl->tpl_vars['nextStart']->nocache = null; $_smarty_tpl->tpl_vars['nextStart']->scope = 0;
} else $_smarty_tpl->tpl_vars['nextStart'] = new Smarty_variable($_smarty_tpl->tpl_vars['finish']->value, null, 0);?>
					<?php if ($_smarty_tpl->tpl_vars['prevStart']->value<0){?>
						<button disabled class="jui-button" icon="ui-icon-seek-prev">
							Önceki sayfa
						</button>
					<?php }else{ ?>
						<a class="jui-button" icon="ui-icon-seek-prev"
						   href="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['var']->value;?>
=<?php echo $_smarty_tpl->tpl_vars['prevStart']->value;?>
">Önceki sayfa</a>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['nextStart']->value>=$_smarty_tpl->tpl_vars['dt']->value->DataPageInfo->RecordCount){?>
						<button disabled class="jui-button" icon="ui-icon-seek-next">
							Sonraki sayfa
						</button>
					<?php }else{ ?>
						<a class="jui-button" icon="ui-icon-seek-next"
						   href="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['var']->value;?>
=<?php echo $_smarty_tpl->tpl_vars['nextStart']->value;?>
">Sonraki sayfa</a>
					<?php }?>
				</td>
			</tr>
		</table>
	</div>
<?php }else{ ?>
	<table id="<?php echo $_smarty_tpl->tpl_vars['DbModelName']->value;?>
"></table>
<?php }?>

<?php }?>
<?php }} ?>