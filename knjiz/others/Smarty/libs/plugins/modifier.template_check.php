<?php

function smarty_modifier_template_check ($template)
{
	if(LibLoader::IsLoaded(LIB_TEMA))
	{
		$template = str_replace(App::$Klasor, '', $template);
		return Tema::TemplateCheck($template);
	}
	return $template;
}
