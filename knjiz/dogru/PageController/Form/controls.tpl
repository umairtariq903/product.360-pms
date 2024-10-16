<script>
var ignored = ['Type', 'Id'];
function RefreshControls(result)
{
	if (typeof result == 'string')
		FormControls = JSON.parse(result);
	for(var Id in FormControls)
	{
		var control = $('#' + Id);
		var el = FormControls[Id];
		for(var prop in el)
		{
			if (jQuery.inArray(prop, ignored) >= 0)
				continue;
			if (prop == 'Attributes')
				for(var attr in el[prop])
					control.attr(attr, el[prop][attr]);
			else if (prop == 'Style')
				for(var style in el[prop])
					control.css(style, el[prop][style]);
			else if (prop == 'Html')
				control.html( el[prop] );
			else
				control.attr(prop, el[prop]);
		}
	}
}

FormControls = JSON.parse('{Kodlama::JSON($FormControls)}');

$('[server_click]').click(function(){
	for(var Id in FormControls)
	{
		var el = FormControls[Id];
		var control = $('#' + Id);
		for(var prop in el)
		{
			if (jQuery.inArray(prop, ignored) >= 0)
				continue;
			if (prop == 'Attributes')
				for(var attr in el[prop])
					el.Attributes[attr]= control.attr(attr);
			else if (prop == 'Style')
				for(var style in el[prop])
					el.Style[style] = control.css(style);
			else if (prop == 'Html')
				el.Html = control.html( );
			else
				el[prop] = control.attr(prop);
		}
	}
	Page.Ajax.Send($(this).attr('server_click'), FormControls, RefreshControls);
});
</script>