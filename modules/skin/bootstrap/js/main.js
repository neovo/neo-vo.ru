(function($) {
	// http://james.padolsey.com/javascript/regex-selector-for-jquery/
	jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ?
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
		return regex.test(jQuery(elem)[attr.method](attr.property));
	};

	$.ajaxSetup({
		cache: false,
		error: function(jqXHR, textStatus, errorThrown){
			$.loadingScreen('hide');
			jqXHR.statusText != 'abort' && alert('Ajax error: ' + textStatus + ', ' + errorThrown);
		}
	});

	$.extend({
		appendInput: function(windowId, ObjectId, InputName, InputValue)
		{
			var windowId = $.getWindowId(windowId), obj = $('#'+windowId+' #'+ObjectId);

			if (obj.length == 1
			&& obj.find("input[name='"+InputName+"']").length === 0)
			{
				$('#'+windowId+' #'+ObjectId).append(
					$('<input>')
					.attr('type', 'hidden')
					.attr('name', InputName)
					.val(InputValue));
			}
		},
		toogleInputsActive: function(windowId, status)
		{
			$("#"+$.getWindowId(windowId)+" #ControlElements input").attr('disabled', !status);
		},
		getWindowId: function(WindowId)
		{
			if (typeof WindowId == 'undefined' || WindowId == '')
			{
				WindowId = 'id_content';
			}

			return WindowId;
		},
		filterKeyDown: function(e) {
			if (e.keyCode == 13) {
				e.preventDefault();
				//jQuery(this).parents('.admin_table').find('#admin_forms_apply_button').click();
				jQuery(this).parentsUntil('table').find('#admin_forms_apply_button').click();
			}
		},
		loadingScreen: function(method) {
			// Method calling logic
			if (methods[method]) {
			  return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
			} else {
			  alert('Method ' +  method + ' does not exist on jQuery.loadingScreen');
			}
		},
		adminCheckObject: function(settings) {
			settings = jQuery.extend({
				objectId: '',
				windowId: 'id_content'
			}, settings);

			var cbItem = jQuery("#"+settings.windowId+" #"+settings.objectId);

			if (cbItem.length > 0)
			{
				// Uncheck all checkboxes with name like 'check_'
				jQuery("#" + settings.windowId + " input[type='checkbox'][id^='check_']:not([name*='_fv_'])").prop('checked', false);

				// Check checkbox
				cbItem.prop('checked', true);
			}
			else
			{
				var Check_0_0 = jQuery('<input>')
					.attr('type', 'checkbox')
					.attr('id', settings.objectId);

				jQuery('<div>')
					.attr("style", 'display: none')
					.append(Check_0_0)
					.appendTo(
						jQuery("#"+settings.windowId)
					);

				// After insert into DOM
				Check_0_0.prop('checked', true);
			}

			$("#"+settings.windowId).setTopCheckbox();
		},
		requestSettings: function(settings) {
			settings = jQuery.extend({
				// position shift
				open: function(type, data) {
					var jWindow = jQuery(this).parent(),
						mod = jQuery('body>.ui-dialog').length % 5;

					jWindow.css('top', jWindow.offset().top + 10 * mod).css('left', jWindow.offset().left + 10 * mod);

					var uiDialog = $(this).parent('.ui-dialog');
					uiDialog.width(uiDialog.width()).height(uiDialog.height());
				},
				focus: function(event, ui){
					// Текущий window
					jQuery.data(document.body, 'currentWindowId', jQuery(this).attr('id'));
				},
				path: '',
				context: '',
				action: '',
				operation: '',
				additionalParams: '',
				windowId: 'id_content',
				datasetId: 0,
				objectId: 0,
				limit: '',
				current: '',
				sortingFieldId: '',
				sortingDirection: '',
				post: {}
				//callBack: ''
			}, settings);

			return settings;
		},
		adminLoad: function(settings) {
			settings = jQuery.requestSettings(settings);

			var path = settings.path,
				data = jQuery.getData(settings);

			if (settings.additionalParams != ' ' && settings.additionalParams != '')
			{
				path += '?' + settings.additionalParams;
			}

			// Элементы списка
			var jChekedItems = jQuery("#"+settings.windowId+" :input[type='checkbox'][id^='check_']:checked"),
				iChekedItemsCount = jChekedItems.length,
				jItemsValue, iItemsValueCount, sValue;

			var reg = /check_(\d+)_(\S+)/;
			for (var jChekedItem, i=0; i < iChekedItemsCount; i++)
			{
				jChekedItem = jChekedItems.eq(i);

				var arr = reg.exec(jChekedItem.attr('id'));

				data['hostcms[checked]['+arr[1]+']['+arr[2]+']'] = 1;

				// arr[1] - ID источника, arr[2] - ID элемента
				var element_id = jChekedItem.attr('id');

				// Ищем значения записей, ID поля должно начинаться с ID checkbox-а
				jItemsValue = jQuery("#"+settings.windowId+" :input[id^='apply_"+element_id+"_fv_']"),
				iItemsValueCount = jItemsValue.length;

				for (var jValueItem, k = 0; k < iItemsValueCount; k++)
				{
					jValueItem = jItemsValue.eq(k);

					if (jValueItem.attr("type") == 'checkbox')
					{
						sValue = jValueItem.prop('checked') ? '1' : '0';
					}
					else
					{
						sValue = jValueItem.val();
					}

					data[jValueItem.attr('name')] = sValue;
				}
			}

			// Фильтр
			var jFiltersItems = jQuery("#"+settings.windowId+" :input[name^='admin_form_filter_']"),
				iFiltersItemsCount = jFiltersItems.length;

			for (var jFiltersItem, i=0; i < iFiltersItemsCount; i++)
			{
				jFiltersItem = jFiltersItems.eq(i);

				// Если значение фильтра до 255 символов
				if (jFiltersItem.val().length < 256)
				{
					// Дописываем к передаваемым данным
					data[jFiltersItem.attr('name')] = jFiltersItem.val();
				}
			}

			// Текущая страница.
			/*if (ALimit === false)
			{
				ALimit = '';
			}
			else
			{
				ALimit = '&limit=' + ALimit;
			}*/

			$.loadingScreen('show');

			jQuery.ajax({
				context: jQuery('#'+settings.windowId),
				url: path,
				type: 'POST',
				data: data,
				dataType: 'json',
				abortOnRetry: 1,
				success: [jQuery.ajaxCallback, jQuery.ajaxCallbackSkin, function()
				{
					var pjax = window.history && window.history.pushState && window.history.replaceState /*&& !navigator.userAgent.match(/(WebApps\/.+CFNetwork)/)*/;

					/*if (settings.windowId == 'id_content'){*/
					if (pjax)
					{
						var state = {
							windowId: settings.windowId,
							url: path,
							data: data
						};
						delete data['_'];

						// jQuery.param(data) is too long => 400 bad request
						// Delete empty items
						/*for (var i in data) {
							if (data[i] === '') {
								delete data[i];
							}
						}
						var url = path + (path.indexOf('?') >= 0 ? '&' : '?') + jQuery.param(data);
						*/

						window.history.pushState(state, document.title, path);
					}
					//}
				}]
			});

			return false;
		},
		adminSendForm: function(settings) {
			settings = jQuery.requestSettings(settings);

			settings = jQuery.extend({
				buttonObject: ''
			}, settings);

			// Сохраним из визуальных редакторов данные
			if (typeof tinyMCE != 'undefined')
			{
				tinyMCE.triggerSave();
			}

			// CodeMirror
			jQuery("#"+settings.windowId+" .CodeMirror").each(function(){
				this.CodeMirror.save();
			});

			var FormNode = jQuery(settings.buttonObject).closest('form'),
				data = jQuery.getData(settings),
				path = FormNode.attr('action');

			if (settings.additionalParams != ' ' && settings.additionalParams != '')
			{
				path += '?' + settings.additionalParams;
			}

			// Очистим поле для сообщений
			jQuery("#"+settings.windowId+" #id_message").empty();

			// Отображаем экран загрузки
			$.loadingScreen('show');

			//FormNode.find(':disabled').removeAttr('disabled');

			FormNode.ajaxSubmit({
				data: data,
				context: jQuery('#'+settings.windowId),
				url: path,
				//type: 'POST',
				dataType: 'json',
				cache: false,
				success: jQuery.ajaxCallback
			});
		},
		getData: function(settings) {
			var data = (typeof settings.post != 'undefined') ? settings.post : {};

			data['_'] = Math.round(new Date().getTime());

			if (settings.action != '')
			{
				data['hostcms[action]'] = settings.action;
			}

			if (settings.operation != '')
			{
				data['hostcms[operation]'] = settings.operation;
			}

			/*if (settings.additionalParams != ' ' && settings.additionalParams != '')
			{
				path += '?' + settings.additionalParams;
			}*/

			if (settings.limit != '')
			{
				data['hostcms[limit]'] = settings.limit;
			}

			if (settings.current != '')
			{
				data['hostcms[current]'] = settings.current;
			}

			if (settings.sortingFieldId != '')
			{
				data['hostcms[sortingfield]'] = settings.sortingFieldId;
			}

			if (settings.sortingDirection != '')
			{
				data['hostcms[sortingdirection]'] = settings.sortingDirection;
			}

			data['hostcms[window]'] = settings.windowId;

			return data;
		},
		beforeContentLoad: function(object)
		{
			if (typeof tinyMCE != 'undefined')
			{
				object.find('textarea').each(function(){
					var elementId = this.id;
					if (tinyMCE.getInstanceById(elementId) != null)
					{
						tinyMCE.execCommand('mceRemoveControl', false, elementId);
						//jQuery('#content').tinymce().execCommand('mceInsertContent',false, elementId);
					}
				});
			}
		},
		insertContent: function(jObject, content)
		{
			// Fix blink in FF
			jObject.scrollTop(0).empty().html(content);
		},
		ajaxCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			if (data == null)
			{
				alert('AJAX response error.');
				return;
			}

			var jObject = jQuery(this);

			if (data.form_html !== null && data.form_html.length)
			{
				jQuery.beforeContentLoad(jObject, data);
				jQuery.insertContent(jObject, data.form_html);
				jQuery.afterContentLoad(jObject, data);
			}

			var jMessage = jObject.find('#id_message');

			if (jMessage.length === 0)
			{
				jMessage = jQuery('<div>').attr('id', 'id_message');
				jObject.prepend(jMessage);
			}

			jMessage.empty().html(data.error);

			if (typeof data.title != 'undefined' && data.title != '' && jObject.attr('id') == 'id_content')
			{
				document.title = data.title;
			}
		},
		ajaxRequest: function(settings) {

			settings = jQuery.requestSettings(settings);

			if (typeof settings.callBack == 'undefined')
			{
				alert('Callback function is undefined');
			}

			var path = settings.path;

			if (settings.additionalParams != ' ' && settings.additionalParams != '')
			{
				path += '?' + settings.additionalParams;
			}

			$.loadingScreen('show');

			var data = jQuery.getData(settings);
			data['hostcms[checked][' + settings.datasetId + '][' + settings.objectId + ']'] = 1;

			if (typeof settings.additionalData != 'undefined')
			{
				$.each(settings.additionalData, function(index, value){
					data[index] = value;
				})
			}

			var ajaxOptions = {
				context: jQuery('#'+settings.windowId + ' #' + settings.context),
				url: path,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: settings.callBack,
				abortOnRetry:1
			}

			if (typeof settings.ajaxOptions != 'undefined')
			{
				$.each(settings.ajaxOptions, function(optionName, optionValue){
					ajaxOptions[optionName] = optionValue;
				})
			}

			jQuery.ajax(ajaxOptions);

			return false;
		},
		loadSelectOptionsCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');

			jQuery(this).empty();
			for (var key in data)
			{
				jQuery(this).append(jQuery('<option>').attr('value', key).text(data[key]));
			}
		},
		loadDivContentAjaxCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			jQuery(this).empty().html(data);
		},
		pasteStandartAnswer: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			jQuery(this).val(jQuery(this).val() + data);

		},
		clearFilter: function(windowId)
		{
			jQuery("#" + windowId + " .admin_table_filter input").val('');
			jQuery("#" + windowId + " .admin_table_filter select").prop('selectedIndex', 0);
		},
		deleteNewProperty: function(object)
		{
			//jQuery(object).closest('.item_div').remove();
			jQuery(object).closest('[id ^= "property_"]').remove();
		},
		deleteProperty: function(object, settings)
		{
			var jObject = jQuery(object).siblings('input,select:not([onchange]),textarea');

			// For files
			if (jObject.length === 0)
			{
				jObject = jQuery(object).siblings('div,label').children('input');
			}

			var property_name = jObject.eq(0).attr('name');

			settings = jQuery.extend({
				operation: property_name
			}, settings);

			settings = jQuery.requestSettings(settings);

			var data = jQuery.getData(settings);
			data['hostcms[checked][' + settings.datasetId + '][' + settings.objectId + ']'] = 1;

			var path = settings.path;

			jQuery.ajax({
				context: jQuery('#'+settings.windowId),
				url: path,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: jQuery.ajaxCallback
			});

			jQuery.deleteNewProperty(object);
		},
		setCheckbox: function(windowId, checkboxId)
		{
			jQuery("#"+windowId+" input[type='checkbox'][id='"+checkboxId+"']").attr('checked', true);
		},
		cloneSpecialPrice: function(windowId, cloneDelete)
		{
			var jSpecialPrice = jQuery(cloneDelete).closest('.spec_prices'),
			jNewObject = jSpecialPrice.clone();

			// Change input name
			jNewObject.find(':regex(name, ^\\S+_\\d+$)').each(function(index, object){
				var reg = /^(\S+)_(\d+)$/;
				var arr = reg.exec(object.name);
				jQuery(object).prop('name', arr[1] + '_' + '[]');
			});
			jNewObject.find("input").val('');

			//jNewObject.find("img#delete").attr('onclick', "jQuery.deleteNewProperty(this)");
			jNewObject.insertAfter(jSpecialPrice);
		},
		deleteNewSpecialprice: function(object)
		{
			var jObject = jQuery(object).closest('.spec_prices').remove();
		},
		clonePropertyInfSys: function(windowId, index)
		{
			var jProperies = jQuery('#' + windowId + ' #property_' + index),
			jNewObject = jProperies.eq(0).clone(),
			iNewId = index + 'group' + Math.floor(Math.random() * 999999),
			jDir = jNewObject.find("select[onchange]"),
			jItem = jNewObject.find("select:not([onchange])");

			jDir
				.attr('onchange', jDir.attr('onchange').replace(jItem.attr('id'), iNewId))
				.val(jProperies.eq(0).find("select[onchange]").val());

			jItem
				.attr('name', 'property_' + index + '[]')
				.attr('id', iNewId)
				.val(jProperies.eq(0).find("select:not([onchange])").val());

			jNewObject.find("img#delete").attr('onclick', "jQuery.deleteNewProperty(this)");
			jNewObject.insertAfter(jProperies.eq(-1));
		},
		cloneFile: function(windowId)
		{
			var jProperies = jQuery('#' + windowId + ' #file'),
			jNewObject = jProperies.eq(0).clone();
			jNewObject.find("input").attr('name', 'file[]').val('');
			jNewObject.insertAfter(jProperies.eq(-1));
		},
		showWindow: function(windowId, content, settings)
		{
			settings = jQuery.extend({
				/*modal: true, */autoOpen: true, addContentPadding: false, resizable: true, draggable: true, Minimize: false, Closable: true
			}, settings);

			var jWin = jQuery('#' + windowId);

			if (!jWin.length)
			{
				jWin = jQuery('<div>')
					.addClass('hostcmsWindow')
					.attr('id', windowId)
					//.appendTo(jQuery(document))
					.html(content)
					.HostCMSWindow(settings)/*
					.HostCMSWindow('open')*/;
			}
			return jWin;
		},
		loadTagsListCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');

			var windowId = this.parents("[class='hostcmsWindow ui-dialog-content ui-widget-content contentpadding']").attr('id');

			if(windowId == undefined)
				windowId = 'id_content';

			var that = $('#'+ windowId).data('that');

			that.element.siblings('.tag').removeClass('tag-important');

			that.input.data('typeahead').source = data;
			that.input.data('typeahead').process(data);

			// Добавить удаление that после реализации прерывания AJAX-запроса с помощью Prefilter
			//$('#'+ windowId).removeData('that');
		},
		// Изменение статуса заказа товара
		changeOrderStatus: function(windowId)
		{
			var date = new Date(), day = date.getDate(), month = date.getMonth() + 1, hours = date.getHours(), minutes = date.getMinutes();

			if (day < 10)
			{
				day = '0' + day;
			}

			if (month < 10)
			{
				month = '0' + month;
			}

			if (hours < 10)
			{
				hours = '0' + hours;
			}

			if (minutes < 10)
			{
				minutes = '0' + minutes;
			}

			$("#"+windowId+" #status_datetime").val(day + '.' + month + '.' + date.getFullYear() + ' ' + hours + ':' + minutes + ':' + '00');
		},
		// Установка cookies
		// name - имя параметра
		// value - значение параметра
		// expires - время жизни куки в секундах
		// path - путь куки
		// domain - домен
		setCookie: function(name, value, expires, path, domain, secure)
		{
			// если истечение передано - устанавливаем время истечения на expires секунд
			// вперед
			if (expires)
			{
				var date = new Date();
				expires = (expires * 1000) + date.getTime();
				date.setTime(expires);
			}

			document.cookie = name + "=" + encodeURIComponent(value) +
			((expires) ? "; expires=" + date.toGMTString() : "") +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") +
			((secure) ? "; secure" : "");
		}
	});

	// Функции для коллекции элементов
	jQuery.fn.extend({
		toggleDisabled: function()
		{
			return this.each(function(){
				this.disabled = !this.disabled;
			});
		},
		editable: function(settings){
			settings = jQuery.extend({
				save: function(item, settings){

					var data = jQuery.getData(settings), reg = /apply_check_(\d+)_(\S+)_fv_(\d+)/,
					itemId = item.parent().prop('id'), arr = reg.exec(itemId);

					data['hostcms[checked]['+arr[1]+']['+arr[2]+']'] = 1;
					data[itemId] = item.text();

					jQuery.ajax({
						// ajax loader
						context: jQuery('<img>').addClass('img_line').prop('src', '/modules/skin/default/js/ui/themes/base/images/ajax-loader.gif').appendTo(item),
						url: settings.path,
						type: 'POST',
						data: data,
						dataType: 'json',
						success: function(){this.remove();}
					});
				},
				action: 'apply'
			}, settings);

			return this.each(function(index, object){
				jQuery(object).on('dblclick', function(){
					var item = jQuery(this).css('display', 'none'),
					jInput = jQuery('<input>').prop('type', 'text').on('blur', function() {
						var input = jQuery(this), item = input.prev();
						item.html(input.val()).css('display', 'block');
						input.remove();
						settings.save(item, settings);
					}).on('keydown', function(e){
						if (e.keyCode == 13) {
							e.preventDefault();
							this.blur();
						}
						if (e.keyCode == 27) { // ESC
							e.preventDefault();
							var input = jQuery(this), item = input.prev();
							item.css('display', 'block');
							input.remove();
						}
					}).width('90%').prop('name', item.parent().prop('id'))
					.insertAfter(item).focus().val(item.text());
				});
			});
		},
		clearSelect: function()
		{
			return this.each(function(index, object){
				jQuery(object).empty().append(jQuery('<option>').attr('value', 0).text(' ... '));
			});
		},
		toggleHighlight: function()
		{
			return this.each(function(){
				var object = jQuery(this);
				object.toggleClass('cheked');
			});
		},
		highlightAllRows: function(checked)
		{
			return this.each(function(){
				var object = jQuery(this);

				// Устанавливаем checked для групповых чекбоксов
				object.find("input[type='checkbox'][id^='id_admin_forms_all_check']").prop('checked', checked);

				object.find("input[type='checkbox'][id^='check_']").each(function() {
					var object = $(this);

					if (object.prop('checked') != checked)
					{
						object.parents('tr').toggleHighlight();
					}
					// Устанавливаем checked
					object.prop('checked', checked);
				});
			});
		},
		setTopCheckbox: function()
		{
			return this.each(function(){
				var object = jQuery(this), bChecked = !object.find("input[type='checkbox'][id^='check_']").is(':not(:checked)');
				object.find("input[type='checkbox'][id^='id_admin_forms_all_check']").prop('checked', bChecked);
			});
		}
	});

	var baseURL = location.href, popstate = ('state' in window.history && window.history.state !== null);
	jQuery(window).bind('popstate', function(event){
		// Ignore inital popstate that some browsers fire on page load
		var startPop = !popstate && baseURL == location.href;
		popstate = true;
		if (startPop){
			return;
		}

		var state = event.state;
		if (state && state.windowId/* && state.windowId == 'id_content'*/){
			var data = state.data;
			data['_'] = Math.round(new Date().getTime());

			$.loadingScreen('show');

			jQuery.ajax({
				context: jQuery('#'+state.windowId),
				url: state.url,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: jQuery.ajaxCallback
			});
		}
		else{
			  window.location = location.href;
		}
	});

	if (jQuery.inArray('state', jQuery.event.props) < 0){
		jQuery.event.props.push('state');
	}

	var currentRequests = {};
	jQuery.ajaxPrefilter(function(options, originalOptions, jqXHR){
	  if(options.abortOnRetry){
		if(currentRequests[options.url]){
			currentRequests[options.url].abort();
		}
		currentRequests[options.url] = jqXHR;
	  }
	});

})(jQuery);


