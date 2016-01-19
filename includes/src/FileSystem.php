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
  public static function loc($path){
    $new = str_replace(L_DIR, "", $path);
    $new = L_DIR . $new;
    
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
  
  public static function remove($path, $exclude = array(), $remove_parent = true){
    $path = self::loc($path);
    
    if(is_dir($path)){
      $it = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
      $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
      foreach($files as $file) {
        $file_name = $file->getFilename();
        $file_path = $file->getRealPath();
        if ($file_name === '.' || $file_name === '..' || in_array($file_path, $exclude)) {
            continue;
        }
        if ($file->isDir()){
          rmdir($file_path);
        } else {
          unlink($file_path);
        }
      }
      if($remove_parent){
        rmdir($path);
      }
      return true;
    }else{
      return unlink($path);
    }
  }
}
//\Lobby\FS::init();
