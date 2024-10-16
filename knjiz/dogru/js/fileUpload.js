/* global Page, Table, Jui */
var fileUploadIdGen = 1;

var UploadTypeSingle = 1;
var UploadTypeMulti	 = 2;
var UploadTypeImage	 = 3;
var UploadTypeImageMulti = 4;

var SingleFile = {
	Init: function(obj){
		$(obj).addClass('init');
		if (obj.find('SPAN.fileinput-button').length == 0)
			AddButton(obj, 'Upload File', '');
		var inp = $(obj).find('INPUT[type=file]').first();
		var tbl = $(obj).find('DIV.tbl-file-upload').first();
		var btn = $(tbl).find('BUTTON.btn-del-upload');
		btn.addClass('btn btn-sm btn-danger');
		if (btn.find('i.fa').length == 0)
			$('<i class="fa fa-remove">').css('padding-right', '5px').prependTo(btn);
		var title = $(obj).attr('title');
		if(title)
			$(obj).before($('<div class="ui-state-highlight" style="white-space:normal; padding:3px">').html(title));
		if (!inp.attr('file_name'))
			tbl.hide();
		else
			$(obj).find('.upload').hide();
		btn.click(function(e){
			if (e.originalEvent && !confirm('Dosyayı silmek istiyor musunuz?'))
				return;
			obj.find('.info').html('');
			$(this).closest('DIV.tbl-file-upload').hide();
			$(obj).find('.upload').show();
			$(obj).find('INPUT[type="file"]')
				.attr('file_name', '')
				.attr('file_url', '');
			tbl.change();
		});
	},
	UploadComplete : function(id, obj, file){
		var parts = id.split('|');
		if (parts.length > 1)
			id = 'DIV[parent_id="' + parts[1] + '"] #' + parts[0];
		else
			id = '#' + id;
		var tbl = $(id);
		var btnDel = $(tbl).find('BUTTON.btn-del-upload');
		if(!file)
			file = {};
		var fName = file.Ad;
		var fUrl = file.Link || file.Yol;
		var fId = file.Id || 0;
		if (!file || !fName)
			return btnDel.click();
		$(tbl).find('DIV.tbl-file-upload').show();
		$(tbl).find('.info').html("<a href='javascript:void(0)' " +
			"onclick='Page.Download(\"" + fUrl + "\")' " +
			">" + fName + '</a>');
		$(tbl).find('.upload').hide();
		$(tbl).find('INPUT[type="file"]')
			.attr('file_name', fName)
			.attr('file_url', file.Yol)
			.attr('file_id', fId);
		tbl.change();
	},
	GetData : function(obj){
		var inp = $(obj).find('INPUT[type="file"]').first();
		return {
			Id: $(inp).attr('file_id'),
			Ad: $(inp).attr('file_name'),
			Yol: $(inp).attr('file_url')};
	}
};

var MultiFile = {
	Init: function(div){
		div.css('padding', '5px');
		AddButton(div, 'Dosya ekle', '');
		var tbl = $(div).find('TABLE.tbl-file-upload');
		$(tbl).find('TR.template-row').hide();
		var files = div.find('INPUT.app-file-list').val();
		if(files)
		{
			files = JSON.parse(files);
			for(var i=0; i<files.length; i++)
				AddFileToList(div.attr('id'), files[i]);
		}
		ReorderRows(tbl);
	},
	UploadComplete : function(id, obj, file){
		var o = {Aktif:1, Ad: file.Ad, Yol: file.Yol, Link: file.Link,
			Aciklama: file.Aciklama || '',
			Kategori: file.Kategori || '',
			Id: file.Id || 0};
		AddFileToList(id, o);
	},
	GetData : function(obj){
		var files = $(obj).find('TR.file[data]');
		var list = [];
		for(var k=0; k<files.length; k++)
			list[k] = JSON.parse(files.eq(k).attr('data'));
		return list;
	}
};