function cSelectFilter(windowId, sObjectId)
{
	this.windowId = $.getWindowId(windowId);
	this.sObjectId = sObjectId;

	// Игнорировать регистр
	this.ignoreCase = true;
	this.timeout = null;
	this.pattern = '';
	this.aOriginalOptions = null;
	this.sSelectedValue = '';

	// Сейчас происходит фильтрация
	this.is_filtering = false;

	// Установка требуемого шаблона фильтрации
	this.Set = function(pattern) {
		this.pattern = pattern;
		this.is_filtering = (pattern.length != 0);
	}

	// Указывает регулярному выражению игнорировать регистр
	this.SetIgnoreCase = function(value) {
		this.ignoreCase = value;
	}

	this.GetCurrentSelectObject = function() {
		this.oCurrentSelectObject = $("#"+this.windowId+" #"+this.sObjectId);
	}

	this.Init = function() {

		this.GetCurrentSelectObject();

		if (this.oCurrentSelectObject.length == 1)
		{
			var jOptions = this.oCurrentSelectObject.children("option"), jOptionItem;

			if (jOptions.length > 0)
			{
				// Сохраняем установленное до фильтрации значение
				this.sSelectedValue = this.oCurrentSelectObject.val();
				this.aOriginalOptions = jOptions;
			}
		}
	}

	this.Filter = function() {
		var self = this;
		var icon = $("#" + this.windowId + " #filer_" + this.sObjectId).prev('span').find('i');

		icon.removeClass('fa-search').addClass('fa-spinner fa-spin');

		setTimeout(function(){
			// Если фильтрация - получаем объект
			if (self.is_filtering) {
				// Заново получаем объект, т.к. при AJAX-запросе на момент Init-а
				// объект мог не существовать
				self.GetCurrentSelectObject();
			}

			if (self.aOriginalOptions == null || self.aOriginalOptions.length === 0) {
				self.Init();
			}

			if (self.oCurrentSelectObject.length == 1)
			{
				// Сбрасываем все значения списка
				self.oCurrentSelectObject.empty();

				if (self.is_filtering) {
					var attributes = self.ignoreCase ? 'i' : '',
						regexp = new RegExp(self.pattern, attributes),
						currentOption, iOriginalOptionsLength = self.aOriginalOptions.length;

					for (var i = 0; i < iOriginalOptionsLength; i++)
					{
						currentOption = $(self.aOriginalOptions[i]);

						if (regexp.test(' ' + currentOption.text()))
						//if (currentOption.text().indexOf(self.pattern) != -1)
						{
							self.oCurrentSelectObject.append(
								currentOption
							);
						}
					}
				}
				else {
					// restore all values
					self.oCurrentSelectObject.append(self.aOriginalOptions);
				}
			}

			icon.removeClass('fa-spinner fa-spin').addClass('fa-search');

			self.oCurrentSelectObject.get(0).options.selectedIndex = 0;
			//self.oCurrentSelectObject.val(self.sSelectedValue);
			//jImg.remove();
		}, 100);
	}
}

