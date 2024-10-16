{AddCSS("others/codemirror/codemirror")}
{AddCSS("others/codemirror/neat")}
{AddJS("others/codemirror/codemirror")}
{AddJS("others/codemirror/matchbrackets")}
{AddJS("others/codemirror/htmlmixed")}
{AddJS("others/codemirror/xml")}
{AddJS("others/codemirror/javascript")}
{AddJS("others/codemirror/css")}
{AddJS("others/codemirror/clike")}
{AddJS("others/codemirror/active-line")}
{AddJS("others/codemirror/php")}
<script>
var init = function() {
    code_editor = window.editor = CodeMirror.fromTextArea(document.getElementById('code'), {
        theme: "neat",
		styleActiveLine: true,
		lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift"
    });
};
</script>
<style>
	.CodeMirror {
		height: auto;
	}
	.CodeMirror-scroll {
		overflow-y: hidden;
		overflow-x: auto;
	}
	.CodeMirror-activeline-background {
		background: #C2D1EF !important;
	}
</style>
<textarea id="code" name="code">{$Code}</textarea>
<script>
	$(document).ready(function (){
		init();
		code_editor.setCursor({$Line});
		window.setTimeout(function() {
		   code_editor.addLineClass({$Line}, null, "center-me");
		   line = $('.CodeMirror-lines .center-me');
		   $(window).scrollTop(line.offset().top - $('.CodeMirror-scroll').offset().top  - Math.round($(window).height()/2));
	   }, 200);
	});
</script>