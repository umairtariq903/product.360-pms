<div class="col-md-12 clearfix" style="margin: 20px 0;">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<b><i class="fa fa-warning"></i> Sayfa Bulunamadı</b>
		</div>
		<div class="panel-body" style="font-size: 14px; color: black;">
			<div class='margin-bottom' style="text-align: center;">
				<p  style='display: inline-block; margin-bottom: 0;'>
					<span style="display: block;">Anasayfaya yönlendiriliyorsunuz.</span>
					<span style="display: block;">Otomatik yönlendirilmezseniz</span>
					<span style="display: block;"><a href="{$SITE_URL}" style="color: black; font-weight: bold;">buraya tıklayınız.</a></span>
					<span class="geri-sayim" style="font-weight: bold; font-size: 1.5em;">5</span>
				</p>
			</div>
		</div>
	</div>
</div>
<script>
	var sn = 5;
	var interval = setInterval(function () {
		sn -=1;
		if (sn == 0)
		{
			clearInterval(interval);
			Page.Load(SITE_URL);
		}
		else
			$(".geri-sayim").html(sn);
		// Page.Load(SITE_URL);
	},1000);
</script>