/**
 * Модуль "Структура сайта"
 *
 * @windowId
 * @ASelectedItem код выбранного элемента
 * @structure_id идентификатор структуры
 * @lib_dir_id раздел типовых динамически страниц
 * @lib_id идентификатор типовых динамически страниц
 */
function SetViewStructure(windowId, ASelectedItem, iStructureId, iLibDirId, iLibId)
{
	windowId = $.getWindowId(windowId);

	var template_id = 'none',
		document_dir = 'none',
		document = 'none',
		url = 'none',
		lib_dir = 'none',
		lib = 'none',
		lib_properties = 'none';

	switch (parseInt(ASelectedItem))
	{
		default:
		case 0: // Страница
			document_dir = 'block';
			document = 'block';
			url = 'block';

			$("#"+windowId+" #structure_source").hide();
			$("#"+windowId+" #structure_config_source").hide();
		break;
		case 1: // Динамическая страница
			template_id = 'block';
			$("#"+windowId+" #structure_source").show();
			$("#"+windowId+" #structure_config_source").show();
		break;
		case 2: // Типовая дин. страница
			template_id = 'block';
			lib_dir = 'block';
			lib = 'block';
			lib_properties = 'block';
			$("#"+windowId+" #structure_source").hide();
			$("#"+windowId+" #structure_config_source").hide();

		break;
	}

	$("#"+windowId+" #template_id").css('display', template_id);
	$("#"+windowId+" #document_dir").css('display', document_dir);
	$("#"+windowId+" #document").css('display', document);
	$("#"+windowId+" #url").css('display', url);
	$("#"+windowId+" #lib_dir").css('display', lib_dir);
	$("#"+windowId+" #lib").css('display', lib);
	$("#"+windowId+" #lib_properties").css('display', lib_properties);
}

