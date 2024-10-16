<div style="padding: 5px; text-align: right">
	<button class="jui-button" onclick="window.location.reload();">Yenile</button>
	<button class="jui-button" onclick="ErrTemizle()">Logları Temizle</button>
</div>
<div class="tabber" >
	<div title="DB Log" style="padding:10px; padding-bottom: 30px;" url="act2=">
		{if $smarty.get.act2 neq 'error'}
		<span style="float: right">
			Max. Level
			<select id="max_level" onchange="ChangeMaxLevel();">
				<option>1</option>
				<option {if $debug->MaxLevel eq 2}selected{/if}>2</option>
				<option {if $debug->MaxLevel eq 3}selected{/if}>3</option>
				<option {if $debug->MaxLevel eq 4}selected{/if}>4</option>
				<option {if $debug->MaxLevel eq 5}selected{/if}>5</option>
			</select>
		</span>
		<table id="tb_db_log" onrowclick="LogDetay" width="100%" cellspacing="0" cellpadding="3"></table>
		{/if}
	</div>
	<div title="PHP Errors({count($PhpErrors->List)})" style="padding:10px; padding-bottom: 30px;" url="act2=error">
		{if $smarty.get.act2 eq 'error'}
		<table id="tb_err_log" onrowclick="ErrDetay" width="100%" cellspacing="0" cellpadding="3"></table>
		{/if}
	</div>
</div>
<div id="DIV_LogDetay" style="display: none;" title="Log Detayı">
	<iframe width="100%" height="100%" frameborder="0" border="0"></iframe>
</div>
<div id="DIV_ErrDetay" style="display: none;" title="Php Hata Detayı">
	<iframe width="99%" height="99%" frameborder="0" border="0"></iframe>
</div>
<script>
	function ChangeMaxLevel()
	{
		Page.Ajax.Send('ChangeMaxLevel', $('#max_level').val(), Page.Ajax.REFRESH_NO_MSG);
	}

	function ErrTemizle()
	{
		Page.Ajax.Get('cisc').Send('TmpClear', true, Page.Refresh);
	}

	function LogDetay(id)
	{
		Page.Loading();
		$('#DIV_LogDetay IFRAME').hide()
			.attr('src', GetUrl('cisc.detay','clear')+'&id=' + id)
			.each(function(){
				if ($(this).attr('load_event'))
					return;
				$(this).attr('load_event', 1).load(function(){
					Page.CloseProcessingMessage();
					$('#DIV_LogDetay IFRAME').show();
					$('#DIV_LogDetay').parent().find('.ui-dialog-buttonset BUTTON').first().focus();
				});
			});
		var d = Page.ShowDialog('DIV_LogDetay', '90%', $(window).height() - 50);
		d.dialog('option', 'closeOnEscape', true);
	}

	function ErrDetay(id)
	{
		Page.Loading();
		$('#DIV_ErrDetay IFRAME').hide()
			.attr('src', GetUrl('cisc.detay','clear')+'&t=err&id=' + id)
			.each(function(){
				if ($(this).attr('load_event'))
					return;
				$(this).attr('load_event', 1).load(function(){
					Page.CloseProcessingMessage();
					$('#DIV_ErrDetay IFRAME').show();
					$('#DIV_ErrDetay').parent().find('.ui-dialog-buttonset BUTTON').first().focus();
				});
			});
		var d = Page.ShowDialog('DIV_ErrDetay', '90%', $(window).height() - 50);
		d.dialog('option', 'closeOnEscape', true);
	}

	$(function() {
		Jui.tabs('.tabber');
		$('select, input').click(function(e){
			e.stopPropagation();
		});
		$('[name="tb_db_log_length"]').val(100).change();

		$('#tb_err_log .data_row').click(function (){
			$('TR.selected').removeClass('selected');
			$(this).addClass('selected');
			var index = $(this).attr('index');
			window.top.frames['debug_detay'].location.href = GetUrl('cisc.detay','clear')+'&t=err&i=' + index;
		});
	} );
</script>