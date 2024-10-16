
		var AppInbox = function () {

		var content = $('.inbox-content');

		var loadInbox = function (el) {
			var title = el.find('.sp-title').html();
			var type  = el.attr('data-type');

			App.blockUI({
				target: content,
				overlayColor: 'none',
				animate: true
			});

			toggleButton(el);
			if(typeof inboxCurrentPage == "undefined")
				inboxCurrentPage = 1;

			var obj = new Object();
			obj.type = type;
			obj.page = inboxCurrentPage;

			//Arama yapılmışsa onada bak
			var searchInput = $('.inbox-header input.search-input');
			if(searchInput.val() != "")
				obj.searchText = searchInput.val();

			Page.Ajax.Send("IcerikGetir",obj,function(res){
					toggleButton(el);

					App.unblockUI('.inbox-content');

					$('.inbox-nav > li.active').removeClass('active');
					el.closest('li').addClass('active');
					$('.inbox-header > h1').html(title);

					content.html(res);
					$('[data-title]').tooltip();

					// handle view message
					$('.inbox .pagination-control').on('click', 'a', function () {
						if(typeof inboxCurrentPage == "undefined")
							inboxCurrentPage = 1;
						if($(this).hasClass("btn-next"))
							inboxCurrentPage = inboxCurrentPage+1;
						else
							inboxCurrentPage = inboxCurrentPage-1;
						if(inboxCurrentPage <= 0)
							inboxCurrentPage = 1;
						var el = $(".inbox .inbox-sidebar li[class='active'] a");
						loadInbox(el.eq(0));
					});

					if (Layout.fixContentHeight) {
						Layout.fixContentHeight();
					}
			},"");

			// handle group checkbox:
			jQuery('body').on('change', '.mail-group-checkbox', function () {
				var set = jQuery('.mail-checkbox');
				var checked = jQuery(this).is(":checked");
				jQuery(set).each(function () {
					$(this).prop('checked', checked);
				});
			});
		}

		var loadMessage = function (el, name, resetMenu) {
			App.blockUI({
				target: content,
				overlayColor: 'none',
				animate: true
			});

			toggleButton(el);

			var message_id = el.parent('tr').attr("data-messageid");

			Page.Ajax.Send("MesajIcerikGetir",message_id,function(res){
				App.unblockUI(content);

				toggleButton(el);

				if (resetMenu) {
					$('.inbox-nav > li.active').removeClass('active');
				}
				$('.inbox-header > h1').text('Mesaj İçeriği');

				content.html(res);
				var unreadCountSpan = $('.inbox a[data-type="inbox"]').find("span");
				var unreadCount		= parseInt(content.find(".UnreadCount").val());
				var deletedCountSpan= $('.inbox a[data-type="trash"]').find("span");
				var deletedCount	= parseInt(content.find(".DeletedCount").val());

				unreadCountSpan.html("");
				deletedCountSpan.html("");
				if(unreadCount > 0)
					unreadCountSpan.html(unreadCount);
				if(deletedCount > 0)
					deletedCountSpan.html(deletedCount);

				Layout.fixContentHeight();
			},"");
		}

		var initWysihtml5 = function () {
			$('.inbox-wysihtml5').wysihtml5({
				"stylesheets": ["../assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
			});
		}

		var initFileupload = function () {

			$('#fileupload').fileupload({
				// Uncomment the following to send cross-domain cookies:
				//xhrFields: {withCredentials: true},
				url: '../assets/global/plugins/jquery-file-upload/server/php/',
				autoUpload: true
			});

			// Upload server status check for browsers with CORS support:
			if ($.support.cors) {
				$.ajax({
					url: '../assets/global/plugins/jquery-file-upload/server/php/',
					type: 'HEAD'
				}).fail(function () {
					$('<span class="alert alert-error"/>')
						.text('Upload server currently unavailable - ' +
						new Date())
						.appendTo('#fileupload');
				});
			}
		}

		var loadCompose = function (el) {
			var url = 'app_inbox_compose.html';

			App.blockUI({
				target: content,
				overlayColor: 'none',
				animate: true
			});

			toggleButton(el);

			// load the form via ajax
			$.ajax({
				type: "GET",
				cache: false,
				url: url,
				dataType: "html",
				success: function(res)
				{
					App.unblockUI(content);
					toggleButton(el);

					$('.inbox-nav > li.active').removeClass('active');
					$('.inbox-header > h1').text('Compose');

					content.html(res);

					initFileupload();
					initWysihtml5();

					$('.inbox-wysihtml5').focus();
					Layout.fixContentHeight();
				},
				error: function(xhr, ajaxOptions, thrownError)
				{
					toggleButton(el);
				},
				async: false
			});
		}

		var loadReply = function (el) {
			var messageid = $(el).attr("data-messageid");

			App.blockUI({
				target: content,
				overlayColor: 'none',
				animate: true
			});

			toggleButton(el);

			Page.Ajax.Send("MesajCevapIcerikGetir",messageid,function(res){
				App.unblockUI(content);
				toggleButton(el);

				$('.inbox-nav > li.active').removeClass('active');
				$('.inbox-header > h1').text('Cevapla');

				content.html(res);
				$('[name="message"]').val($('#reply_email_content_body').html());

				handleCCInput(); // init "CC" input field

//				initFileupload();
//				initWysihtml5();
				Layout.fixContentHeight();
			},"");
		}

		var handleCCInput = function () {
			var the = $('.inbox-compose .mail-to .inbox-cc');
			var input = $('.inbox-compose .input-cc');
			the.hide();
			input.show();
			$('.close', input).click(function () {
				input.hide();
				the.show();
			});
		}

		var handleBCCInput = function () {

			var the = $('.inbox-compose .mail-to .inbox-bcc');
			var input = $('.inbox-compose .input-bcc');
			the.hide();
			input.show();
			$('.close', input).click(function () {
				input.hide();
				the.show();
			});
		}

		var toggleButton = function(el) {
			if (typeof el == 'undefined') {
				return;
			}
			if (el.attr("disabled")) {
				el.attr("disabled", false);
			} else {
				el.attr("disabled", true);
			}
		}

		return {
			//main function to initiate the module
			init: function () {

				// handle compose btn click
				$('.inbox').on('click', '.compose-btn', function () {
					loadCompose($(this));
				});

				// handle discard btn
				$('.inbox').on('click', '.inbox-discard-btn', function(e) {
					e.preventDefault();
					loadInbox($(this));
				});

				// handle reply and forward button click
				$('.inbox').on('click', '.reply-btn', function () {
					loadReply($(this));
				});

				// handle view message
				$('.inbox').on('click', '.view-message', function () {
					loadMessage($(this));
				});

				// handle inbox listing
				$('.inbox-nav > li > a').click(function () {
					inboxCurrentPage = 1;
					loadInbox($(this), 'inbox');
				});

				//handle compose/reply cc input toggle
				$('.inbox-content').on('click', '.mail-to .inbox-cc', function () {
					handleCCInput();
				});

				//handle compose/reply bcc input toggle
				$('.inbox-content').on('click', '.mail-to .inbox-bcc', function () {
					handleBCCInput();
				});

				//handle loading content based on URL parameter
				if (App.getURLParameter("a") === "view") {
					loadMessage();
				} else if (App.getURLParameter("a") === "compose") {
					loadCompose();
				} else {
				   $('.inbox-nav > li:first > a').click();
				}

			}

		};

	}();
$(function(){
    AppInbox.init();
});