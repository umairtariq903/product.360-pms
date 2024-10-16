{extends 'bases/edit_form2.tpl'}
{block 'DbModelForm'}
	{$DbModelForm->GetTableBS()}
	<div class="form-group form-group-sm">
		<label class="control-label col-sm-3 col-lg-2 text-right" for="PFieldKeys" label_name="PFieldKeys">
			Product Attributes
			<span class="required_field"> * </span>
		</label>
		<div class="col-sm-9 col-lg-10" field_name="PFieldKeys">
			<div id="Attributes"></div>
		</div>
	</div>
{/block}
{block 'Buttons'}
	{if $Data AND $Data->ImportType eq Import::Feed AND $Data->SpKey neq ""}
		<button class="btn btn-danger btn-sm pull-left" onclick="ResetAllProducts('{$Data->Id}','{$Data->SpKey}')">
			Reset All Products ({$Data->SpKey})
		</button>
	{/if}
	<button class="jui-button" icon='ui-icon-disk' onclick="DbModelForm_Save('{$CustomSaveFunc}')">Kaydet</button>
{/block}
