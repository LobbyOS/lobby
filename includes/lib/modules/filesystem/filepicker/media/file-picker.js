/**
 * Program Name: File Picker
 * Program URI: http://code.google.com/p/file-picker/
 * Description: This program will let you browse server-side folders and files
 * 				like a Windows Explorer, and you can pick several files that you
 * 				want to process in somewhere.
 * 
 * Copyright (c) 2008-2009 Hpyer (coolhpy[at]163.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */

var FilePicker = {

	/**
	 * @desc	
	 * @access	public
	 */
	params: {
		/**
		 * @desc	The URI of root folder that user can visit via HTTP method
		 * @var		string
		 */
		uri: '.',
		/**
		 * @desc	Variable to receive the JSON string after user selected files
		 * @var		string
		 */
		key: 'FP_RESULT',
		/**
		 * @desc	Is user can multi-select files or not
		 * @var		bool
		 */
		multi: true,
		/**
		 * @desc	Server-side script
		 * @var		string
		 */
		access: 'file-picker.php',
		/**
		 * @desc	Is using unicode or not
		 * @var		bool
		 */
		unicode: true,
		/**
		 * @desc	It means double-click if the time click twice quickly 
		 * 			is less than the following seconds. (Unit: millisecond)
		 * @var		int
		 */
		delay: 300,
		/**
		 * @desc	Is auto-complete filename or not
		 * @var		bool
		 * @since	1.1
		 */
		auto_complete: true
	},

	/**
	 * @desc
	 * @access	private
	 */
	last_click: null,

	/**
	 * @access	private
	 */
	timer: null,


	/**
	 * @desc	Initialization
	 * @since	1.0.2
	 * @return	void
	 */
	init: function(params) {
		$.extend(this.params, params);
		$.base64.is_unicode = this.params.unicode;
		$.ajaxSetup({
			url: this.params.access,
			dataType: 'json'
		});
		this.do_translate_options();
		this.events_binder();
		this.get_list();
	},

	/**
	 * @desc	Operation completed, return the JSON string like: {uri:"/path/to/folder", files:["file_1.txt", "file_2.jpg"]}
	 * @return	void
	 */
	do_complete: function() {
		var self = FilePicker;
		var obj = '{' +
			'uri:"' + self.get_uri() + '", ' +
			'files:[' + self.get_selected(true) + ']' +
		'}';
		self.do_close(obj);
	},

	/**
	 * @desc	Close window, and return the JSON string
	 * @param	string	obj
	 * @return	void
	 */
	do_close: function(obj) {
		if (typeof(obj) != 'string') obj = '';
		eval('window.parent.' + FilePicker.params.key + '=\'' + obj + '\';');
		//if (window.parent && window.parent.location) {
			self.parent.tb_remove();
		//	return ;
		//}
		//window.close();
	},

	/**
	 * @desc	Get the URI of current folder
	 * @return	string
	 */
	get_uri: function() {
		var uri;
		uri = $.base64.decode($('#target_dir').val());
		uri = uri == '/' ? '' : uri;
		return this.params.uri + uri;
	},

	/**
	 * @desc	Get JSON string that be translated with all the selected file(s)
	 * @return	string
	 */
	get_selected: function(with_quote) {
		var t = $('li.selected');
		if (t.length == 1){
			return with_quote ? '"' + t.text() + '"' : t.text();
		}
		return $.map(t, function(li){return '"' + li.innerHTML + '"';}).join(', ');
	},

	/**
	 * @desc	select the file/folder
	 * @param	object	obj
	 * @param	boolean	set_filename	[default:false]
	 * @return	void
	 */
	do_select: function(obj, set_filename) {
		set_filename = set_filename || false;
		obj.addClass('selected');
		$('#filename_box').val(set_filename ? this.get_selected() : '');
	},

	/**
	 * @desc	Unselect all file(s)/folder(s), and clear the information
	 * @return	void
	 */
	do_unselect: function() {
		$('li.selected').removeClass('selected');
		$('#filename_box').val('');
		FilePicker.do_hide_info();
		$('#filename_box').focus();
	},

	/**
	 * @desc	Show the information box
	 * @param	object	evt
	 * @return	void
	 */
	do_show_info: function(evt) {
		var box = $('#info_box').addClass('info_box')
			.css('top', (evt.pageY + 10) + 'px')
			.css('left',(evt.pageX + 10) + 'px').fadeIn('fast');
	},

	/**
	 * @desc	Hide the information box
	 * @param	boolean	without_box
	 * @return	void
	 */
	do_hide_info: function(without_box) {
		var box = $('#info_box').empty();
		if (!without_box){
			box.hide();
		}
	},

	/**
	 * @desc	Get JSON string that be translated with all the selected file(s)
	 * @return	string
	 */
	do_translate_options: function() {
		$('#folders_tree option').each(function(){
			$(this).text($.base64.decode($(this).text()));
		});
	},

	/**
	 * @desc	Change the current folder to it parent
	 * @return	void
	 */
	do_up: function() {
		var dir = $.base64.decode($('#target_dir').val());
		var p = dir.lastIndexOf('/');
		if (p < 0 || dir == '/') return false;
		var s = dir.substr(0,p);
		s = (s == '') ? '/' : $.base64.encode(s);
		$('#folders_tree, #target_dir').val(s);
		FilePicker.get_list();
	},

	/**
	 * @desc	Deal with the incident of double-clicking on the file/folder
	 * @return	void
	 */
	do_dblclick: function(){
		var self = FilePicker;
		clearTimeout(self.timer);
		var elmt = $(this);
		if (elmt.attr('ftype') == 'folder'){
			var dir = $.base64.decode($('#target_dir').val());
			if (dir != '/') dir += '/';
			$('#folders_tree, #target_dir').val($.base64.encode(dir + elmt.text()));
			self.get_list();
		} else {
			self.do_select(elmt);
			self.do_complete();
		}
	},

	/**
	 * @desc	Deal with the incident of clicking on the file/folder
	 * @param	object	event
	 * @return	void
	 */
	do_click: function(evt) {
		var self = FilePicker;
		var elmt = $(this);
		self.do_hide_info();
		$('#filename_box').focus();
		if (!self.params.multi){
			self.do_unselect();
		}
		if (self.params.multi && elmt.attr('ftype') == 'folder'){
			if (!evt.shiftKey){
				// Don't remember this item, if SHIFT key was pushed down
				self.last_click = elmt.attr('id');
			}
			if (evt.ctrlKey){
				// Only one folder can be selected
				return false;
			}
			var t = $('li.selected').removeClass('selected');
			if (t.length == 1 && t.attr('id') == elmt.attr('id')){
				// Unselect current folder if it was selected
				self.do_unselect();
				return false;
			}
			self.do_select(elmt);
		} else {	// files
			if (evt.ctrlKey){
				self.last_click = elmt.attr('id');
				// Unselect folder(s)
				$('li.selected[ftype=folder]').removeClass('selected');
				// Select/Unselect current file
				$(this).toggleClass('selected');
				$('#filename_box').val(self.get_selected());
			} else if (self.params.multi && evt.shiftKey){
				// To delay the operation
			} else {
				self.last_click = elmt.attr('id');
				var t = $('li.selected').removeClass('selected');
				if (t.length == 1 && t.attr('id') == elmt.attr('id')){
					// Unselect current file if it was selected
					self.do_unselect();
					return false;
				}
				self.do_select(elmt, true);
			}
		}
		if (self.params.multi && evt.shiftKey){
			if (!self.last_click){
				// Select current item, if no one selected
				self.last_click = elmt.attr('id');
				self.do_select(elmt);
			} else {
				self.do_unselect();
				var first_id = parseInt(self.last_click.split('_')[1]);
				var this_id = parseInt(elmt.attr('id').split('_')[1]);
				if (first_id > this_id){
					$('#list > li').slice(this_id, first_id + 1).each(function(){
						FilePicker.do_select($(this));
					});
				} else {
					$('#list > li').slice(first_id, this_id + 1).each(function(){
						FilePicker.do_select($(this));
					});
				}
			}
			// Unselect Folder(s)
			$('li.selected[ftype=folder]').removeClass('selected');
			$('#filename_box').val(self.get_selected());
		}

		/**
		 * @desc	Make sure click or dblclick will work alone.
		 * 			Click will be disabled if the event was dblclick.
		 * @since	1.0.2
		 */
		clearTimeout(self.timer);
		self.timer = setTimeout(function () {
			self.get_info(evt);
		}, self.params.delay);
		return false;
	},

	/**
	 * @desc	Get infomation of the selected file/folder
	 * @return	void
	 */
	get_info: function(evt) {
		this.do_hide_info(true);
		var t = $('li.selected');
		if (t.length == 1){
			// Initialize the information box
			this.do_show_info(evt);
			$('<img />').attr('id', 'info_loading_img').attr('src', $('#loading_img')
				.attr('src')).appendTo('#info_box');
			$.ajax({
				data:{
					action: 'info',
					dir: $('#target_dir').val(),
					file: $.base64.encode(t.text())
				},
				success: function(json){
					var self = FilePicker;
					$('#info_box').html($('<label></label>').attr('id', 'btn_close')
						.text('X').click(function(){self.do_hide_info(false);}));
					var i = 0;
					$.each(json, function(i, item){
						if ($('#info_box').css('display') == 'none') return false;
						if (item.key == 'preview'){
							var src = self.get_uri() + '/' + $.base64.decode(item.value);
							$('<img />').attr('id', 'preview_img')
								.attr('alt', item.trans).attr('src', src)
								.click(function(){window.open(this.src,'_blank','');})
								.prependTo('#info_box');
						} else {
							if (i == 0){
								item.value = $.base64.decode(item.value);
							}
							$('#info_box').append(
								'<strong>' + item.trans + '</strong>:<br />' + 
								' &nbsp; ' + item.value + '<br />'
							);
						}
						i++;
					});
					/**
					 * @desc	Add a "Open in a new window" link in the detail box, except folder
					 * @since	1.1
					 */
					if (t.attr('class').indexOf('folder') !== -1) return ;
					var folder = $.base64.decode($('#target_dir').val());
					folder = self.params.uri + (folder == '/' ? '' : folder);
					$('#info_box').append(
						'&lt;<em><a href="' + folder + '/' + t.text() + '" target="_blank">Open in a new window</a></em>&gt;'
					);
				}
			});
		}
	},

	/**
	 * @desc	Get file(s)/folder(s) list of current directory
	 * @param	boolean	read_cache	[default:true]
	 * @return	void
	 */
	get_list: function(read_cache) {
		if ( typeof(read_cache) == 'undefined' ) read_cache = true;
		// Clean memory when the list was change
		this.last_click = null;
		this.do_hide_info();
		$('#loading_img').show();
		$('#list').empty();
		$('#filename_box').val('');
		$.ajax({
			cache: read_cache,
			data: {
				action: 'list',
				dir: $('#target_dir').val(),
				filter: $('#filter_box').val()
			},
			success: function(json){
				var self = FilePicker;
				$('#loading_img').hide();
				// To store filename(s) that be used for Auto-Complete
				var files = [];
				$.each(json, function(i,item){
					item.name = $.base64.decode(item.name);
					$('<li></li>').attr('id','item_'+i).attr('ftype',item.type)
						.attr('title', item.name).html(item.name)
						.addClass(item.type).click(self.do_click)
						.dblclick(self.do_dblclick).appendTo('#list');
					if (self.params.auto_complete == true && item.type != 'folder') {
						files.push(item.name);
					}
				});
				if (self.params.auto_complete == true) {
					$('#filename_box').autocompleteArray(files, {onItemSelect: function(){
						// `Click` the file that selected from the list of Auto-Complete
						$('li:not(li[ftyp:folder])').each(function(){
							if ($(this).html() == $('#filename_box').val()){
								self.do_select($(this), true);
								$('#list_box').scrollTop(
									$('#list_box').scrollTop() + 
									$(this).position().top - 
									$('#list_box').position().top
								);
								return false;
							}
						});
					}});
				}
				self.do_unselect();
			}
		});
	},

	/**
	 * @desc	Bind all events that we need
	 * @return	void
	 */
	events_binder: function() {
		var self = FilePicker;
		$('body').bind('selectstart', function(){return false;});
		$('#file_picker_form').bind('submit', function(){return false;});
		$('#list_box').bind('click', self.do_unselect);
		$('#folders_tree').bind('change', function(){self.get_list(true);});
		$('#btn_refresh').bind('click', function(){self.get_list(false);});
		$('#btn_up').bind('click', self.do_up);
		$('#btn_complete').bind('click', self.do_complete);
		$('#btn_cancel').bind('click', self.do_close);
		$('#info_box').ppdrag();
	}

}