/**
 * Модуль "Helpdesk"
 */
function SetHolidays(windowId, index)
{
	var week_day = 'none';

	if (index == 0)
	{
		week_day = 'block';
	}

	$("#"+$.getWindowId(windowId)+" #week_day").css('display', week_day);
}

function ShowPropertyRows(windowId, index)
{
	var windowId = $.getWindowId(windowId),
		default_value = 'none',
		list_id = 'none',
		informationsystem_id = 'none',
		shop_id = 'none',
		default_value_date = 'none',
		default_value_datetime = 'none',
		default_value_checked = 'none';

	index = parseInt(index);

	switch (index)
	{
		case 0: // Число
		case 1: // Строка
		case 4: // Большое текстовое поле
		case 6: // Визуальный редактор
		case 11: // Число с плавающей
			default_value = 'block';
		break;
		case 2: // Файл
		break;
		case 3: // Список
			list_id = 'block';
		break;
		case 5: // Информационная система
			informationsystem_id = 'block';
		break;
		case 7: // Флажок
			default_value_checked = 'block';
		break;
		case 8: // Дата
			default_value_date = 'block';
		break;
		case 9: // ДатаВремя
			default_value_datetime = 'block';
		break;
		case 12: // Магазин
			shop_id = 'block';
		break;
	}

	$("#"+windowId+" #default_value").css('display', default_value);
	$("#"+windowId+" #list_id").css('display', list_id);
	$("#"+windowId+" #informationsystem_id").css('display', informationsystem_id);
	$("#"+windowId+" #shop_id").css('display', shop_id);
	$("#"+windowId+" #default_value_date").css('display', default_value_date);
	$("#"+windowId+" #default_value_datetime").css('display', default_value_datetime);
	$("#"+windowId+" #default_value_checked").css('display', default_value_checked);
}