var MultiImage = {
	Init: function(div){
		div.css('padding', '5px').addClass('clearfix');
		AddButton(div, 'Resim ekle', '');
		$('<div class="multi-image-gallery clearfix">').appendTo(div);
		var files = div.find('INPUT.app-file-list').val();
		if(files)
		{
			files = JSON.parse(files);
			for(var i=0; i<files.length; i++)
				MultiImage.AddImage(div.attr('id'), files[i]);
			div.find('.fancybox').fancybox( {} );
		}
	},
	UploadComplete : function(id, obj, file){
		var o = {Aktif:1, Ad: file.Ad, Yol: file.Yol, Link: file.Link,
			Aciklama: file.Aciklama || '', Id: file.Id || 0};
		MultiImage.AddImage(id, o);
		if (o.Id <= 0)
		{
			var parent = $('.page_edit_form');
			if (parent.length == 0)
				parent = document;
			$(parent).scrollTo($('#' + id).find('.gallery-item:last'), 200);
		}
	},
	GetData : function(obj){
		var files = $(obj).find('.gallery-item A');
		var list = [];
		for(var k=0; k<files.length; k++){
			var a = files.eq(k);
			var file = {
				Id: a.attr('file_id'),
				Ad : a.attr('file_ad'),
				Aciklama: a.attr('title'),
				Yol: a.attr('href')
			};
			list.push(file);
		}
		return list;
	},
	AddImage: function(id, file){
		var did = '#' + id.replace(/([^\\])\./,'$1\\.');

		var inp = $(did).find('INPUT[type=file]');
		var readOnly = inp.prop('disabled');
		var aspect = inp.attr('aspect');
		if (aspect)
			aspect = parseFloat(aspect);
		else
			aspect = 0;
		if (aspect == 0)
			aspect = 1;
		console.log(aspect);
		var gallery = $(did).find('DIV.multi-image-gallery');
		var d = $('<div class="gallery-item">').appendTo(gallery);
		var a = $('<a class="fancybox">').appendTo(d)
			.attr({rel: 'group_' + id.replace(/\\/,""), file_id: file.Id, file_ad: file.Ad, title: file.Aciklama, href: file.Yol});
		$('<img>').appendTo(a)
			.attr({src: file.Yol, height: 200 * aspect, width: 200, alt: ''});
		if (readOnly)
			$('<div>').html(file.Aciklama || '').appendTo(d);
		else
		{
			$('<input type="text" placeholder="Açıklama...">').appendTo(d)
				.val(file.Aciklama || '')
				.change(function(){
					a.attr('title', this.value);
				});
			$('<button class="btn btn-xs btn-danger remove" title="Delete"><i class="fa fa-times"></i></button>')
				.appendTo(d)
				.click(function(){
					if (! confirm("Resmi silmek istiyor musunuz?"))
						return;
					d.fadeOut(function(){ $(this).remove();});
				});
		}
	}
};

