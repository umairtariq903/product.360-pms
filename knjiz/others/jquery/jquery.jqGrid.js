function jqGridInclude()
{
    var pathtojsfiles = "js/"; // need to be ajusted
    // set include to false if you do not want some modules to be included
    var combineIntoOne = false;
    var combinedInclude = new Array();
    var combinedIncludeURL = "combine.php?type=javascript&files=";
    var minver = false;
    var modules = [
        { include: true, incfile:'grid.locale-tr.js',minfile: ''}, // jqGrid translation
        { include: true, incfile:'grid.pack.js',minfile: ''}  // jqGrid all packecd
    ];
    var filename;
    for(var i=0;i<modules.length; i++)
    {
        if(modules[i].include === true) {
        	
        	if (minver !== true) filename = pathtojsfiles+modules[i].incfile;
        	else filename = pathtojsfiles+modules[i].minfile;
        	if (combineIntoOne !== true) {
        		if(jQuery.browser.safari || jQuery.browser.msie ) {
        			jQuery.ajax({url:filename,dataType:'script', async:false, cache: true});
        		} else {
        			IncludeJavaScript(filename);
        		}
        	} else {
        		combinedInclude[combinedInclude.length] = filename;
            }
        }
    }
	if ((combineIntoOne === true) && (combinedInclude.length>0) ) {
		var fileList = implode(",",combinedInclude);
		IncludeJavaScript(combinedIncludeURL+fileList);
	}
	function implode( glue, pieces ) {
    // http://kevin.vanzonneveld.net
    //original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    //returns 1: 'Kevin van Zonneveld'
		return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
    };
    
    function IncludeJavaScript(jsFile)
    {
        var oHead = document.getElementsByTagName('head')[0];
        var oScript = document.createElement('script');
        oScript.type = 'text/javascript';
        oScript.src = jsFile;
        oHead.appendChild(oScript);        
    };
};
jqGridInclude();