function ShowRowsAdminForm(windowId, index)
{
	var windowId = $.getWindowId(windowId), index = parseInt(index);

	var image = 'none', list = 'none', link = 'none', onclick = 'none';

	switch (index)
	{
		case 4: // Ссылка
		case 10: // Callback
			link = 'block';
			onclick = 'block';
		break;
		case 7: // Картинка-ссылка
			image = 'block';
			link = 'block';
			onclick = 'block';
		break;
		case 8: // Список
			list = 'block';
		break;
	}

	$("#"+windowId+" #image").css('display', image);
	$("#"+windowId+" #list").css('display', list);
	$("#"+windowId+" #link").css('display', link);
	$("#"+windowId+" #onclick").css('display', onclick);
}

function ShowRowsForms(windowId, index)
{
	var windowId = $.getWindowId(windowId),
		index = parseInt(index),
		list_id = 'none',
		cols_id = 'none',
		rows_id = 'none',
		checked_id = 'none',
		size_id = 'none',
		default_value_id = 'none',
		obligatory_id = 'none';

	switch (index)
	{
		case 0: // Поле ввода.
		default:
			size_id = 'block';
			default_value_id = 'block';
			obligatory_id = 'block';
			break;
		 case 1: // Пароль.
			size_id = 'block';
			default_value_id = 'block';
			obligatory_id = 'block';
			break;
		 case 2: // Поле загрузки файла.
			size_id = 'block';
			obligatory_id = 'block';
			break;
		 case 3: // Переключатель.
			list_id = 'block';
			obligatory_id = 'block';
			break;
		 case 4: // Флажок.
			checked_id = 'block';
			obligatory_id = 'block';
			break;
		 case 5: // Большое текстовое поле.
			cols_id = 'block';
			rows_id = 'block';
			default_value_id = 'block';
			obligatory_id = 'block';
			break;
		 case 6: // Список.
			list_id = 'block';
			obligatory_id = 'block';
			break;
		case 7: // Скрытое поле.
			default_value_id = 'block';
			obligatory_id = 'block';
			break;
		 case 8: // Надпись.
			default_value_id = 'block';
			obligatory_id = 'block';
			break;
		case 9: // Список флажков
			list_id = 'block';
			obligatory_id = 'block';
			break;
	}
	$("#"+windowId+" #list_id").css('display', list_id);
	$("#"+windowId+" #cols_id").css('display', cols_id);
	$("#"+windowId+" #rows_id").css('display', rows_id);
	$("#"+windowId+" #checked_id").css('display', checked_id);
	$("#"+windowId+" #size_id").css('display', size_id);
	$("#"+windowId+" #default_value_id").css('display', default_value_id);
	$("#"+windowId+" #obligatory_id").css('display', obligatory_id);
}

