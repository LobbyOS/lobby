<?php
function makeOSPath($path){
  $path = base64_decode($path);
  if(DIRECTORY_SEPARATOR === '\\'){
    // Windows
    $drive = substr(L_DIR, 0, 2);
    if($path == "/" || $path == ""){
      $path = $drive . "\\";
    }else{
      //$path = substr_replace($path, "$drive/", 0, 1);
      $path = str_replace('/', '\\', $path);
    }
  }else{
    if($path == "/" || $path == ""){
      $path = "/";
    }else{
      $path = str_replace('\\', '/', $path);
    }
  }
  return $path;
}

$homeFolder = \Lobby::getSysInfo("home_folder");
// Folder path
if($homeFolder === "/home/www-data")
  define('FP_ROOT_PATH', makeOSPath("/"));
else
  define('FP_ROOT_PATH', makeOSPath($homeFolder));

if(\Lobby\Modules::exists("indi")){
  /**
   * If `indi` module is enabled, don't use FilePicker
   */
  return false;
  exit;
}

if(isset($_GET['img'])){
  $img_path = FP_ROOT_PATH . base64_decode($_GET['img']);

  $img = getimagesize($img_path);
  if(is_array($img)){
    $fp = fopen($img_path, 'rb');
    header('Content-Type: ' . $size['mime']);
    header('Content-Length: ' . filesize($img_path));
    fpassthru($fp);
    exit;
  }
}else{
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

  // Language [Default: en]
  define('FP_LANGUAGE', 'en');

  // Date format [Default: Y-m-d]
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

  define('FP_SCRIPT_ROOT', dirname(__DIR__));
  define('FP_CLASS_ROOT', FP_SCRIPT_ROOT . '/inc');

  require_once(FP_CLASS_ROOT . '/FilePicker.php');

  $fp = new FilePicker(function($e){
    if($e === "permission_denied"){
      echo $e;
    }
    exit;
  });
  $action = \Request::get('action');

  switch ($action){
    case 'list':
      $dir = base64_encode(makeOSPath(\Request::postParam("dir", "/")));
      $filter = isset($_POST['filter']) ? $_POST['filter'] : 0;
      echo $fp->get_list($dir, $filter);
      break;
    case 'info':
      $dir = base64_encode(makeOSPath(\Request::postParam("dir", "/")));
      $file = \Request::postParam("file", "");
      echo $fp->get_info($dir, $file);
      break;
  /*
    case 'new':
      $dir = $_POST['dir'] ? $_POST['dir'] : '/';
      $folder = $_POST['folder'] ? $_POST['folder'] : 'New Folder';
      $fp->new_folder($dir, $folder);
      break;
  */
    default :
      $filter = \Request::get('filter');
      $filters = '';
      $filters = $fp->get_filters($filter);

      $dir = makeOSPath(\Request::postParam("dir", "/"));
      $dir_b64 = base64_encode($dir);
      ob_start();
  ?>
      <div class="Lobby-FS-filepicker-picker-nav">
        <table cellspacing="0" cellpadding="0"><tr>
          <td class="label"><label><?php _e('Folder'); ?></label></td>
          <td>
            <input type="text" id="target_dir_path" value="<?php echo $dir_b64;?>" />
            <input type="hidden" id="target_dir" value="<?php echo $dir_b64;?>" />
          </td>
          <td>
            <li><img id="btn_refresh" src="<?php echo L_URL;?>/includes/lib/modules/filepicker/image/refresh.svg" alt="<?php _e('Refresh'); ?>" /></li>
            <li><img id="btn_up" src="<?php echo L_URL;?>/includes/lib/modules/filepicker/image/up.svg" alt="<?php _e('Up'); ?>" /></li>
          </td>
        </tr></table>
      </div>
      <div class="Lobby-FS-filepicker-picker-body row">
        <div id="viewbox" class="col l8">
          <img id="loading_img" src="<?php echo L_URL;?>/includes/lib/modules/filepicker/image/loading.gif" alt="<?php _e('Loading...'); ?>" />
          <ul id="list"></ul>
        </div>
        <div id="info_box" class="col l8"></div>
      </div>
      <div class="Lobby-FS-filepicker-picker-footer">
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td class="label"><label><?php _e('Filter'); ?></label>:</td>
            <td><select id="filter_box" class="select"><?php echo $filters; ?></select></td>
          </tr>
        </table>
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td class="label"><label for="filename_box"><?php _e('Filename'); ?></label>:</td>
            <td><input type="text" id="filename_box" name="filename" value="" class="select2" /></td>
            <td>
              <input type="button" id="btn_complete" value="<?php _e('OK'); ?>" class="btn green" />&nbsp;
              <input type="button" id="btn_cancel" value="<?php _e('Cancel'); ?>" class="btn red" />
            </td>
          </tr>
        </table>
      </div>
  <?php
      $html = ob_get_contents();
      ob_end_clean();
      echo json_encode(array(
        "dir" => FP_ROOT_PATH,
        "html" => $html
      ));
    continue;
  }
}
