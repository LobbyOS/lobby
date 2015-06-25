<?php
/**
 * FileSystem of Lobby
 * Retreive, Modify & Write Files inside Lobby
 */

namespace Lobby;

class FS {
  
  
  public static function init(){
    
  }
  
  /**
   * Make relative path of Lobby to Absolute Path
   */
  public static function loc($path, $localized = true){
    $new = str_replace(L_DIR, "", $path);
    if(!defined("APP_DIR") || $localized === false){
      $new = L_DIR . $new;
    }else{
      $new = APP_DIR . $new;
    }
    return str_replace("\\", "/", $new);
  }
  
  /**
   * Check if a File/Dir exist
   */
  public static function exists($path){
    return file_exists(self::loc($path));
  }
  
  public static function get($file){
    return file_get_contents(self::loc($file));
  }
  
  /**
   * Write Contents to a file. There are 2 types of writing :
   * w - Write
   * a - Append
   */
  public static function write($path, $content, $type = "w"){
    $file = self::loc($path, false);
    if($type == "w"){
      $fh = fopen($file, 'w');
      $status = fwrite($fh, $content);
      fclose($fh);
      return $status === false || $status == 0 ? false : true;
    }elseif($type == "a"){
      $fh = fopen($file, 'a');
      $status = fwrite($fh, "$content\n");
      fclose($fh);
      return $status === false || $status == 0 ? false : true;
    }
  }
  
  public static function remove($path){
    return unlink(self::loc($path));
  }
}
//\Lobby\FS::init();
?>