function ShowImport(windowId, index)
{
	var windowId = $.getWindowId(windowId),
		index = parseInt(index),
		import_price_encoding = 'none',
		import_price_separator = 'none',
		import_price_stop = 'none',
		import_price_name_field_f = 'none',
		import_price_action_items = 'none',
		import_price_action_delete_image = 'none',
		import_price_max_time = 'none',
		search_event_indexation = 'none',
		import_price_max_count = 'none',
		import_price_separator_text = 'none',
		import_price_stop_text = 'none',
		export_external_properties_allow_groups = 'none';
		import_price_list_separator = 'none';

	if (index == 0)
	{
		import_price_encoding = 'block';
		import_price_separator = 'block';
		import_price_stop = 'block';
		import_price_name_field_f = 'block';
		import_price_action_items = 'block';
		import_price_action_delete_image = 'block';
		import_price_max_time = 'block';
		search_event_indexation = 'block';
		import_price_max_count = 'block';
		import_price_separator_text = 'block';
		import_price_stop_text = 'block';
		export_external_properties_allow_groups = 'block';
		import_price_list_separator = 'block';
	}

	$("#"+windowId+" #import_price_encoding").css('display', import_price_encoding);
	$("#"+windowId+" #import_price_separator").css('display', import_price_separator);
	$("#"+windowId+" #import_price_stop").css('display', import_price_stop);
	$("#"+windowId+" #import_price_name_field_f").css('display', import_price_name_field_f);
	$("#"+windowId+" #import_price_action_items").css('display', import_price_action_items);
	$("#"+windowId+" #import_price_action_delete_image").css('display', import_price_action_delete_image);
	$("#"+windowId+" #import_price_max_time").css('display', import_price_max_time);
	$("#"+windowId+" #search_event_indexation").css('display', search_event_indexation);
	$("#"+windowId+" #import_price_max_count").css('display', import_price_max_count);
	$("#"+windowId+" #import_price_separator_text").css('display', import_price_separator_text);
	$("#"+windowId+" #import_price_stop_text").css('display', import_price_stop_text);
	$("#"+windowId+" #export_external_properties_allow_groups").css('display', export_external_properties_allow_groups);
	$("#"+windowId+" #import_price_list_separator").css('display', import_price_list_separator);
}
function ShowExport(windowId, index)
{
	var windowId = $.getWindowId(windowId),
		index = parseInt(index),
		export_price_separator = 'none',
		order_begin_date = 'none',
		order_end_date = 'none',
		import_price_encoding = 'none',
		shop_groups_parent_id = 'none',
		export_external_properties_allow_items = 'none',
		export_external_properties_allow_groups = 'none',
		export_modifications_allow = 'none'
	;

	switch(index)
	{
		case 0:
			export_price_separator = 'block';
			import_price_encoding = 'block';
			shop_groups_parent_id = 'block';
			export_external_properties_allow_items = 'block';
			export_external_properties_allow_groups = 'block';
			export_modifications_allow = 'block';
		break;
		case 1:
			export_price_separator = 'block';
			order_begin_date = 'block';
			order_end_date = 'block';
			import_price_encoding = 'block';
		break;
		case 2:
		case 3:
			export_modifications_allow = 'block';
			export_external_properties_allow_items = 'block';
		break;
	}

	$("#"+windowId+" #export_price_separator").css('display', export_price_separator);
	$("#"+windowId+" #order_begin_date").css('display', order_begin_date);
	$("#"+windowId+" #order_end_date").css('display', order_end_date);
	$("#"+windowId+" #import_price_encoding").css('display', import_price_encoding);
	$("#"+windowId+" #shop_groups_parent_id").css('display', shop_groups_parent_id);
	$("#"+windowId+" #export_external_properties_allow_items").css('display', export_external_properties_allow_items);
	$("#"+windowId+" #export_external_properties_allow_groups").css('display', export_external_properties_allow_groups);
	$("#"+windowId+" #export_modifications_allow").css('display', export_modifications_allow);
}

