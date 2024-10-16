<div class="input-group arama margin-bottom-15" style="width: 100%;" use_default_button="1">
	<input id="aramaTxt" class="form-control ui-widget ui-widget-content ui-corner-all"
		   placeholder="Aramak için bir kelime giriniz" type="text"
		   name='sorgu' value="{$smarty.get.sorgu}">
	<span class="input-group-btn">
		<button class="btn btn-primary pull-right" onclick="Ara()" default_button="1" style="border-radius: 0;">
			<i class="fa fa-search"></i> Search
		</button>
	</span>
	<span class="input-group-btn">
		<button class="btn btn-primary pull-right" onclick="GelismisAraGoster()">
			<i class="fa fa-cog"></i>
		</button>
	</span>
</div>
