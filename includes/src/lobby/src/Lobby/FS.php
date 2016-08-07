<?php
namespace Lobby;

use Lobby\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * FileSystem of Lobby
 * Retreive, Modify & Write Files inside Lobby
 * Important : Only works for paths INSIDE Lobby
 */

class FS {

  /**
   * symfony/filesystem object
   */
  public static $fs;

  public static function __constructStatic(){
    self::$fs = new Filesystem();
  }
  
  /**
   * Make relative path of Lobby to Absolute Path
   */
  public static function loc($path){
    /**
     * If path is absolute, make it relative
     */
    $new = str_replace(L_DIR, "", $path);
    
    /**
     * For Windows
     * Replace backslash with forward slash
     */
    $new = str_replace("\\", "/", $new);
    
    /**
     * Remove slash at the beginning
     */
    $new = ltrim($new, "/");
    
    /**
     * Make path absolute
     */
    $new = L_DIR . "/" . $new;
    
    return $new;
  }
  
  /**
   * Make absolute path to relative path
   */
  public static function rel($path){
    $path = self::loc($path);
    return rtrim(self::$fs->makePathRelative($path, L_DIR), '/');
  }
  
  /**
   * Check if a File/Dir exists
   */
  public static function exists($path){
    return file_exists(self::loc($path));
  }
  
  public static function get($file){
    $contents = file_get_contents(self::loc($file));
    return $contents == false ? false : $contents;
  }
  
  /**
   * Write Contents to a file. There are 2 types of writing :
   * w - Write (Overwrite if exists)
   * a - Append
   */
  public static function write($path, $content, $type = "w"){
    $path = self::loc($path, false);
    
    /**
     * Append newline at EOF
     */
    if($type === "a"){
      if(file_exists($path))
        $content = file_get_contents($path) . $content . "\n";
      else
        $content .= "\n";
    }
    self::$fs->dumpFile($path, $content);
  }
  
  /**
   * Recursively remove a directory or remove a file
   * @param string $path Path to delete
   * @param array $exclude Files (relative paths) to exclude from deletion
   * @param bool $removeParent Whether the main directory along with it's contents should be removed 
   */
  public static function remove($path, $exclude = array(), $removeParent = true){
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
      if($removeParent){
        rmdir($path);
      }
      return true;
    }else{
      return unlink($path);
    }
  }
  
  /**
   * Bytes to KB, MB, GB converter
   * Base is 1000
   * @param int $size The size in bytes
   */
  public static function normalizeSize($size){
    $base = 1000;
    
    $sizeBase = log($size) / log($base);
    $suffixes = array("", "KB", "MB", "GB", "TB");
    $flooredBase = floor($sizeBase);
    
    if($flooredBase == 0){
      return round(round(pow($base, $sizeBase - $flooredBase), 1) / $base, 1) . "KB";
    }else{
      return round(pow($base, $sizeBase - $flooredBase), 1) . $suffixes[$flooredBase];
    }
  }
  
  /**
   * Get the size
   * @param $path The path
   * @param $normalizeSize Whether to run self::normalizeSize() on return
   * @return integer
   */
  public static function getSize($path, $normalizeSize = false) {
    $path = self::loc($path);
    $size = 0;
    
    if(is_dir($path)){
      foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file){
        $size += $file->getSize();
      }
      return $normalizeSize ? self::normalizeSize($size, true) : $size;
    }else{
      $size = filesize($path);
      return $normalizeSize ? self::normalizeSize($size, true) : $size;
    }
  }
  
  /**
   * Create a temporary file
   * Dir : contents/extra
   */
  public static function getTempFile(){
    return self::$fs->tempnam(L_DIR . "/contents/extra", "lobby_temp");
  }
  
}