function ShowRowsLibProperty(windowId, index)
{
	var sql_request = 'none',
		sql_caption_field = 'none',
		sql_value_field = 'none';

	switch (parseInt(index))
	{
		case 4:
			sql_request = sql_caption_field = sql_value_field = 'block';
		break;
	}

	$("#"+windowId+" #sql_request").css('display', sql_request);
	$("#"+windowId+" #sql_caption_field").css('display', sql_caption_field);
	$("#"+windowId+" #sql_value_field").css('display', sql_value_field);
}

function ShowRowsAdvertisementPropertyType(windowId, index)
{
	var source = 'none',
		href = 'none',
		html = 'none',
		popup_structure_id = 'none';

	index = parseInt(index);

	switch (index)
	{
		case 0: // Изображение
		case 3: // Flash
			source = 'block';
			href = 'block';
		break;
		case 1: // HTML
			html = 'block';
		break;
		case 2: // Всплывающий
			popup_structure_id = 'block';
		break;
	}

	$("#"+windowId+" #source").css('display', source);
	$("#"+windowId+" #href").css('display', href);
	$("#"+windowId+" #html").css('display', html);
	$("#"+windowId+" #popup_structure_id").css('display', popup_structure_id);
}

// -- Проверка ячеек
function FieldCheck(WindowId, field)
{
	if (typeof fieldType == 'undefined')
	{
		return false;
	}

	var WindowId = $.getWindowId(WindowId),
		value = $(field).val(),
		FiledId = $(field).attr('id'),
		message = '';

	if (typeof fieldType[field.id] != 'undefined')
	{
		// Проверка на минимальную длину
		if (fieldType[FiledId]['minlen'] && value.length < fieldType[FiledId]['minlen'])
		{
			var decl = declension(fieldType[FiledId]['minlen'], i18n['one_letter'], i18n['some_letter2'], i18n['some_letter1']);

			// Есть пользовательское сообщение
			if (fieldMessage[FiledId] && fieldMessage[FiledId]['minlen'])
			{
				message += fieldMessage[FiledId]['minlen'];
			}
			else // Стандартное сообщение
			{
				message += i18n['Minimum'] + ' ' + fieldType[FiledId]['minlen'] + ' ' + decl + '. ' + i18n['current_length'] + ' ' + value.length + '. ';
			}
		}

		// Проверка на максимальную длину
		if (fieldType[FiledId]['maxlen'] && value.length > fieldType[FiledId]['maxlen'])
		{
			var decl = declension(fieldType[FiledId]['maxlen'], i18n['one_letter'], i18n['some_letter2'], i18n['some_letter1']);

			// Есть пользовательское сообщение
			if (fieldMessage[FiledId] && fieldMessage[FiledId]['maxlen'])
			{
				message += fieldMessage[FiledId]['maxlen'];
			}
			else // Стандартное сообщение
			{
				message += i18n['Maximum'] + ' ' + fieldType[FiledId]['maxlen'] + ' ' + decl + '. ' + i18n['current_length'] + ' ' + value.length + '. ';
			}
		}

		// Проверка на регулярное выражение
		if (value.length > 0 && fieldType[FiledId]['reg'] && !value.match(fieldType[FiledId]['reg']))
		{
			// Есть пользовательское сообщение
			if (fieldMessage[FiledId] && fieldMessage[FiledId]['reg'])
			{
				message += fieldMessage[FiledId]['reg'];
			}
			else // Стандартное сообщение
			{
				message += i18n['wrong_value_format'] + ' ';
			}
		}

		// Проверка на соответствие значений 2-х полей
		if (fieldType[FiledId]['fieldEquality'])
		{
			// Пытаемся получить значение поля, которому должны соответствовать
			var jFiled2 = $("#"+WindowId+" #"+fieldType[FiledId]['fieldEquality']);

			if (jFiled2.length > 0
			// Сравниваем значение полей
			&& value != jFiled2.val())
			{
				// Есть пользовательское сообщение
				if (fieldMessage[FiledId] && fieldMessage[FiledId]['fieldEquality'])
				{
					message += fieldMessage[FiledId]['fieldEquality'];
				}
				else // Стандартное сообщение
				{
					message += i18n['different_fields_value'] + ' ';
				}
			}
		}

		FieldCheckShowError(WindowId, FiledId, message);
	}
}

