<?
class SiteCompressor {
 public $root, $curOptions;
 private $files          = array();
 public $fileRead        = array();
 private $defaultOptions = array(
  "site" => array(
   "location"     => "",
   "output"       => "",
   "replaceFrom"  => array(),
   "replaceTo"    => array(),
   "beforeCommand"=> "",
   "afterCommand" => ""
  ),
  "compress"      => array(
   "minHtml"      => false,
   "minPHP"       => false,
   "noComments"   => false,
   "minCss"       => false,
   "minJs"        => false,
   "minInline"    => false
  )
 );
 
 public function __construct($root){
  $this->root = $root;
 }
 
 /* Set the options send by the browser */
 public function makeOptions($siteOptions, $compressOptions){
  foreach($compressOptions as $key => $value){
   $compressOptions[$key]=true;
  }
  $this->curOptions = array_replace_recursive($this->defaultOptions, array(
   "site"     => $siteOptions,
   "compress" => $compressOptions
  ));
 }
 
 /* Check Options to see if they're right */
 public function checkOptions(){
  $opt = $this->curOptions;
  
  /* Site Details Check */
  $siteLocation = $opt["site"]["location"];
  $siteOutput   = $opt["site"]["output"];
  
  if($siteLocation==""){
   $this->ser("Site Location Not Given", "The absolute path of the site locations is not given");
  }elseif($siteOutput==""){
   $this->ser("Output Location Not Given", "The output path where the compressed site is written is not given");
  }elseif(!file_exists($siteLocation)){
   $this->ser("Site Location Not Found", "The site location path was not found");
  }elseif(!file_exists($siteOutput)){
   $this->ser("Site Location Not Found", "The site location path was not found");
  }elseif(!is_writable($siteOutput)){
   $this->ser("Output Path not writable", "The output path given is not writable for me. Set the permission of the output folder to Read & Write (777)");
  }elseif(!is_readable($siteLocation)){
   $this->ser("Site Path Not Readable", "The site path given is not readable for me. Set the permission of the site folder to Read (444)");
  }
 }
 
 /* Initiate Compressing */
 public function startCompress(){
  $path      = $this->curOptions["site"]["location"];
  $output	 = $this->curOptions["site"]["output"];
  $notWanted = array(".", "..");
  
  /* Execute before commands */
 if($this->curOptions["site"]["beforeCommand"]!=""){
     $this->status("Executing Terminal Command");
     system($this->curOptions["site"]["beforeCommand"]);
  }
      
  $this->status("Emptying Output Directory");
  /* Empty the Output Dir just in case */
  $this->recursiveRemoveDirectory($output);
  $this->status("Finished Emptying Output Directory");
 
  /* Make an array of found files */
  $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
  foreach($objects as $location => $object){
      $name = str_replace($path."/", "", $location);
      /* Check if the file is in the "not wanted" list */
      if(!array_search($name, $notWanted)){
          $outLoc = $output."/$name";
          if($object->isFile()){
              $type = $this->getMIMEType($location);
              $this->files[$type][]=$name;
              $this->status("Copying $name to Ouput Directory");
              copy($location, "$outLoc");
          }elseif($object->isDir() && !file_exists($outLoc)){
              /* Make sub directories on output folder */
              mkdir($outLoc);
          }
      }
   }
   if(count($this->files)==0){
    	 $this->ser("No Files Found", "No files found in the site directory to compress.");
   }else{
      $this->status("Started Compressing");
      
      /* Replace strings */
      if( isset($this->curOptions["site"]["replaceFrom"]) && isset($this->curOptions["site"]["replaceTo"]) ){
      	$this->replaceStrings();
      }
      
      /* We will proceed only if HTML, CSS, JS files are found */
      
      /* Start Compressing JS */
      if($this->curOptions["compress"]["minJs"] && isset($this->files["application/javascript"])){
        	$this->compressJS();
      }
      
      /* Start Compressing CSS */
      if($this->curOptions["compress"]["minCss"] && isset($this->files["text/css"])){
        	$this->compressCSS();
      }
      
      /* Start Compressing HTML, PHP */
      if($this->curOptions["compress"]["minHtml"] && isset($this->files["text/html"])){
        	$this->compressHTML();
      }
      
      /* Execute after commands */
      if($this->curOptions["site"]["afterCommand"]!=""){
      	$this->status("Executing Terminal Command");
      	system($this->curOptions["site"]["afterCommand"]);
      }
      $this->status("Finished Site Compression. Thank you. Hope everything went OK.");
   }
 }
 
 /* Show errors in HTML format */
 public function ser($title, $description=""){
     header('Content-type: text/html');
     echo "<h2>$title</h2>";
     echo "<p>$description</p>";
     exit;
 }
 
 /* Show success messages in HTML format */
 public function sss($title, $description=""){
     header('Content-type: text/html');
     echo "<h2>$title</h2>";
     echo "<p>$description</p>";
 }
 
 /* http://subinsb.com/php-find-file-mime-type */
 private function getMIMEType($filename){
    $finfo = new finfo;
    $mime = $finfo->file($filename, FILEINFO_MIME_TYPE);
  
    /* MIME Type is text/plain for .js and .css files, so we check if MIME type from finfo are right */
  
    if($mime=="text/plain"){
       $dots       = explode(".", $filename);
       $extension  = strtolower($dots[ count($dots)-1 ]);
       if($extension == "js"){
         $mime = "application/javascript";
       }elseif($extension == "css"){
         $mime = "text/css";
       }
    }
    return $mime;
 }
 