var SingleImage = {
	Init: function(div){
		var img = div.find('IMG');
		var input = div.find('INPUT[type="file"]');
		// Genel düğmeler
		var buttonDiv = $('<div class="image-buttons">').appendTo(div);
		var buttons = [
			{Ad: 'Yükle', Icon: 'newwin', Cls: 'btn-new-upload'},
			{Ad: 'Çek', Icon: 'video', Cls: 'btn-take-upload'},
			{Ad: 'Delete', Icon: 'trash', Cls: 'btn-del-upload'}
		];
		for(var i=0; i<buttons.length; i++)
		{
			$('<button class="jui-button">')
				.addClass(buttons[i].Cls)
				.attr('icon', 'ui-icon-' + buttons[i].Icon)
				.html(buttons[i].Ad)
				.appendTo(buttonDiv);
		}
		Jui.InitButtons(buttonDiv, 1);
		input.appendTo(buttonDiv); // Upload düğmesi
		// Fotoğraf çek kısmı
		var takePhotoDIV = $('#DIV_TakePhoto');
		if (takePhotoDIV.length == 0)
			takePhotoDIV = $('<div id="DIV_TakePhoto">').appendTo('body');
		var takeButtonInDiv = {
			text:'Çek',
			id: 'take',
			class: 'btn-take-in-div',
			click: eval('do_freeze')
		};
		var saveButtonInDiv = {
			text:'Kaydet',
			id: 'upload',
			class: 'btn-save-in-div',
			click: eval('do_upload')
		};
		var resetButtonInDiv = {
			text:'Resmi Sıfırla',
			id: 'vazG',
			class: 'btn-reset-in-div',
			click: eval('do_reset')
		};
		var buttonsBtn = [];
		buttonsBtn[0] = takeButtonInDiv;
		buttonsBtn[1] = saveButtonInDiv;
		buttonsBtn[2] = resetButtonInDiv;
		takePhotoDIV.hide();
		var btn = $(div).find('BUTTON.btn-del-upload')
			.click(function(e){
				if (!confirm('Resmi silmek istiyor musunuz?'))
					return;
				img.attr('src', img.attr('default_src'));
				Jui.DisableButton(this, true);
				Jui.DisableButton($(div).find('BUTTON.btn-take-upload'), false);
				$(div).find('INPUT[type="file"]').attr('file_name', '').attr('file_url', '');
				e.stopPropagation();
			});
		var takeBtn = $(div).find('BUTTON.btn-take-upload')
			.click(function(e){
				webcam.set_api_url( 'index.php?SavePhoto=1' );
				webcam.set_quality( 90 );
				takePhotoDIV.html(webcam.get_html(320, 240));
				webcam.set_hook( 'onComplete', 'my_completion_handler' );
				Page.ShowDialog('DIV_TakePhoto',360,350,buttonsBtn);
				e.stopPropagation();
			});
		var yukleBtn = $(div).find('BUTTON.btn-new-upload')
			.click(function(e){
				$(div).find(".input-upload").click();
				e.stopPropagation();
			});
		if (!div.find('INPUT[type=file]').attr('file_name'))
			Jui.DisableButton(btn, true);
		if (input.attr('file_url'))
			img.attr('src', input.attr('file_url'));
		img.css('cursor', 'pointer').click(function(e){
			var src = $(this).attr('src');
			var def = $(this).attr('default_src');
			if (src != def)
				Page.OpenNewWindow(src, 'preview', 800, 600);
			e.stopPropagation();
		});
	},
	UploadComplete : function(id, obj, file){
		var parts = id.split('|');
		id = parts[0];
		var tbl = $('#' + id);
		Jui.DisableButton($(tbl).find('BUTTON.btn-del-upload'), false);
		$(tbl).find('IMG').attr('src', file.url);
		if(typeof file.Yol == 'undefined')
			file.Yol = file.url;
		$(tbl).find('INPUT[type="file"]')
			.attr('file_name', file.name)
			.attr('file_url', file.Yol)
			.attr('file_id', file.Id);
	},
	GetData : function(obj){
		return SingleFile.GetData(obj);
	}
};

var ChunkedParts = [];

var UploadTypeObj = {};
UploadTypeObj[UploadTypeSingle] = SingleFile;
UploadTypeObj[UploadTypeMulti] = MultiFile;
UploadTypeObj[UploadTypeImage] = SingleImage;
UploadTypeObj[UploadTypeImageMulti] = MultiImage;

$(document).ready(function(){
	InitFileUpload();
});

function AddButton(div, text, size)
{
	var inp = div.find('INPUT[type="file"]');
	if (typeof size == "undefined" || !size)
		size = 'sm';
	size = 'btn-' + size;
	if (! inp.prop('disabled'))
	{
		var span = $('<span class="btn ' + size + ' btn-success fileinput-button upload">' +
			'<i class="fa fa-plus"></i> ' + text + '...' +
			'</span>').prependTo(div);
		inp.appendTo(span);
	}
}