function FieldCheckShowError(WindowId, FiledId, message)
{
	var WindowId = $.getWindowId(WindowId);

	// Insert message into the message div
	$("#" + WindowId + " #"+FiledId + '_error').html(message);

	// Плучаем элемент формы, над которым ведется работа
	var ElementField =	$("#" + WindowId + " #"+FiledId);

	if (ElementField.length > 0)
	{
		// Устанавливаем флаг несоответствия
		fieldsStatus[FiledId] = (message.length > 0);

		if (fieldsStatus[FiledId])
		{
			ElementField
				.css('border-style', 'solid')
				.css('border-width', '1px')
				.css('border-color', '#DB1905')
				.css('background-image', "url('/admin/images/bullet_red.gif')")
				.css('background-position', 'center right')
				.css('background-repeat', 'no-repeat');
		}
		else
		{
			ElementField
				.css('border-style', '')
				.css('border-width', '')
				.css('border-color', '')
				.css('background-image', "url('/admin/images/bullet_green.gif')")
				.css('background-position', 'center right')
				.css('background-repeat', 'no-repeat');
		}
	}

	// Отображать контрольные элементы
	var ControlElementsStatus = true;

	for (ItemIndex in fieldsStatus)
	{
		// если есть хоть одно несоответствие - выключаем управляющие элементы
		if (fieldsStatus[ItemIndex])
		{
			ControlElementsStatus = false;
			break;
		}
	}

	// Активируем-выключаем контрольные элементы формы
	$.toogleInputsActive(WindowId, ControlElementsStatus);
}

function CheckAllField(windowId, formId)
{
	var windowId = $.getWindowId(windowId);
	$("#"+windowId+" #"+formId+" :input").each(function(){
		FieldCheck(windowId, this);
	});
}

/**
* Склонение после числительных
* int number числительное
* int nominative Именительный падеж
* int genitive_singular Родительный падеж, единственное число
* int genitive_plural Родительный падеж, множественное число
*/
function declension(number, nominative, genitive_singular, genitive_plural)
{
	var last_digit = number % 10;
	var last_two_digits = number % 100;

	if (last_digit == 1 && last_two_digits != 11)
	{
		var result = nominative;
	}
	else
	{
		var result = (last_digit == 2 && last_two_digits != 12) || (last_digit == 3 && last_two_digits != 13) || (last_digit == 4 && last_two_digits != 14)
			? genitive_singular
			: genitive_plural;
	}

	return result;
}
// /-- Проверка ячеек

// http://www.tinymce.com/wiki.php/How-to_implement_a_custom_file_browser
function HostCMSFileManager(defaultpath)
{
	this.defaultpath = defaultpath;

	this.fileBrowserCallBack = function(field_name, url, type, win)
	{
		this.field = field_name;
		this.callerWindow = win;

		if (url == '') {
			url = this.defaultpath;
		}
		url = url.split('\\').join('/');

		var cdir = '/', dir = '', lastPos = url.lastIndexOf('/');

		if (lastPos != -1)
		{
			url = url.substr(0, lastPos);
			// => /upload

			lastPos = url.lastIndexOf('/');

			if (lastPos != -1)
			{
				cdir = url.substr(0, lastPos + 1);
				dir = url.substr(lastPos + 1);
			}
		}

		var path = "/admin/wysiwyg/filemanager/index.php?field_name=" + field_name + "&cdir=" + cdir + "&dir=" + dir + "&type=" + type, width = 700, height = 500;

		var x = parseInt(screen.width / 2.0) - (width / 2.0), y = parseInt(screen.height / 2.0) - (height / 2.0);

		this.win = window.open(path, "FM", "top=" + y + ",left=" + x + ",scrollbars=yes,width=" + width + ",height=" + height + ",resizable=yes");

		/*tinyMCE.openWindow({
			file : path,
			title : "File Browser",
			width : 700,
			height : 500,
			close_previous : "no"
		}, {
			window : win,
			input : field_name,
			resizable : "yes",
			inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
			editor_id : tinyMCE.selectedInstance.editorId
		});*/
		return false;
	}

	this.insertFile = function(url)
	{
		url = decodeURIComponent(url);
		url = url.replace(new RegExp(/\\/g), '/');
		this.callerWindow.document.forms[0].elements[this.field].value = url;

		try
		{
			this.callerWindow.document.forms[0].elements[this.field].onchange();
		}
		catch (e){}

		this.win.close();
	}
};

/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.cookie = function (key, value, options) {
    // key and at least value given, set cookie...
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);

        if (value === null || value === undefined) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }

        value = String(value);

        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : cookie_encode(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

function cookie_encode(string){
	//full uri decode not only to encode ",; =" but to save uicode charaters
	var decoded = encodeURIComponent(string);
	//encod back common and allowed charaters {}:"#[] to save space and make the cookies more human readable
	var ns = decoded.replace(/(%7B|%7D|%3A|%22|%23|%5B|%5D)/g,function(charater){return decodeURIComponent(charater);});
	return ns;
}
/* /jQuery Cookie plugin */