 /* Get file contents from file */
 private function input($file){
    $out = $this->curOptions["site"]["location"];
    $filename = "$out/$file";
    
    /* Has the file already been read ? */
    if(array_key_exists($file, $this->fileRead)){
    		$contents = base64_decode($this->fileRead[$file]);
    }else{
     	$contents = file_get_contents($filename);
     	$this->fileRead[$file] = base64_encode($contents);
    }
    return $contents;
 }
 
 /* Put in Output folder */
 private function output($name, $content){
    $out = $this->curOptions["site"]["output"];
    $location = "$out/$name";
    $this->fileRead[$name] = base64_encode($content);
    file_put_contents($location, $content);
 }
 
 /* Publish status to the browser */
 public function status($msg){
    $msg = date("H:i:s") . " - " . $msg;
    echo "<div class='status'>$msg</div>";
    /* Scroll the iframe to the bottom */
    echo "<script>document.getElementsByClassName( 'status' )[document.getElementsByClassName( 'status' ).length-1].scrollIntoView()</script>";
    ob_flush();
    flush();
 }
 
 /* Compressing Functions */
 
 /* Replace Strings */
 public function replaceStrings(){
  	$this->status("Started Replacing Strings");
  	$strings = array();
  	foreach($this->curOptions["site"]["replaceFrom"] as $index => $value){
  		if($value!=""){
  			$strings[$value] = $this->curOptions["site"]["replaceTo"][$index];
  		}
  	}
  	$files = $this->files;
  	foreach($files as $subFiles){
  	 	foreach($subFiles as $file){
  	 		$contents = $this->input($file);
  	 		$replaced = $contents;
  	 		foreach($strings as $from => $to){
  	 			$replaced = str_replace($from, $to, $replaced);
  	 		}
  	 		/* Check if content changed */
  	 		if($contents != $replaced){
  	 		 	$this->status("Replacing Strings in $file");
  	 		 	$this->output($file, $replaced);
  	 		}
  	 	}
  	}
  	$this->status("Finished Replacing Strings");
 }
 
 /* HTML */
 public function compressHTML(){
    $this->status("Started HTML Compression");
    $files = $this->files["text/html"];
    
    if($this->curOptions["compress"]["minPHP"] && isset($this->files["text/x-php"])){
     $files=array_merge($this->files["text/x-php"], $files);
    }
    foreach($files as $file){
        $this->status("Started Compressing <b>$file</b>");
        $code = $this->input($file);
        if(preg_match("/\<html\>/", $code)){ /* Only do it if it has <html> tag */
        		$minified = $this->_compressor("html", $code);
        		$this->output($file, $minified);
        }
        $this->status("Finished Compressing $file");
    }
    $this->status("Finished HTML Compression");
 }
 
 /* JS */
 public function compressJS(){
    $this->status("Started JavaScript Compression");
    $files = $this->files["application/javascript"];

    foreach($files as $file){
        $this->status("Started Compressing <b>$file</b>");
        $code = $this->input($file);
        $minified = $this->_compressor("js", $code);
        $this->output($file, $minified);
        $this->status("Finished Compressing $file");
    }
    $this->status("Finished JavaScript Compression");
 }
 
 /* CSS */
 public function compressCSS(){
    $this->status("Started CSS Compression");
    $files = $this->files["text/css"];

    foreach($files as $file){
        $this->status("Started Compressing <b>$file</b>");
        $code = $this->input($file);
        $minified = $this->_compressor("css", $code);
        $this->output($file, $minified);
        $this->status("Finished Compressing $file");
    }
    $this->status("Finished CSS Compression");
 }
 
 /* Compressor libraries */
 public function _compressor($language, $code=""){
    $root = realpath(__DIR__);
    require_once $root."/min-css.php";
    require_once $root."/min-js.php";
    require_once $root."/htmlCompress.php";
    
    if($language == "css") {
        /* What kind of css stuff should it convert */
        $plugins = array(
         "Variables"                => true,
         "ConvertFontWeight"        => true,
         "ConvertHslColors"         => true,
         "ConvertRgbColors"         => true,
         "ConvertNamedColors"       => true,
         "CompressColorValues"      => true,
         "CompressUnitValues"       => true,
         "CompressExpressionValues" => true
        );
        $minifier = new CssMinifier($code, array(), $plugins);
        return $minifier->getMinified();
    }elseif($language == "js"){
        $jSqueeze = new JSqueeze();
        return $jSqueeze->squeeze($code, true, false);
    }elseif($language == "html"){
        $html = new Tinyfier_HTML_Tool();
        if($this->curOptions['compress']["minInline"]){
            return $html->process($code, array("compress_all" => true));
        }else{
            return $html->process($code, array("compress_all" => false));
        }
    }
 }
 
 /* Recursive Directory Remover */
 public function recursiveRemoveDirectory($dir) {
   $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $file) {
    if ($file->getFilename() === '.' || $file->getFilename() === '..') {
        continue;
    }
    if ($file->isDir()){
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
	}
   if($dir != $this->curOptions["site"]["output"]){
    rmdir($dir);
   }
 }
}
?>