function AddFileToList(id, file)
{
	var tbl = $('#' + id + ' .tbl-file-upload');
	var temp= $(tbl).find('TR.template-row');
	var row = Table.AddNewRow(tbl, temp, file);
	var link = file.Link || file.Yol;
	$(row).addClass('file').find('A.Ad')
		.attr('href', 'javascript:void(0)')
		.attr('onclick', 'Page.Download("' + link + '");');
	$(row).find('BUTTON.btn-del-upload').click(function(){
		if (!confirm('Silmek istediğinize emin misiniz?'))
			return;
		var row = $(this).parents('TR').get(0);
		var tbl = $(row).parents('TABLE').get(0);
		tbl.deleteRow(row.rowIndex);
		ReorderRows(tbl);
	});
	$(row).find('INPUT,SELECT').change(function(){
		var val = $(this).val().replace(/['"]/g, '');
		if (this.type == 'checkbox')
			val = this.checked ? 1 : 0;
		var row = $(this).parents('TR').first();
		var data = JSON.parse($(row).attr('data'));
		data[$(this).attr('field_name')] = val;
		$(row).attr('data', JSON.stringify(data));
	});
	$(row).attr('data', JSON.stringify(file));
	ReorderRows(tbl);
}

function ReorderRows(tbl)
{
	var rows = $(tbl).find('TR.file');
	for(var i=0; i<rows.length; i++)
		rows.eq(i).find('.SiraNo').html(i+1);
}

function InitFileUpload(parent)
{
	if (typeof parent == "undefined")
		parent = 'body';
	$(parent).find('.fileinput-button INPUT[type="file"],INPUT.input-upload:not(.dgr-no-init)').each(function(){
		var input = this;
		if ($(input).attr('file_upload_loaded') == '1')
			return;
		$(input).attr('file_upload_loaded', '1');
		var maxFileSize = $(input).attr('max_file_size');
		if (maxFileSize)
		{
			var matches = maxFileSize.match(/(.*)MB/i);
			if (matches)
				maxFileSize = parseFloat($.trim(matches[1]) * 1024 * 1024);
		}
		if (!maxFileSize)
			maxFileSize = -1;

		var allowedExt = $(input).attr('allowed_ext');
		if (!allowedExt)
			allowedExt = "doc,docx,xls,xlsx,pdf,gif,jpg,png,jpeg";
		var ext = allowedExt.replace(/,/g, '|');
		var Re1 = new RegExp('(\.|\/)(' + ext + ')$', 'i');
		var Re2 = new RegExp(ext, 'i');

		var parent = $(this).closest('DIV[upload_type]');
		var parent_id = $(this).closest('DIV[parent_id]').attr('parent_id');
		var id = parent.attr('id');
		if (! id)
		{
			id = 'fileUpload' + fileUploadIdGen++;
			parent.attr('id', id);
		}
		id = id.replace(/\./,"\\.");
		var uploadType = parseInt(parent.attr('upload_type'));
		UploadTypeObj[uploadType].Init(parent);

		// fileupload nesnesi düzgün yüklenmemişse devam etmeye gerek yok
		if (typeof $.fn.fileupload != "function")
			return;

		$(this).fileupload({
			replaceFileInput : true,
			dataType: 'json',
			singleFileUploads: false,
			maxChunkSize: typeof MaxChunkSize != "undefined" ? MaxChunkSize*1000000 : undefined,
			maxNumberOfFiles: 10,
			url: 'index.php?act=file_upload',
			acceptFileTypes: Re1,
			complete : function(result, textStatus, obj){
				if (ChunkedParts.length != 0)
				{
					var prvFiles = JSON.TryParse(result.responseText);
					if(! prvFiles)
					{
						ChunkedParts = [];
						alert("Dosyalar yüklenirken hata oluştu");
						return false;
					}
					ChunkedParts[ChunkedParts.length - 1].prvFileUrl = prvFiles[0].url;
					if (ChunkedParts[ChunkedParts.length - 1].total == ChunkedParts[ChunkedParts.length - 1].uploadedBytes)
					{
						var cData = {};
						cData.ChunkedParts = [];
						for (var i=0; i < ChunkedParts.length; i++)
							cData.ChunkedParts[i] = ChunkedParts[i].prvFileUrl;
						ChunkedParts = [];
						$.ajax({
							type: "POST",
							cache: false,
							data: cData,
							url: "index.php?act=file_upload",
							dataType: "json",
							success: function (res) {
								// prvFiles[0].Yol = res;
								// prvFiles[0].url = res;

								// result.responseText = JSON.stringify(prvFiles);
								completeFunc(result, textStatus, obj, Re2, maxFileSize, id, input);
							},
							error: function (res, ajaxOptions, thrownError) {
							}
						});
					}
				}
				else
					completeFunc(result, textStatus, obj, Re2, maxFileSize, id, input);
			}, // end complate
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				Page.Loading('yükleniyor[ '
					+ FileSizeStr(data.loaded) + ' / '
					+ FileSizeStr(data.total) + ' ]', progress);
			},
			change : function(e, data){
				if(data.files.length >= 10)
				{
					Page.ShowError("Tek seferde en fazla 10 adet dosya yükleyebilirsiniz.");
					return false;
				}
				var uploadType = $(this).attr('upload_type');
				if ( uploadType == 3 || uploadType == 4)
				{
					CheckResolution(this, data.files);
					e.stopPropagation();
					return false;
				}
			}
		}).on('fileuploadchunkalways', function (e, data) {
			ChunkedParts.push(data);
		});
	});
}
function completeFunc(result, textStatus, obj, Re2, maxFileSize, id, input) {
	Page.CloseProcessingMessage();
	try{
		var prvFiles = JSON.TryParse(result.responseText);
		if(! prvFiles)
		{
			alert("Dosyalar yüklenirken hata oluştu");
			return false;
		}
		var ths = $(this);
		$.each(prvFiles,function(k, r){
			if(typeof r != "object")
			{
				Page.ShowError('HATA\n\n' + r + "\n\n");
				return false;
			}
			var parts = r.name.split('.');
			if (! Re2.test(parts[ parts.length - 1]))
			{
				alert('Lütfen "' + allowedExt + '" uzantılı bir dosya seçiniz');
				return false;
			}
			let newJqFiles = (typeof ths[0].files != "undefined" && typeof ths[0].files == "object") ?
				ths[0].files : ths[0]._FILES;
			if(maxFileSize > 0 && newJqFiles[0].size > maxFileSize)
			{
				alert('Dosya boyutu en fazla ' + FileSizeStr(maxFileSize) + ' olabilir.');
				return false;
			}
			var uploadType = UploadTypeSingle;
			try {
				var parts = id.split('|');
				id = parts[0];
				uploadType = parseInt($('#' + id).attr('upload_type'));
			} catch(uploadException){}

			var obj = UploadTypeObj[uploadType];
			if (typeof obj == "undefined")
				obj = UploadTypeObj[UploadTypeSingle];
			if (typeof parent_id != 'undefined' && parent_id != '')
				id = id + '|' + parent_id;
			obj.UploadComplete(id, input, r);
			if (typeof DGRUploadComplete == "function")
				DGRUploadComplete(id,input,r);
		});
	}
	catch(e){
		Page.ShowError('HATA\n\n' + result.responseText + "\n\n" + e);
	}
}
var _FILES = null;
var _INPUT = null;
function CheckResolution(inp, files)
{
	var file = null, img = null;
	var _URL = window.URL || window.webkitURL;
	_FILES = files;
	_INPUT = inp;
	if ((file = files[0])) {
		var size = $(inp).attr('max_file_size');
		if (size && (matches = size.match(/(.*)MB/i)))
			size = parseInt(matches[1]) * 1024 * 1024;
		else if (size)
			size = parseInt(size);
		else
			size = -1;
		if (size > 0 && size < file.size)
			return Page.ShowError("Yüklenecek dosya en fazla " +
				FileSizeStr(size) + " olabilir<br>" +
				"Yüklenmek istenen dosya " + FileSizeStr(file.size) + " büyüklüğündedir");
		img = new Image();
		img.onload = function () {
			var dims = $(inp).attr('max_resolution');
			var aspect = 0;
			if ($(inp).attr('aspect'))
				aspect = parseFloat($(inp).attr('aspect'));
			if (dims)
			{
				var matches = dims.split('x');
				if (parseInt(matches[0]) < this.width ||
					parseInt(matches[1]) < this.height)
					return Page.ShowError("Yüklenecek resim dosyası " +
						"en fazla " + dims + " boyutunda olabilir<br>" +
						"Yüklenmek istenen resim " + this.width + "x" + this.height +
						" boyutundadır");
			}

			if (aspect != 0)
			{
				var modal = $('#DIV_ImgCropper');
				if (modal.length >= 0)
					modal.remove();
				modal = $(CropperModalHtml).appendTo('body');
				modal.on('shown.bs.modal', function () {
					image = $(this).find('.img-cropped');
					image.cropper({
						aspectRatio: 1./aspect,
						//viewMode: 1,
						dragMode: 'move',
						autoCropArea: 0.65,
						restore: false,
						guides: true,
						highlight: false,
						cropBoxMovable: true
					});
				}).on('hidden.bs.modal', function (e) {
					$(this).find('.img-cropped').cropper('destroy');
				});

				modal.find('.img-cropped').attr('src', $(this).attr('src'));
				modal.modal();
				return false;
			}

			// Resolution veya aspect kontrolü yoksa, default olarak çalışacak:
			$(_INPUT).fileupload('send', {files : _FILES});
		};
		img.src = _URL.createObjectURL(file);
	}
}

function GetFileUploadData(id)
{
	var fileUpload = $('#' + id);
	var type = parseInt(fileUpload.attr('upload_type'));
	return UploadTypeObj[type].GetData(fileUpload);
}

function SetFileUploadData(id, files)
{
	$('#' + id + ' TR.file').remove();

	ReorderRows('#' + id);

	for(var i=0; i<files.length; i++)
		AddFileToList(id, files[i]);
}

function CropAndUpload(btn)
{
	var modal = $('#DIV_ImgCropper');
	var image = modal.find('.img-cropped');
	var cropBoxData = image.cropper('getCropBoxData');
	var canvasData = image.cropper('getCanvasData');
	modal.modal('hide');

	var data = cropBoxData;
	var scale = canvasData.width / canvasData.naturalWidth;
	data.left /= scale;
	data.top /= scale;
	data.width /= scale;
	data.height /= scale;

	data.left -= canvasData.left / scale;
	data.top  -= canvasData.top / scale;
	data.rotate = image.cropper('getData').rotate;

	$(_INPUT).fileupload('send', {
		files : _FILES,
		formData: {
			cropData: JSON.stringify(data)
		}
	});
}

function RotateCroppedImage(deg)
{
	image.cropper('rotate', deg);
}

var CropperModalHtml = [
	'<div class="modal fade" id="DIV_ImgCropper" aria-labelledby="modalLabel" role="dialog" tabindex="-1">',
	'<div class="modal-dialog" role="document">',
	'<div class="modal-content">',
	'<div class="modal-header">',
	'<button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>',
	'<h5 class="modal-title" id="modalLabel">Fotoğrafı Kırp</h5>',
	'</div>',
	'<div class="modal-body">',
	'<div style="height: 400px;">',
	'<img class="img-cropped" src="" alt="Picture">',
	'</div>',
	'</div>',
	'<div class="modal-footer">',
	'<div style="float: left; width: 50%; text-align: left;">',
	'<button class="btn btn-info" title="Sola çevir" onclick="RotateCroppedImage(-45);"><i class="fa fa-undo"></i></button>',
	'<button class="btn btn-info" title="Sağa çevir" onclick="RotateCroppedImage(45);"><i class="fa fa-undo fa-flip-horizontal"></i></button>',
	'</div>',
	'<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>',
	'<button type="button" class="btn btn-success" onclick="CropAndUpload(this);">Ok</button>',
	'</div>',
	'</div>',
	'</div>',
	'</div>'].join("\n");
