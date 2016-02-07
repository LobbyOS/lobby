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

class FilePicker {

	/**
	 * @desc	To store all folders
	 * @access	private
	 * @type	array
	 */
	var $folders;

	/**
	 * @desc	To store all files
	 * @access	private
	 * @type	array
	 */
	var $files;

	/**
	 * @desc	To store description of each filter
	 * @access	private
	 * @type	array
	 */
	var $filters;

	/**
	 * @desc	To store extensions of each filter
	 * @access	private
	 * @type	array
	 */
	var $filters_exts;

	/**
	 * @desc	Object of JSON parser
	 * @access	private
	 * @type	object
	 */
	private $json = true;
  
  /**
   * A callback to receive errors
   */
  public $callback;


	/**
	 * @desc	Constructor
	 * @access	public
	 * @return	void
	 */
	function FilePicker($cb = ""){
		$this->callback = $cb == "" ? function($e){} : $cb;
    
    $this->filters = array(
			__('All files'),
			__('Images'),
			__('Documents'),
			__('Archives'),
			__('Flash files'),
			__('Audio files'),
			__('Video files')
		);
		$this->filters_exts = array(
			'',
			array('bmp', 'jpg', 'gif', 'png'),
			array('txt', 'rtf', 'pdf', 'doc', 'xls', 'ppt'),
			array('zip', 'rar', 'tar', 'gz', '7z'),
			array('swf', 'flv', 'fla'),
			array('wav', 'wma', 'mp3', 'mid'),
			array('avi', 'wmv', 'rm', 'rmvb', 'mpeg', 'mp4')
		);
	}

	/**
	 * @desc	Get list by $filter (include files and folders)
	 * @param	string	$dir
	 * @param	integer	$filter	[default:0]	[range:0,1,2,3,4,5,6]
	 * @access	public
	 * @return	string
	 */
	function get_list($dir, $filter = 0){
		if (!$dir = $this->do_check($dir)) return '';
		$this->read_dir($dir);
		$filter = round(abs($filter));
		$filters = count($this->filters_exts);
		if ($filter > $filters) $filter = $filters - 1;
		if ($filter < 0) $filter = 0;
		$list = array();
		for ($i=0, $l=count($this->folders); $i<$l; $i++){
			$list[] = array('name' => base64_encode($this->folders[$i]), 'type' => 'folder');
		}
		for ($i=0, $l=count($this->files); $i<$l; $i++){
			$ext = $this->get_extension($this->files[$i]);
			if ($filter == 0 || in_array($ext, $this->filters_exts[$filter])){
				$list[] = array('name' => base64_encode($this->files[$i]), 'type' => $ext);
			}
		}
		return $this->do_json_encode($list);
	}

	/**
	 * @desc	Get information of $file under $dir
	 * @param	string	$dir
	 * @param	string	$file
	 * @access	public
	 * @return	string
	 */
	function get_info($dir, $file){
		if (!$dir = $this->do_check($dir)) return '';
		$filename = $dir . '/' . base64_decode($file);
		if (file_exists($filename)){
			$info = array();
			$info[] = array(
				'key' => 'name', 
				'trans' => __('Name'), 
				'value' => $file
			);
			if (is_dir($filename)){
				$this->read_dir($filename);
				$info[] = array(
					'key' => 'folders', 
					'trans' => __('Folder(s)'), 
					'value' => count($this->folders)
				);
				$info[] = array(
					'key' => 'files', 
					'trans' => __('File(s)'), 
					'value' => count($this->files)
				);
			} elseif (is_file($filename)){
				if (in_array($this->get_extension($filename), $this->filters_exts[1])){
					$info[] = array(
						'key' => 'preview', 
						'trans' => __('Preview'), 
						'value' => $file
					);
				}
				$info[] = array(
					'key' => 'size', 
					'trans' => __('File Size'), 
					'value' => $this->format_size(filesize($filename), FP_DECIMAL_NUM, FP_DECIMAL, FP_THOUSAND)
				);
				$info[] = array(
					'key' => 'modify', 
					'trans' => __('Last Modified'), 
					'value' => date(FP_DATE . ' ' . FP_TIME, filemtime($filename))
				);
			}
			$info[] = array(
				'key' => 'permission', 
				'trans' => __('Permission'), 
				'value' => $this->get_permission($filename)
			);
			return $this->do_json_encode($info);
		}
		return '';
	}


	/**
	 * @desc	Make sure $dir is under FP_ROOT_PATH, and it really exist
	 * @param	string	$dir
	 * @access	private
	 * @return	boolean
	 */
	function do_check($dir){
		$dir = base64_decode($dir);
		$dir = (strpos($dir, FP_ROOT_PATH) === 0) ? $dir : FP_ROOT_PATH . $dir;
		if (file_exists($dir)){
			return $dir;
		}
		return false;
	}

