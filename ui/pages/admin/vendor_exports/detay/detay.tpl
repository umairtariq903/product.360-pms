{extends 'bases/edit_form2.tpl'}
{block 'DbModelForm'}

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
			{$DbModelForm->GetTableBS()}
			<div class="button_panel ui-corner-all manuel" style="position: relative; margin-top: 5px">
				<button class="jui-button" icon='ui-icon-disk' onclick="DbModelForm_Save('{$CustomSaveFunc}')">Save</button>
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
					{foreach $Data->RulesInfo as $rule}
						<li>
							<a data-toggle="tab" tab-title="aktif_rule_tab" tab-name="rule_{$rule->Id}" href="#rule_{$rule->Id}">
								<i class="fa fa-filter"></i> {$rule->Name} </a>
							<span class="after"> </span>
						</li>
					{/foreach}
					{*<li>
						<a data-toggle="tab" href="#tab_2-2">
							<i class="fa fa-picture-o"></i> Change Avatar </a>
					</li>
					<li>
						<a data-toggle="tab" href="#tab_3-3">
							<i class="fa fa-lock"></i> Change Password </a>
					</li>
					<li>
						<a data-toggle="tab" href="#tab_4-4">
							<i class="fa fa-eye"></i> Privacity Settings </a>
					</li>*}
				</ul>
			</div>
			<div class="col-md-9">
				<div class="tab-content">
					{foreach $Data->RulesInfo as $rule}
						<div id="rule_{$rule->Id}" class="tab-pane">
							<div class="col-xs-12 rule-detail-content">
								<div class="margin-bottom-10">
									<label>Rule Name : </label>
									<input type="text" id="RuleName_{$rule->Id}" value="{$rule->Name}" />
									{if $rule->Aktif}
										<button class="btn btn-warning btn-xs" title="Pause" onclick="ChangeStatusRule('{$rule->Id}')"><i class="fa fa-pause"></i></button>
									{else}
										<button class="btn btn-success btn-xs" title="Run" onclick="ChangeStatusRule('{$rule->Id}')"><i class="fa fa-play"></i></button>
									{/if}
									<button class="btn btn-danger btn-xs" title="Delete Rule" onclick="DeleteRule('{$rule->Id}')"><i class="fa fa-trash"></i></button>
								</div>
							</div>
							<div class="col-xs-12 filter-list-content">
								<h3>Filters</h3>
								<div class="form-group form-group-sm">
									{*<label class="control-label col-sm-3 col-lg-2 text-right" for="Filters_{$rule->Id}" label_name="Filters_{$rule->Id}">
                                        Filters
                                        <span class="required_field"> * </span>
                                    </label>*}
									{*								<div class="col-sm-9 col-lg-10" field_name="Filters_{$rule->Id}">*}
									<div field_name="Filters_{$rule->Id}">

{*										<div id="FilterRows_{$rule->Id}" class="row margin-bottom-20 flex-center"></div>*}
										<div id="Filters_{$rule->Id}"></div>
									</div>
								</div>
							</div>
							<div class="col-xs-12">
								<label>Exclude : </label>
								<select id="RuleTransaction_{$rule->Id}" rule_id="{$rule->Id}" class="RuleTransactionSelect" style="width: 200px;">
									<option value="0" {if $rule->Transaction eq 0}selected{/if}>No</option>
									<option value="1" {if $rule->Transaction eq 1}selected{/if}>Yes</option>
								</select>
							</div>
							<div class="col-xs-12 transaction-list-content" rule_id="{$rule->Id}">
								<h3>Transactions</h3>
								<div class="form-group form-group-sm">
									<div field_name="Transactions_{$rule->Id}">
										<div id="Transactions_{$rule->Id}"></div>
									</div>
								</div>
							</div>
							<div class="col-xs-12 summary-content">
								<div class="col-xs-6 margin-bottom-20 text-left rule-summary-{$rule->Id}">
									<span class="total-count"></span>
									<br/><span class="before-count"></span>
									<br/><span class="after-count"></span>
								</div>
								<div class="col-xs-6 margin-bottom-20 text-right">
									<button class="btn btn-success btn-sm" onclick="SaveRule('{$rule->Id}',0)">
										<i class="fa fa-save"></i> Save
									</button>
									<button class="btn btn-danger btn-sm" onclick="SaveRule('{$rule->Id}',1)">
										<i class="fa fa-suitcase"></i> Save & Summary
									</button>
								</div>
							</div>
						</div>
					{/foreach}
					{*<div id="tab_2-2" class="tab-pane">
					</div>
					<div id="tab_3-3" class="tab-pane">
					</div>
					<div id="tab_4-4" class="tab-pane">
					</div>*}
				</div>
			</div>
		</div>
	</div>
{/block}
{block 'Buttons'}
{/block}
