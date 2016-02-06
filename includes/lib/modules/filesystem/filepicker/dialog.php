<div class="Lobby-FS-filepicker">
  <?php
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
  
  // -------------------- Configration begin -------------------------
  
  // Folder path
  define('FP_ROOT_PATH', '/home/simsu');
  
  // Folder URI
  define('FP_ROOT_URI', 'http://localhost');
  
  // Langeuage [Default: en]
  define('FP_LANGUAGE', 'zh_CN');
  
  // Data format [Default: Y-m-d]
  define('FP_DATE', 'Y-m-d');
  
  // Time format [Default: H:i:s]
  define('FP_TIME', 'H:i:s');
  
  // Separator thousand [Default: ,]
  define('FP_THOUSAND', ',');
  
  // Decimal point [Default: .]
  define('FP_DECIMAL', '.');
  
  // Number of decimals to display [Default: 2]
  define('FP_DECIMAL_NUM', 2);
  
  // How many level of FP_ROOT_PATH that user can visit, 0 means root only [Default: 1]
  // 0 means root only, -1 means unlimited
  // @since: 1.1.1
  // (Not ready for 1.1 - Nov. 10, 2009)
  // define('FP_DIR_LEVEL', 1);
  
  // --------------------- Configration end --------------------------
  
  
  header('Content-Type: text/html; charset=UTF-8');
  
  define('FP_SCRIPT_ROOT', dirname(__FILE__));
  define('FP_CLASS_ROOT', FP_SCRIPT_ROOT . '/classes');
  
  require_once(FP_CLASS_ROOT . '/JSON.php');
  require_once(FP_CLASS_ROOT . '/FilePicker.php');
  
  $fp = new FilePicker();
  $action = \H::input('action');
  
  switch ($action){
    case 'list':
      $dir = isset($_GET['dir']) ? $_GET['dir'] : '/';
      $filter = isset($_GET['filter']) ? $_GET['filter'] : 0;
      echo $fp->get_list($dir, $filter);
      break;
    case 'info':
      $dir = $_GET['dir'] ? $_GET['dir'] : '/';
      $file = $_GET['file'] ? $_GET['file'] : '';
      echo $fp->get_info($dir, $file);
      break;
  /*
    case 'new':
      $dir = $_GET['dir'] ? $_GET['dir'] : '/';
      $folder = $_GET['folder'] ? $_GET['folder'] : 'New Folder';
      $fp->new_folder($dir, $folder);
      break;
  */
    default :
      $filter = \H::input('filter', 31);

      $filters = '';
      $filters = $fp->get_filters($filter);
      ob_start();
  ?>
      <form id="file_picker_form" name="file_picker_form">
        <div id="container">
          <div id="header">
            <table cellspacing="0" cellpadding="0"><tr>
              <td class="label"><label><?php _e('Folder'); ?></label>:</td>
              <td><select id="folders_tree" class="select"><option value="Lw==">Lw==</option></select><input type="hidden" id="target_dir" value="/" /></td>
              <td><ul>
                <li><img id="btn_refresh" src="<?php echo L_URL;?>/includes/lib/modules/filesystem/filepicker/image/refresh.gif" alt="<?php _e('Refresh'); ?>" /></li>
                <li><img id="btn_up" src="<?php echo L_URL;?>/includes/lib/modules/filesystem/filepicker/image/up.gif" alt="<?php _e('Up'); ?>" /></li>
              </ul></td>
            </tr></table>
          </div>
          <div id="body">
            <div id="list_box" class="order_list"><img id="loading_img" src="<?php echo L_URL;?>/includes/lib/modules/filesystem/filepicker/image/loading.gif" alt="<?php _e('Loading...'); ?>" /><ul id="list"></ul></div>
          </div>
          <div id="footer">
            <table cellspacing="0" cellpadding="0"><tr>
              <td class="label"><label for="filename_box"><?php _e('Filename'); ?></label>:</td>
              <td><input type="text" id="filename_box" name="filename" value="" class="select2" /></td>
              <td> &nbsp; <input type="button" id="btn_complete" value="<?php _e('OK'); ?>" class="btn" /></td>
            </tr></table>
            <table cellspacing="0" cellpadding="0"><tr>
              <td class="label"><label><?php _e('Filter'); ?></label>:</td>
              <td><select id="filter_box" onchange="get_list();" class="select"><?php echo $filters; ?></select></td>
              <td> &nbsp; <input type="button" id="btn_cancel" value="<?php _e('Cancel'); ?>" class="btn" /></td>
            </tr></table>
          </div>
        </div>
        <div id="info_box"></div>
      </form>
  <?php
    continue;
  }
  
  ?>
</div>