	/**
	 * @desc	To encode $obj into JSON format
	 * @param	[mixed]	$obj
	 * @access	private
	 * @return	string
	 */
	function do_json_encode($obj){
		if ($this->json === true){
			return json_encode($obj);
		} elseif (is_object($this->json)){
			return $this->json->encode($obj);
		}
		return '["Unencode"]';
	}

	/**
	 * @desc	Get folder-tree of $dir (non-recursive)
	 * @param	string	$dir	[default:FP_ROOT_PATH]
	 * @param	string	$level	[default:0]
	 * @access	private
	 * @since	1.1
	 * @return	string
	 */
	function get_tree($dir=FP_ROOT_PATH, $level=0){
		//if (FP_DIR_LEVEL !== -1 && $level >= FP_DIR_LEVEL) {
		//	return '';
		//}
		$tree = '';
		if (is_dir($dir)){
			for ($i=0,$prefix=''; $i<=$level; $i++) $prefix .= '...';
			if ($dh = opendir($dir)){
				while (($file = readdir($dh)) !== false){
					if ($file == '.' || $file == '..') continue;
					$filename = $dir . '/' . $file;
					if (is_dir($filename)){
						$tree .= '<option value="' . base64_encode(str_replace(FP_ROOT_PATH, '', $filename)) . '">' . base64_encode($prefix . '|- ' . $file) . '</option>';
						$tree .= $this->get_tree($filename, $level+1);
					}
				}
				closedir($dh);
			}
		}
		return $tree;
	}

	/**
	 * @desc	Get filters list that can be selected by client-side user
	 * @param	integer	$filter	[default:31]	[range:1,2,3...126,127]
	 * @access	private
	 * @return	string
	 */
	function get_filters($filter = 31){
		if ($filter <=0 || $filter > 127) $filter = 31;
		$filters = '';
		$i = 0;
		foreach($this->filters as $item){
			if (pow(2,$i) & $filter){
				$filters .= '<option value="' . $i . '">' . $item . '</option>';
			}
			$i++;
		}
		return $filters;
	}

	/**
	 * @desc	Read in all files and folders in $dir
	 * @param	string	$dir
	 * @access	private
	 * @return	void
	 */
	function read_dir($dir){
		if (is_dir($dir)){
			if(is_readable($dir)){
        if ($dh = opendir($dir)){
          while (($file = readdir($dh)) !== false){
            if ($file == '.' || $file == '..') continue;
            $filename = $dir . '/' . $file;
            if (is_dir($filename)) $this->folders[] = $file;
            if (is_file($filename)) $this->files[] = $file;
          }
          closedir($dh);
        }
      }else{
        $this->callback("permission_denied");
      }
		}
		@sort($this->folders);
		@sort($this->files);
	}

	/**
	 * @desc	Get filename of $filename
	 * @param	string	$filename
	 * @access	private
	 * @return	string
	 */
	function get_permission($file){
		$perms = fileperms($file);

		if (($perms & 0xC000) == 0xC000){
			// Socket
			$info = 's';
		} elseif (($perms & 0xA000) == 0xA000){
			// Symbolic Link
			$info = 'l';
		} elseif (($perms & 0x8000) == 0x8000){
			// Regular
			$info = '-';
		} elseif (($perms & 0x6000) == 0x6000){
			// Block special
			$info = 'b';
		} elseif (($perms & 0x4000) == 0x4000){
			// Directory
			$info = 'd';
		} elseif (($perms & 0x2000) == 0x2000){
			// Character special
			$info = 'c';
		} elseif (($perms & 0x1000) == 0x1000){
			// FIFO pipe
			$info = 'p';
		} else {
			// Unknown
			$info = 'u';
		}

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

		return $info;
	}

	/**
	 * @desc	Get extension of $filename
	 * @param	string	$file
	 * @access	private
	 * @return	string
	 */
	function get_extension($file){
		$ext = '';
		$pos = strrpos($file, ".");
		if (++$pos){
			$ext = substr($file, $pos, strlen($file));
		}
		return strtolower($ext);
	}

	/**
	 * @desc	Format bit size
	 * @param	integer	$size
	 * @param	integer	$decimals	[default:2]
	 * @param	string	$decimal	[default:.]
	 * @param	string	$thousand	[default:,]
	 * @access	private
	 * @return	string
	 */
	function format_size($size, $decimals = 2, $decimal = '.', $thousand = ','){
		switch ($size){
			case ($size > 1073741824) :
				$size = number_format($size/1073741824, $decimals, $decimal, $thousand);
				$size .= ' GB';
				break;
			case ($size > 1048576) :
				$size = number_format($size/1048576, $decimals, $decimal, $thousand);
				$size .= ' MB';
				break;
			case ($size > 1024) :
				$size = number_format($size/1024, $decimals, $decimal, $thousand);
				$size .= ' KB';
				break;
			default :
				$size = number_format($size, $decimals, $decimal, $thousand);
				$size .= ' Bytes';
				break;
		}
		return $size;
	}

}

?>
