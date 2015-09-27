<?php
/**
* Library to create simple graph charts
* 
* PHP version 5
* 
* LICENSE:
* 
* Free to use distribute or modify in any way.
* 
* THIS SOFTWARE IS PROVIDED BY THE AUTHOR ''AS IS''.
*
* @category		Images
* @package		graph
* @author		Martijn Beulens <mbeulens(at)bawic.nl>
* @thanksto		Tekin Ozbek (For pointing out configuration directive misspellings)
* @thanksto		Jack Finch (For right-aligment and round value range functionality)
* @license		Free to use, distribute or modify in any way.
* @version		5.0.5
*
* @updates
* 5.0.4: A display bug has been resolved which occured when all data
* values where below zero.
* 
*/
//##########################################################################
//# phpMyGraph
//##########################################################################
class phpMyGraph {

//-------------------------------------------------------------------------+

	/**
	 * parseVerticalColumnGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function parseVerticalColumnGraph($data, $cfg = array()) {
		$graph = self::factory('verticalColumnGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}

//-------------------------------------------------------------------------+

	/**
	 * parseVerticalLineGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	 public static function parseVerticalLineGraph($data, $cfg = array()) {
		$graph = self::factory('verticalLineGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}

//-------------------------------------------------------------------------+

	/**
	 * parseVerticalPolygonGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function parseVerticalPolygonGraph($data, $cfg = array()) {
		$graph = self::factory('verticalPolygonGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}

//-------------------------------------------------------------------------+

	/**
	 * parseHorizontalColumnGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function parseHorizontalColumnGraph($data, $cfg = array()) {
		$graph = self::factory('horizontalColumnGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}

//-------------------------------------------------------------------------+

	/**
	 * parseHorizontalLineGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function parseHorizontalLineGraph($data, $cfg = array()) {
		$graph = self::factory('horizontalLineGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}

//-------------------------------------------------------------------------+

	/**
	 * parseHorizontalPolygonGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function parseHorizontalPolygonGraph($data, $cfg = array()) {
		$graph = self::factory('horizontalPolygonGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}

//-------------------------------------------------------------------------+

	/**
	 * parseHorizontalSimpleColumnGraphGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function parseHorizontalSimpleColumnGraph($data, $cfg = array()) {
		$graph = self::factory('horizontalSimpleColumnGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}	

//-------------------------------------------------------------------------+

	/**
	 * parseVerticalSimpleColumnGraphGraph
	 *
	 * For backwards compatibility or easy usage
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function parseVerticalSimpleColumnGraph($data, $cfg = array()) {
		$graph = self::factory('verticalSimpleColumnGraph', $data, $cfg);
		$graph->parse($data, $cfg);
	}	
	
//-------------------------------------------------------------------------+

	/**
	 * factory
	 *
	 * Creates a graph
	 * 
	 * @param array $data
	 * @param optional array $cfg
	 * @return void
	 */
	public static function factory($type) {
		if(!class_exists($type)) {
			return false;
		}
		return new $type();
	}
	
//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# iGraph
//##########################################################################

/**
* @name			iGraph
* @type			interface
* @package      graph
* @version      5.0.1
* @comment:		Graph interface
*/
interface iGraph {
	public function parse($data, $cfg = array());
}

//##########################################################################
//# graph
//##########################################################################

/**
* @name			graphBase
* @type			class (abstract)
* @extends		none
* @package      graph
* @version      5.0.1
* @comment:		Graph super class
*/
abstract class graphBase implements iGraph {

	/**
	* Properties
	*/
		
	/**
	 * @name:		ip
	 * @access:		protected
	 * @since:		version 5.0.1
	 * @comment:	Image pointer
	*/
	protected $ip;

	/**
	 * @name:		cfg
	 * @access:		protected
	 * @since:		version 5.0.1
	 * @comment:	Config array
	*/
	protected $cfg;
	
//-------------------------------------------------------------------------+

	/**
	* Methods
	*/

	/**
	 * @name: __construct
	 * @param: none
	 * @return: void
	 * @access: public
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Constructs the graph
	*/
	public function __construct() {
	}
	
//-------------------------------------------------------------------------+

	/**
	 * @name: parse
	 * @param: (array) $data, [(array) $cfg]
	 * @return: void
	 * @access: public
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Parses the graph
	*/
	public function parse($data, $cfg=array()) {
	
		//Try block
		try {
			//Parse config etc
			$this->parseConfig($cfg);

			//Implement your code here
			throw new Exception('Method not implemented');
		}
		catch(Exception $ex) {
			//Parse error message overriding original
			$this->parseError($ex->__toString());
		}
		
		//Parse image
		$this->parseImage();
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: getConfigProperties
	 * @param: none
	 * @return: (array) configProperties
	 * @access: public
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Returns array of config properties
	*/	
	public static function getConfigProperties() {
		$properties = array(
			array('name' => 'type', 
				  'description' => 'Type for image output (png, jpg, gif)',
			      'type' => 'arrayValue', 
				  'default' => 'png', 
				  'values' => array('png','jpg','gif'), 
				  'casing' => 'lowercase'),
			array('name' => 'width',
				  'description' => 'Width for image', 
				  'type' => 'numeric', 
				  'default' => 500),
			array('name' => 'height', 
				  'description' => 'Height for image', 
			      'type' => 'numeric', 
				  'default' => 200),
			array('name' => 'background-color', 
				  'description' => 'Background color', 
				  'type' => 'color', 
			      'default' => 'FFFFFF'),
			array('name' => 'background-image', 
				  'description' => 'Background image', 			
				  'type' => 'file', 
				  'default' => ''),
			array('name' => 'title',
				  'description' => 'Title for graph', 			 
				  'type' => 'text', 
				  'default' => ''),
			array('name' => 'title-visible', 
				  'description' => 'Set title visibility', 			
				  'type' => 'boolean', 

				  'default' => true),
			array('name' => 'title-font-size', 
				  'description' => 'Title font size (1, 2, 3, 4, 5, 6)', 			
			      'type' => 'arrayValue', 
				  'default' => 2, 
				  'values' => array(1, 2, 3, 4, 5, 6)),
			array('name' => 'title-color', 
				  'description' => 'Title color (Use HEX)', 			
				  'type' => 'color', 
				  'default' => '000000'),
			array('name' => 'zero-line-visible', 
				  'description' => 'Set zero line visibility', 				
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'zero-line-color', 
				  'description' => 'Zero line color', 			
				  'type' => 'color', 
				  'default' => '000000'),
			array('name' => 'zero-line-alpha', 
				  'description' => 'Zero line alpha', 			
				  'type' => 'numeric', 
				  'default' => 0),
			array('name' => 'average-line-visible', 
				  'description' => 'Set average line visibility', 			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'average-line-color', 
				  'description' => 'Average line color (Use HEX)', 			
				  'type' => 'color', 
				  'default' => '0000FF'),
			array('name' => 'average-line-alpha', 
				  'description' => 'Average line alpha', 			
				  'type' => 'numeric', 
				  'default' => 0),
			array('name' => 'key-color', 
				  'description' => 'Key color (Use HEX)', 			
				  'type' => 'color', 
				  'default' => '006699'),
			array('name' => 'key-visible', 
				  'description' => 'Set key visibility', 			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'key-font-size', 
				  'description' => 'Set key font size (1, 2, 3, 4, 5, 6)', 			
				  'type' => 'arrayValue', 
				  'default' => 2, 
				  'values' => array(1, 2, 3, 4, 5, 6)),
			array('name' => 'key-right-align', 
				  'description' => 'Right Align Keys', 			
				  'type' => 'boolean', 
				  'default' => false),
			array('name' => 'label', 
				  'description' => 'Label text',			
				  'type' => 'text', 
				  'default' => ''),
			array('name' => 'label-color', 
				  'description' => 'Label color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '000000'),
			array('name' => 'label-visible', 
				  'description' => 'Set label visibility',			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'label-font-size', 
				  'description' => 'Label font size (1, 2, 3, 4, 5, 6)',			
				  'type' => 'arrayValue', 
				  'default' => 2, 
				  'values' => array(1, 2, 3, 4, 5, 6)),
			array('name' => 'label-right-align', 
				  'description' => 'Right Align Labels', 			
				  'type' => 'boolean', 
				  'default' => false),
			array('name' => 'value-visible', 
				  'description' => 'Set value visibility',			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'value-font-size', 
				  'description' => 'Value font size (1, 2, 3, 4, 5, 6)',			
				  'type' => 'arrayValue', 
				  'default' => 2, 
				  'values' => array(1, 2, 3, 4, 5, 6)),
			array('name' => 'value-color', 
				  'description' => 'Value color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '000000'),
			array('name' => 'value-label-visible', 
				  'description' => 'Set value label visibility',			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'value-label-font-size', 
				  'description' => 'Value label font size',			
				  'type' => 'arrayValue', 
				  'default' => 2, 
				  'values' => array(1, 2, 3, 4, 5, 6)),
			array('name' => 'value-label-color', 
				  'description' => 'Value label color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '006699'),
			array('name' => 'box-border-color', 
				  'description' => 'Box border color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '006699'),
			array('name' => 'box-border-alpha', 
				  'description' => 'Box border alpha',			
				  'type' => 'numeric', 
				  'default' => 0),
			array('name' => 'box-border-visible', 
				  'description' => 'Set box border visibility',			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'box-background-color', 
				  'description' => 'Box background color (Use HEX)',			
				  'type' => 'color', 
				  'default' => 'F1F1F1'),
			array('name' => 'box-background-visible', 
				  'description' => 'Box background visibility',			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'box-background-alpha', 
				  'description' => 'Box background alpha',			
				  'type' => 'numeric', 
				  'default' => 0),
			array('name' => 'column-divider-color', 
				  'description' => 'Column divider color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '000000'),
			array('name' => 'column-divider-alpha', 
				  'description' => 'Column divider alpha',			
				  'type' => 'numeric', 
				  'default' => 100),
			array('name' => 'column-divider-visible', 
				  'description' => 'Set column divider visibility',			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'horizontal-divider-color', 
				  'description' => 'Horizontal divider color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '000000'),
			array('name' => 'horizontal-divider-alpha', 
				  'description' => 'Horizontal divider alpha',			
				  'type' => 'numeric', 
				  'default' => 100),
			array('name' => 'horizontal-divider-visible', 
				  'description' => 'Set horizontal divider visiblity',			
				  'type' => 'boolean', 
				  'default' => true),
			array('name' => 'column-color-random', 
				  'description' => 'Create random column color',			
				  'type' => 'boolean', 
				  'default' => false),
			array('name' => 'column-color', 
				  'description' => 'Column color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '0099CC'),
			array('name' => 'column-alpha', 
				  'description' => 'Column alpha',			
				  'type' => 'numeric', 
				  'default' => 0),
			array('name' => 'column-shadow-alpha', 
				  'description' => 'Column shadow alpha',			
				  'type' => 'numeric', 
				  'default' => 0),
			array('name' => 'column-shadow-color', 
				  'description' => 'Column shadow color (Use HEX)',			
				  'type' => 'color', 
				  'default' => '006699'),
			array('name' => 'column-compare-color', 
				  'description' => 'Compare column color (Use HEX)',			
				  'type' => 'color', 
				  'default' => 'FF0000'),
			array('name' => 'column-compare-shadow-color', 
				  'description' => 'Compare column shadow color (Use HEX)',			
				  'type' => 'color', 
				  'default' => 'FF0000'),
			array('name' => 'file-name', 
				  'description' => 'Filename to parse to',			
				  'type' => 'text', 
				  'default' => NULL),
			array('name' => 'jpg-quality', 
				  'description' => 'JPG output quality',			
				  'type' => 'numeric', 
				  'default' => 60),
			array('name' => 'round-value-range', 
				  'description' => 'Round Range Values',			
				  'type' => 'boolean', 
				  'default' => false)		  
		);
		
		return $properties;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: getExampleUsage
	 * @param: none
	 * @return: (array) configProperties
	 * @access: public
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Returns an usage example
	*/	
	public function getExampleUsage() {
		//Var
		$phpCode = '<?php';

		//Set config
		$phpCode .= "\t\n";
		$phpCode .= "\t//Set content-type header\n";
		$phpCode .= "\theader(\"Content-type: image/png\");\n\n";
		$phpCode .= "\t//Include phpMyGraph5.0.php\n";
		$phpCode .= "\tinclude_once('phpMyGraph5.0.php');\n";
		$phpCode .= "\t\n";
		$phpCode .= "\t//Set config directives\n";
		$phpCode .= "\t\$cfg['title'] = 'Example graph';\n";
		$phpCode .= "\t\$cfg['width'] = 500;\n";
		$phpCode .= "\t\$cfg['height'] = 250;\n";
		$phpCode .= "\t\n";
		$phpCode .= "\t//Set data\n";
		$phpCode .= "\t\$data = array(\n";
		$phpCode .= "\t	'Jan' => 12,\n";
		$phpCode .= "\t	'Feb' => 25,\n";
		$phpCode .= "\t	'Mar' => 0,\n";
		$phpCode .= "\t	'Apr' => 7,\n";
		$phpCode .= "\t	'May' => 80,\n";
		$phpCode .= "\t	'Jun' => 67,\n";
		$phpCode .= "\t	'Jul' => 45,\n";
		$phpCode .= "\t	'Aug' => 66,\n";
		$phpCode .= "\t	'Sep' => 23,\n";
		$phpCode .= "\t	'Oct' => 23,\n";
		$phpCode .= "\t	'Nov' => 78,\n";
		$phpCode .= "\t	'Dec' => 23\n";
		$phpCode .= "\t);\n";
		$phpCode .= "\t\n"; 
		
		/*
		$phpCode .= "//NEW WAY\n";
		$phpCode .= "	//Create instance\n";
		$phpCode .= "	\$graph = new ".get_class($this)."();\n";
		$phpCode .= "\n";
		$phpCode .= "	//Parse\n";
		$phpCode .= "	\$graph->parse(\$data, \$cfg);\n";
		$phpCode .= "\n";
		$phpCode .= "//FACTORY WAY\n";
		$phpCode .= "	//Create instance via factory\n";
		$phpCode .= "	\$graph = phpMyGraph::factory('horizontalLineGraph');\n";
		$phpCode .= "\n";
		$phpCode .= "	//Parse\n";
		$phpCode .= "	\$graph->parse(\$data, \$cfg);\n";
		$phpCode .= "\n";		
		$phpCode .= "//BACKWARDS COMPATIBILITY WAY\n";
		*/
		
		$phpCode .= "	//Create phpMyGraph instance\n";
		$phpCode .= "	\$graph = new phpMyGraph();\n";
		$phpCode .= "\n";
		$phpCode .= "	//Parse\n";
		$phpCode .= "	\$graph->parse".ucfirst(get_class($this))."(\$data, \$cfg);\n";
		
		
		$phpCode .= '?>';
		
		return highlight_string($phpCode, true);
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: parseConfig
	 * @param: (array) $cfg
	 * @return: (array) $cfg
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Parses config properties
	*/	
	protected function parseConfig($cfg) {
		//Var
		$properties = $this->getConfigProperties();
		
		//Test
		if(!is_array($cfg)) {
			$cfg = array();
		}
		
		//Loop properties
		foreach($properties as $propertyData) {
		
			//Test for key
			if(!array_key_exists($propertyData['name'], $cfg)) {
				$cfg[$propertyData['name']] = $propertyData['default'];
			}
			
			//Test casing
			if(array_key_exists('casing', $propertyData)) {
				switch(strtolower($propertyData['casing'])) {
					case 'lower':
					case 'lowercase':
						$cfg[$propertyData['name']] = 
							strtolower($cfg[$propertyData['name']]);
						break;
					case 'upper':
					case 'uppercase':
						$cfg[$propertyData['name']] = 
							strtoupper($cfg[$propertyData['name']]);
						break;
					default:
						break;
				}
			}
			
			//Test type	
			switch($propertyData['type']) {
				case 'boolean':
					if($cfg[$propertyData['name']] === true || 
					   $cfg[$propertyData['name']] === '1' || 
					   $cfg[$propertyData['name']] === 1
					 ) {
						$cfg[$propertyData['name']] = true;
					}
					else {
						$cfg[$propertyData['name']] = false;
					}
					break;
				case 'color':
					$cfg[$propertyData['name']] = 
						$this->hex2Rgb($cfg[$propertyData['name']]);
					break;
				case 'numeric':
					if(!is_numeric($cfg[$propertyData['name']])) {
						$cfg[$propertyData['name']] = 
							$propertyData['default'];
					}
					break;
				case 'file':
					if(!empty($cfg[$propertyData['name']])) {
						if(!file_exists($cfg[$propertyData['name']])) {
							throw new Exception(sprintf(
								'Could not find file "%s" for use as background.',
								$cfg[$propertyData['name']]));
						}
					}
					break;
				case 'arrayValue':
					if(!in_array($cfg[$propertyData['name']], 
						$propertyData['values'])) {
						$cfg[$propertyData['name']] = 
							$propertyData['default'];
					}
					break;
				default:
					break;
			}
		}
		
		//Map
		$this->cfg = $cfg;
		
		//Result
		return $this->cfg;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: createImage
	 * @param: (int) $width, (int) $height, [(bool) $transparent=false]
	 * @return: (resource) $ip
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Creates an image
	*/	
	protected function createImage($width, $height, $transparent=false) {
		//Var
		$ip = imagecreatetruecolor($width, $height);
		
		//Set transparent
		if($transparent) {
			imagesavealpha($ip, true);
			$trans_colour = imagecolorallocatealpha($ip, 0, 0, 0, 127);
			imagefill($ip, 0, 0, $trans_colour);	
		}
		
		//Result
		return $ip;
	}
	
//-------------------------------------------------------------------------+

	/**
	 * @name: parseImage
	 * @param: none
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Parses the image
	*/	
	protected function parseImage() {
		//Test IP
		if(!$this->ip) {
			$this->parseError('$this->ip is not a valid Image resource.');
		}
		if(!empty($this->cfg['file-name'])) {
			
			switch(strtolower($this->cfg['type'])) {
				case 'jpg':
					imagejpeg($this->ip, $this->cfg['file-name'], $this->cfg['jpg-quality']);
				break;
				case 'gif':
					imagegif($this->ip, $this->cfg['file-name']);
				break;
				default:
					imagepng($this->ip, $this->cfg['file-name']);
				break;
			}
			
		}
		else {
			
			
			switch(strtolower($this->cfg['type'])) {
				case 'jpg':
					imagejpeg($this->ip, NULL, $this->cfg['jpg-quality']);
				break;
				case 'gif':
					imagegif($this->ip);
				break;
				default:
					imagepng($this->ip);
				break;
			}			
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: hex2Rgb
	 * @param: (string) $hexColor
	 * @return: (array) $rgb
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Converts HEX color to RGB
	*/	
	protected function hex2Rgb($hexColor) {
		//Test empty
		if(empty($hexColor)) {
			return NULL;
		}
		//Var
		$rgb = array();
		
		//Strip #
		$hexColor = str_replace("#", '', $hexColor);
		
		//Convert to r g b
		$rgb['r'] = hexdec(substr($hexColor, 0, 2));
        $rgb['g'] = hexdec(substr($hexColor, 2, 2));
        $rgb['b'] = hexdec(substr($hexColor, 4, 2));		
		
		//Return 
		return $rgb;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: parseDataStructure
	 * @param: (array) $data
	 * @return: (array) $structure
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Parses a data array in to graph structure (Rounding calculations by Jack Finch)
	*/	
	protected function parseDataStructure($data) {
		//Test
		if(!is_array($data)) {
			throw new Exception('Provided data is not an array.');
		}
		
		//Var
		$structure = array(
			'cols' => 0,
			'max' => 0,
			'min' => 0,
			'avg' => 0,
			'fakeMax' => 0,
			'fakeMin' => 0,
			'difference' => 0,
			'plusDifference' => 0,
			'minDifference' => 0,
			'plusQuadrantPercentage' => 0,
			'minQuadrantPercentage' => 0,
			'maxKeyLength' => 0,
			'maxValueLength' => 0,
			'data' => $data
		);
			
		//Loop
		foreach($data as $key => $value) {
			//Test
			if(!is_numeric($value)) {
				throw new Exception(
					'The value of the key "%s" is not numeric.');
			}
			
			//Test max key length
			$keyLength = strlen($key);
			if($structure['maxKeyLength'] < $keyLength) {
				$structure['maxKeyLength'] = $keyLength;
			}
			
			//Test max value length
			$valueLength = strlen($value);
			//if($structure['maxValueLength'] < $valueLength) {
			//	$structure['maxValueLength'] = $valueLength;
			//}
		}

		//Set
		$structure['max'] = max($data);
		$structure['min'] = min($data);
		$structure['fakeMax'] = $structure['max'] + (($structure['max'] / 100) * 10); //Add 2 percent onto max for margin
		$structure['fakeMin'] = ($structure['min'] > 0) ? 0 : ($structure['min'] + (($structure['min'] / 100) * 10));
		$structure['cols'] = count($data);
		$structure['avg'] = array_sum($data) / $structure['cols'];
		$structure['difference'] = $structure['fakeMax'] - $structure['fakeMin'];
		$structure['plusDifference'] = $structure['fakeMax'];
		$structure['minDifference'] = 0 - $structure['fakeMin'];
		
		//Round values by Jack Finch
		if( $this->cfg['round-value-range'] ){
			$structure['fakeMax'] = round($structure['fakeMax']);
			$structure['fakeMin'] = round($structure['fakeMin']);
		}
		
		//Test lengths
		if($structure['maxValueLength'] < strlen($structure['fakeMax'])) {
			$structure['maxValueLength'] = strlen($structure['fakeMax']);
		}
		if($structure['maxValueLength'] < strlen($structure['fakeMin'])) {
			$structure['maxValueLength'] = strlen($structure['fakeMin']);
		}
		if($structure['maxValueLength'] < strlen(ceil($structure['avg']))) {
			$structure['maxValueLength'] = strlen(ceil($structure['avg']));
		}
		
		//Calc quadrant percentages
		if($structure['fakeMin'] < 0) {
			$structure['plusQuadrantPercentage'] = ($structure['plusDifference'] / ($structure['difference'] / 100));
			$structure['minQuadrantPercentage'] = ($structure['minDifference'] / ($structure['difference'] / 100));
		}
		else {
			$structure['plusQuadrantPercentage'] = 100;
			$structure['minQuadrantPercentage'] = 0;
		}
		
		//MAP TO SECTIONS
		$structure['positiveSectionPercentage'] = $structure['plusQuadrantPercentage'];
		$structure['negativeSectionPercentage'] = $structure['minQuadrantPercentage'];

		//echo "<pre>";
		//print_r($structure);
		//exit;
		
		//Result
		return $structure;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: isDataStructure
	 * @param: (array) $structure
	 * @return: (bool)
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Test if provided struc is datastructure
	*/	
	protected static function isDataStructure($structure) {
		//Var
		$values = array('cols','max','min','avg','maxKeyLength', 'maxValueLength');
		//Test
		if(!is_array($structure)) {
			return false;
		}
		//Validate
		foreach($values as $key) {
			if(!array_key_exists($key, $structure)) {
				return false;
			}
		}
		return true;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: compareDataStructures
	 * @param: (multiple (array) $dataStructure)
	 * @return: (array) $compareStructure
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Compare datastructures
	*/	
	protected function compareDataStructures() {
		//Test arg
		if(func_num_args() < 2) {
			throw new Exception('compareDataStructures needs atleast 2 data structures');
		}
		//Var
		$argumentList = func_get_args();
		$structure = array(
			'cols' => NULL,
			'max' => 0,
			'min' => 0,
			'avg' => NULL,
			'fakeMax' => 0,
			'fakeMin' => 0,
			'difference' => 0,
			'plusDifference' => 0,
			'minDifference' => 0,
			'plusQuadrantPercentage' => 0,
			'minQuadrantPercentage' => 0,			
			'maxKeyLength' => 0,
			'maxValueLength' => 0,
			'structures' => array()
		);

		
		$cols = NULL;
		$avgArray = array();
		
		//Loop
		foreach($argumentList as $idx => $dataStructure) {
		
			//Test cols
			if(is_null($structure['cols'])) {
				//Set the first as control
				$structure['cols'] = $dataStructure['cols'];
			}
			else {
				//Test against control
				if($structure['cols'] != $dataStructure['cols']) {
					throw new Exception(sprintf('Not all datastructures have the same ammount of columns.', $idx));
				}
			}
		
			//Test for valid structure
			if(!$this->isDataStructure($dataStructure)) {
				throw new Exception(sprintf('Structure "%s" is not a valid datastructure', $idx));
			}
			
			//Compare max
			if($structure['max'] < $dataStructure['max']) {
				$structure['max'] = $dataStructure['max'];
			}

			//Compare min
			if($dataStructure['min'] < $structure['min']) {
				$structure['min'] = $dataStructure['min'];
			}

			//Compare key length
			if($structure['maxKeyLength'] < $dataStructure['maxKeyLength']) {
				$structure['maxKeyLength'] = $dataStructure['maxKeyLength'];
			}
			
			//Compare value length
			if($structure['maxValueLength'] < $dataStructure['maxValueLength']) {
				$structure['maxValueLength'] = $dataStructure['maxValueLength'];
			}			
			
			//Get avg
			$avgArray[] = $dataStructure['avg'];
			
		}
		
		//Set
		$structure['fakeMax'] = $structure['max'] + (($structure['max'] / 100) * 10); //Add 2 percent onto max for margin
		$structure['fakeMin'] = ($structure['min'] > 0) ? 0 : ($structure['min'] + (($structure['min'] / 100) * 10));
		$structure['avg'] = array_sum($avgArray) / count($avgArray);
		$structure['difference'] = $structure['fakeMax'] - $structure['fakeMin'];
		$structure['plusDifference'] = $structure['fakeMax'];
		$structure['minDifference'] = 0 - $structure['fakeMin'];

		//Calc quadrant percentages
		if($structure['fakeMin'] < 0) {
			$structure['plusQuadrantPercentage'] = ($structure['plusDifference'] / ($structure['difference'] / 100));
			$structure['minQuadrantPercentage'] = ($structure['minDifference'] / ($structure['difference'] / 100));
		}
		else {
			$structure['plusQuadrantPercentage'] = 100;
			$structure['minQuadrantPercentage'] = 0;
		}
		
		//MAP TO SECTIONS
		$structure['positiveSectionPercentage'] = $structure['plusQuadrantPercentage'];
		$structure['negativeSectionPercentage'] = $structure['minQuadrantPercentage'];
		
		//Override datastructures
		foreach($argumentList as $idx => $dataStructure) {
			
			//Override
			$dataStructure['fakeMax'] = $structure['fakeMax'];
			$dataStructure['fakeMin'] = $structure['fakeMin'];
			$dataStructure['avg'] = $structure['avg'];
			$dataStructure['difference'] = $structure['difference'];
			$dataStructure['plusDifference'] = $structure['plusDifference'];
			$dataStructure['minDifference'] = $structure['minDifference'];
			$dataStructure['plusQuadrantPercentage'] = $structure['plusQuadrantPercentage'];
			$dataStructure['minQuadrantPercentage'] = $structure['minQuadrantPercentage'];
			$dataStructure['positiveSectionPercentage'] = $structure['positiveSectionPercentage'];
			$dataStructure['negativeSectionPercentage'] = $structure['negativeSectionPercentage'];
			
			$dataStructure['min'] = $structure['min'];
			$dataStructure['max'] = $structure['max'];
			$dataStructure['maxValueLength'] = $structure['maxValueLength'];
			$dataStructure['maxKeyLength'] = $structure['maxKeyLength'];
			
			

			//Add datastructure
			$structure['structures'][] = $dataStructure;
			
		}
		
		//Result
		return $structure;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: parseError
	 * @param: (string) $message
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Parses error image
	*/	
	protected function parseError($message) {
		//Var
		$lines = explode("\n", $message);
		$lineCount = count($lines);
		$fontWidth = imagefontwidth(2);
		$fontHeight = imagefontheight(2);
		$imageWidth = 0;
		$imageHeight = ceil($fontHeight * $lineCount);
		$x = 0;
		$y = 0;
		$messageWidth = 0;
		
		//Loop lines to get max width
		foreach($lines as $lineMessage) {
			$messageWidth = ceil(strlen($lineMessage) * $fontWidth);
			if($imageWidth < $messageWidth) {
				$imageWidth = $messageWidth;
			}
		}
		
		//Create image
		$this->ip = $this->createImage($imageWidth, $imageHeight);
		
		//Set colors
		$textColor = imagecolorallocate($this->ip, 255, 0, 0);
		$backgroundColor = imagecolorallocate($this->ip, 255, 255, 255);
		
		//Create rectangle
		imagefilledrectangle($this->ip, 0, 0, $imageWidth, $imageHeight, $backgroundColor);
		
		//Parse lines
		foreach($lines as $lineMessage) {
			imagestring($this->ip, 2, $x, $y, $lineMessage, $textColor);
			$y = $y + $fontHeight;
		}
		
	}
	
//-------------------------------------------------------------------------+

	/**
	 * @name: allocateColor
	 * @param: (array) $rgb, [(bool)alpha=false]
	 * @return: (resource) color
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: allocates color on global IP
	*/	
	protected function allocateColor($rgb, $alpha=false) {
		if(!$this->ip) {
			return false;
		}
		if(is_numeric($alpha) && $alpha > 0) {
			return imagecolorallocatealpha($this->ip, $rgb['r'], $rgb['g'], $rgb['b'], $alpha);
		}
		else {
			return imagecolorallocate($this->ip, $rgb['r'], $rgb['g'], $rgb['b']);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: generateRandomColor
	 * @param: none
	 * @return: (array) forecolor(rgb), backcolor(rgb)
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Generates random color between 70,200
	*/	
	protected function generateRandomColor() {
			//Generate
			$r = rand(70,200);
			$g = rand(70,200);
			$b = rand(70,200);
	
			//Var
			$colors = array(
				'forecolor' => array('r'=>$r, 'g' => $g, 'b' => $b),
				'backcolor' => array('r'=>$r-20, 'g' => $g-20, 'b' => $b-20)
			);
			return $colors;	
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: createImageFromFile
	 * @param: (string) file
	 * @return: (resource) ip
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Creates IP from file
	*/	
	protected function createImageFromFile($file) {
		$ip = @imagecreatefromjpeg($file);
		if(!$ip) {
			$ip = @imagecreatefromgif($file);
		}
		if(!$ip) {
			$ip = @imagecreatefrompng($file);
		}
		return $ip;
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# verticalGraphBase
//##########################################################################

/**
* @name			verticalGraphBase
* @type			class (abstract)
* @extends		graph
* @package      graph
* @version      5.0.1
* @comment:		Vertical graph super class
*/
abstract class verticalGraphBase extends graphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateGraph
	 * @param: (array) dataStructure
	 * @return: (array) graphPoints
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates graph point structure
	*/	
	protected function calculateGraph($dataStructure) {
		//Var	
		$points = array();
		
		//Build structure (Don't change order)
		$points['offset'] = $this->calculateOffset($dataStructure);
		$points['box'] = $this->calculateGraphBox($dataStructure, $points);
		$points['col'] = $this->calculateColumnWidth($dataStructure, $points);
		$points['section']['positive'] = $this->calculatePositiveSection($dataStructure, $points);
		$points['section']['negative'] = $this->calculateNegativeSection($dataStructure, $points);
		$points['zero'] = $this->calculateZero($dataStructure, $points);
		$points['average'] = $this->calculateAverage($dataStructure, $points);
		$points['title'] = $this->calculateTitle($dataStructure, $points);		
		$points['label'] = $this->calculateLabel($dataStructure, $points);
		$points['value'] = $this->calculateValueLabels($dataStructure, $points);
		$points['columns'] = $this->calculateColumns($dataStructure, $points);
		
		//Result
		return $points;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateOffset
	 * @param: (array) dataStructure
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates graph box offset
	*/	
	protected function calculateOffset($dataStructure) {
		//Var
		$result = array(
			'top' => 20,
			'left' => 20,
			'right' => 20,
			'bottom' => 20,
		);
		
		//Test value
		if($this->cfg['value-label-visible']) {
			$result['left'] = 
				($dataStructure['maxValueLength'] * 
				imagefontwidth($this->cfg['value-label-font-size'])) + 
				20;
		}

		//Test label
		if($this->cfg['title-visible']) {
			$result['top'] = 
				($this->cfg['title'] !== '') ? 
				imagefontheight($this->cfg['title-font-size']) + 
				20 : 
				$result['top'];
		}
		
		//Test label
		if($this->cfg['label-visible']) {
			$result['right'] = 
				($this->cfg['label'] !== '') ? 
				(strlen($this->cfg['label']) * 
				imagefontwidth($this->cfg['label-font-size'])) + 
				20 : 
				$result['right'];
		}
		
		//Test key
		if($this->cfg['key-visible']) {
			$result['bottom'] = 
				($dataStructure['maxKeyLength'] * 
				imagefontwidth($this->cfg['key-font-size'])) +
				20;
		}		
		
		//Test minimum
		$result['top'] = ($result['top'] < 20) ? 20 : $result['top'];
		$result['left'] = ($result['left'] < 20) ? 20 : $result['left'];
		$result['right'] = ($result['right'] < 20) ? 20 : $result['right'];
		$result['bottom'] = ($result['bottom'] < 20) ? 20 : $result['bottom'];
		
		
		$result['right'] = ($result['right'] < $result['left']) ? $result['left'] : $result['right'];
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateGraphBox
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates graph box
	*/	
	protected function calculateGraphBox($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0,
			'x2' => 0,
			'y2' => 0,
			'width' => 0,
			'height' => 0
		);

		//Calculate box
		$result['x1'] = $points['offset']['left'];
		$result['y1'] = $points['offset']['top'];
		$result['x2'] = 
			$this->cfg['width'] - 
			$points['offset']['right'];
		$result['y2'] = 
			$this->cfg['height'] - 
			$points['offset']['bottom'];
		
		//Width
		$result['width'] = 
			(($this->cfg['width'] - 
			$points['offset']['left']) - 
			$points['offset']['right']);
			
		//Height
		$result['height'] = 
			(($this->cfg['height'] - 
			$points['offset']['top']) - 
			$points['offset']['bottom']);
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateColumnWidth
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates column width
	*/	
	protected function calculateColumnWidth($datastructure, $points) {
		//Var
		$result = array(
			'width' => 0,
			'show' => true
		);

		//Calculate col width
		$result['width'] = 
			$points['box']['width'] / 
			$datastructure['cols'];
		
		//Test col text visible
		if($result['width'] < 
			imagefontheight($this->cfg['key-font-size'])) {
			$result['show'] = false;
		}
		
		//Result
		return $result;
	
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculatePositiveSection
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates positive section box
	*/	
	protected function calculatePositiveSection($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0,
			'x2' => 0,
			'y2' => 0,
			'height' => 0
		);

		if(($datastructure['fakeMin'] < 0) && 
			($datastructure['fakeMax'] > 0)) {
			//Map box
			$result['x1'] = $points['box']['x1'];
			$result['y1'] = $points['box']['y1'];
			$result['x2'] = $points['box']['x2'];
			$result['y2'] = 
				(($points['box']['height'] / 100) * 
				$datastructure['positiveSectionPercentage']) + 
				$points['offset']['top'];
			
			//Quadrant height
			$result['height'] = $result['y2'] - $result['y1'];
			
		}
		elseif(($datastructure['fakeMin'] < 0) && 
				($datastructure['fakeMax'] <= 0)) {
			//Quadrant heights
			$result['height'] = 0;
		}
		else {
			//Map box to quadrant
			$result['x1'] = $points['box']['x1'];
			$result['y1'] = $points['box']['y1'];
			$result['x2'] = $points['box']['x2'];
			$result['y2'] = $points['box']['y2'];

			//Quadrant heights
			$result['height'] = $points['box']['height'];
		}
		
		//Result
		return $result;
	
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateNegativeSection
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates negative section box
	*/	
	protected function calculateNegativeSection($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0,
			'x2' => 0,
			'y2' => 0,
			'height' => 0
		);

		if(($datastructure['fakeMin'] < 0) && 
			($datastructure['fakeMax'] > 0)) {
			//Map box
			$result['x1'] = $points['box']['x1'];
			$result['y1'] = $points['section']['positive']['y2'];
			$result['x2'] = $points['box']['x2'];
			$result['y2'] = $points['box']['y2'];
			
			//Height
			$result['height'] = $result['y2'] - $result['y1'];
		}
		elseif(($datastructure['fakeMin'] < 0) && 
				($datastructure['fakeMax'] <= 0)) {
			//Map box
			$result['x1'] = $points['box']['x1'];
			$result['y1'] = $points['box']['y1'];
			$result['x2'] = $points['box']['x2'];
			$result['y2'] = $points['box']['y2'];

			//Height
			$result['height'] = $points['box']['height'];
		}
		else {
			//Height
			$result['height'] = 0;
		}

		
		//Result
		return $result;
	
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateZero
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates zero line & text position
	*/	
	protected function calculateZero($datastructure, $points) {
		//Var
		$result = array(
			'line' => array(
				'x1' => 0,
				'y1' => 0,
				'x2' => 0,
				'y2' => 0
			),
			'text' => array(
				'x1' => 0,
				'y1' => 0
			)
		);
		
		//Test
		if(($datastructure['fakeMin'] < 0) && 
			($datastructure['fakeMax'] > 0)) {
			//Zero line
			$result['line']['x1'] = $points['box']['x1'];
			$result['line']['y1'] = $points['section']['negative']['y1'];
			$result['line']['x2'] = $points['box']['x2'];
			$result['line']['y2'] = $points['section']['negative']['y1'];
			
		}
		elseif(($datastructure['fakeMin'] < 0) && 
				($datastructure['fakeMax'] <= 0)) {
			//Zero line
			$result['line']['x1'] = $points['box']['x1'];
			$result['line']['y1'] = $points['box']['y1'];
			$result['line']['x2'] = $points['box']['x2'];
			$result['line']['y2'] = $points['box']['y1'];			
		}
		else {
			//Zero line
			$result['line']['x1'] = $points['box']['x1'];
			$result['line']['y1'] = $points['box']['y2'];
			$result['line']['x2'] = $points['box']['x2'];
			$result['line']['y2'] = $points['box']['y2'];
		}

		$result['text']['x1'] = 
			$points['box']['x2'] + 
			imagefontwidth($this->cfg['value-label-font-size']);
		$result['text']['y1'] = 
			$result['line']['y2'] - 
			(imagefontheight($this->cfg['value-label-font-size']) / 2);
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateAverage
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates average line & text position
	*/	
	protected function calculateAverage($datastructure, $points) {
		//Var
		$result = array(
			'line' => array(
				'x1' => 0,
				'y1' => 0,
				'x2' => 0,
				'y2' => 0
			),
			'text' => array(
				'x1' => 0,
				'y1' => 0
			)
		);

		//Test < 0
		if($datastructure['avg'] < 0) {
			//Calc
			$avgHeightPerc = 
				($points['section']['negative']['height'] / 100);
			$avgValPerc =  
				(-$datastructure['avg'] / 
				$datastructure['minDifference']) * 
				100;
			$avgValueHeight =  $avgHeightPerc * $avgValPerc;			
			
			//Set
			$result['line']['x1'] = $points['box']['x1'];
			$result['line']['y1'] = $points['zero']['line']['y1'] + $avgValueHeight;
			$result['line']['x2'] = $points['box']['x2'];
			$result['line']['y2'] = $result['line']['y1'];
			
			//Text
			$result['text']['x1'] = 
				($points['box']['x1'] - 
				(strlen(round($datastructure['avg'], 2)) * 
				imagefontwidth($this->cfg['value-label-font-size']))) - 
				2;
			$result['text']['y1'] = 
				$result['line']['y1'] - 
				(imagefontheight($this->cfg['value-label-font-size']) / 
				2);
		}
		else {
			//Calc
			$avgHeightPerc = 
				($points['section']['positive']['height'] / 100);
			$avgValPerc = ($datastructure['plusDifference'] > 0) ?
				($datastructure['avg'] / 
				$datastructure['plusDifference']) * 
				100 :
				0;
				
			$avgValueHeight = $avgHeightPerc * $avgValPerc;			
			
			//Set
			$result['line']['x1'] = $points['box']['x1'];
			$result['line']['y1'] = $points['zero']['line']['y1'] - $avgValueHeight;
			$result['line']['x2'] = $points['box']['x2'];
			$result['line']['y2'] = $result['line']['y1'];	
			
			//Text
			$result['text']['x1'] = 
				($points['box']['x1'] - 
				(strlen(round($datastructure['avg'],2)) * 
				imagefontwidth($this->cfg['value-label-font-size']))) - 
				2;
			$result['text']['y1'] = 
				$result['line']['y1'] - 
				(imagefontheight($this->cfg['value-label-font-size']) / 
				2);
					
		}
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateLabel
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates label text position
	*/	
	protected function calculateLabel($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0
		);

		$result['x1'] = $points['box']['x2'] + 4;
		$result['y1'] = 
			($points['box']['y1'] - 
			imagefontheight($this->cfg['label-font-size'])) - 
			2;
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateTitle
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates title text position
	*/	
	protected function calculateTitle($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0
		);

		$result['x1'] = $points['box']['x1'];
		$result['y1'] = 
			($points['offset']['top']/2) - 
			(imagefontheight($this->cfg['title-font-size']) / 
			2);
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateValueLabels
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates value labesl text position (min, max)
	*/	
	protected function calculateValueLabels($datastructure, $points) {
		//Var
		$result = array(
			'min' => array (
				'x1' => 0,
				'y1' => 0
			),
			'max' => array (
				'x1' => 0,
				'y1' => 0
			)
		);

		//Max
		$result['max']['x1'] = 8;
			/*
			$points['box']['x1'] - 
			((strlen($datastructure['fakeMax']) * 
			imagefontwidth($this->cfg['value-label-font-size'])) + 
			2);
			*/
		$result['max']['y1'] = 
			$points['box']['y1'] - 
			(imagefontheight($this->cfg['value-label-font-size']) / 
			2);

		//Min
		$result['min']['x1'] = 8;
		/*
			$points['box']['x1'] - 
			((strlen($datastructure['fakeMin']) * 
			imagefontwidth($this->cfg['value-label-font-size'])) + 
			2);
		*/
		$result['min']['y1'] = 
			$points['box']['y2'] - 
			(imagefontheight($this->cfg['value-label-font-size']) / 
			2);


		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateColumns
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates column positions
	*/	
	protected function calculateColumns($datastructure, $points) {
		//Var
		$idx = 0;
		$colX = $points['box']['x1'];
		$colY = $points['box']['y1'];
		$previousPoints = NULL;
		$columns = array();
		
		//Loop data items
		foreach($datastructure['data'] as $key => $value) {
			
			//Calc
			$colWidthOffset = ($points['col']['width'] > 8) ? 3 : 0;
			$colHeightOffset = ($points['col']['width'] > 8) ? 3 : 0;
			
			//Var
			$result = array(
				'value' => array(
						'text' => $value,
						'x1' => 0,
						'y1' => 0,
					),
				'key' => array(
						'text' => $key,
						'x1' => 0,
						'y1' => 0,
					),
				'fill' => 0,
				'column' => array(
					'x1' => $colX,
					'y1' => $points['box']['y1'],
					'x2' => $colX + $points['col']['width'],
					'y2' => $points['box']['y1'],
					'x3' => $colX + $points['col']['width'],
					'y3' => $points['box']['y2'],
					'x4' => $colX,
					'y4' => $points['box']['y2'],
				),
				'bar' => array(
					'x1' => $colX,
					'y1' => 0, //NEEDS CALC
					'x2' => $colX + $points['col']['width'],
					'y2' => 0, //NEEDS CALC
					'x3' => $colX + $points['col']['width'],
					'y3' => 0, //NEEDS CALC
					'x4' => $colX,
					'y4' => 0, //NEEDS CALC
				),
				'foregroundbar' => array(
					'x1' => $colX + $colWidthOffset,
					'y1' => 0, //NEEDS CALC
					'x2' => $colX + $colWidthOffset,
					'y2' => 0, //NEEDS CALC
					'x3' => ($colX + $points['col']['width']) - ($colWidthOffset * 2),
					'y3' => 0, //NEEDS CALC
					'x4' => ($colX + $points['col']['width']) - ($colWidthOffset * 2),
					'y4' => 0, //NEEDS CALC
				),
				'shadowbar' => array(
					'x1' => $colX + ($colWidthOffset * 2),
					'y1' => 0,
					'x2' => $colX + ($colWidthOffset * 2),
					'y2' => 0,
					'x3' => ($colX + $points['col']['width']) - $colWidthOffset,
					'y3' => 0,
					'x4' => ($colX + $points['col']['width']) - $colWidthOffset,
					'y4' => 0,
				),
				'previous' => array(
					'value' => 0,
					'x1' => 0,
					'y1' => 0,
					'x2' => 0,
					'y2' => 0,
					'x3' => 0,
					'y3' => 0,
					'x4' => 0,
					'y4' => 0,
				),
				'line' => array(
					'x1' => 0,
					'y1' => 0,
					'x2' => 0,
					'y2' => 0,
				),
				'poly' => array(
				),
			);
			
			
			//$previousPoints
			if(is_null($previousPoints)) {
				$result['previous']['value'] = 0;
				$result['previous']['x1'] = $points['box']['x1'];
				$result['previous']['y1'] = $points['zero']['line']['y1'];
				$result['previous']['x2'] = $points['box']['x1'];
				$result['previous']['y2'] = $points['zero']['line']['y1'];
				$result['previous']['x3'] = $points['box']['x1'];
				$result['previous']['y3'] = $points['zero']['line']['y1'];
				$result['previous']['x4'] = $points['box']['x1'];
				$result['previous']['y4'] = $points['zero']['line']['y1'];
			}
			else {
				//Map
				$result['previous']['value'] = $previousPoints['value']['text'];
				$result['previous']['x1'] = $previousPoints['bar']['x1'];
				$result['previous']['y1'] = $previousPoints['bar']['y1'];
				$result['previous']['x2'] = $previousPoints['bar']['x2'];
				$result['previous']['y2'] = $previousPoints['bar']['y2'];
				$result['previous']['x3'] = $previousPoints['bar']['x3'];
				$result['previous']['y3'] = $previousPoints['bar']['y3'];
				$result['previous']['x4'] = $previousPoints['bar']['x4'];
				$result['previous']['y4'] = $previousPoints['bar']['y4'];
			}
			
			
			
			//Key placement
			$keyX = ($points['col']['width'] / 2) - (imagefontheight($this->cfg['key-font-size']) / 2);
			$result['key']['x1'] = $colX + $keyX;
			$result['key']['y1'] = ($points['box']['y2'] + (strlen($key) * imagefontwidth($this->cfg['key-font-size']))) + 4;
			
			//Test plus or min quadrant
			if($value < 0) {
			
				//Calc value height
				$hgtPerc = ($points['section']['negative']['height'] / 100);
				$valPerc =  ($value / $datastructure['minDifference'])  * 100;
				$valueHeight =  $hgtPerc * $valPerc;
				
				//Fill percentage
				$result['fill'] = floor(-$valPerc);
				
				//Min
				$result['bar']['y1'] =  $points['zero']['line']['y1'];
				$result['bar']['y2'] = $result['bar']['y1'];
				$result['bar']['y3'] = $points['zero']['line']['y1'] - $valueHeight;
				$result['bar']['y4'] = $result['bar']['y3'];
				
				//Main		
				$result['foregroundbar']['y1'] =  $points['zero']['line']['y1'];
				$result['foregroundbar']['y2'] = $result['foregroundbar']['y1'];
				$result['foregroundbar']['y3'] = $points['zero']['line']['y1'] - $valueHeight;
				$result['foregroundbar']['y4'] = $result['foregroundbar']['y3'];

				//Shadow		
				$result['shadowbar']['y1'] =  $points['zero']['line']['y1'];
				$result['shadowbar']['y2'] = $result['shadowbar']['y1'];
				$result['shadowbar']['y3'] = ($points['zero']['line']['y1'] - $valueHeight) - $colHeightOffset;
				$result['shadowbar']['y4'] = $result['shadowbar']['y3'];
		
		
			
				//Value text
				$valueLength = strlen($value) * imagefontwidth($this->cfg['value-font-size']);
				$valueHeight = imagefontheight($this->cfg['value-font-size']);
				//$result['value']['x1'] = ( $colX + (($points['col']['width'] / 2) - ($valueHeight / 2))) - ($colWidthOffset / 2);
				$result['value']['x1'] = $colX + (($points['col']['width'] / 2) - ($valueHeight / 2));
				
				//Test sector
				if($valueLength > $points['section']['negative']['height']) {
					$result['value']['y1'] = ($points['zero']['line']['y1']) - 4;
				}
				else {
					//Test where value text is gonna be
					if($result['fill'] < 50) {
						$result['value']['y1'] = ($result['bar']['y3'] + $valueLength) + 2;
					}
					else {
						$result['value']['y1'] = ($points['zero']['line']['y1'] + $valueLength) + 4;
					}
				}
				
				//Polygon
				if($result['previous']['value'] < 0) {
					//Poly
					$result['poly'][0] = $result['bar']['x1'];
					$result['poly'][1] = $result['bar']['y1'];
					$result['poly'][2] = $result['bar']['x2'];
					$result['poly'][3] = $result['bar']['y2'];
					$result['poly'][4] = $result['bar']['x3'];
					$result['poly'][5] = $result['bar']['y3'];
					$result['poly'][6] = $result['bar']['x4'];
					$result['poly'][7] = $result['previous']['y3'];
					$result['poly'][8] = $result['bar']['x1'];
					$result['poly'][9] = $result['previous']['y1'];

					//Line
					$result['line']['x1'] = $result['previous']['x3'];
					$result['line']['y1'] = $result['previous']['y3'];
					$result['line']['x2'] = $result['bar']['x3'];
					$result['line']['y2'] = $result['bar']['y3'];
					
					
				}
				else {
					//Poly
					$result['poly'][0] = $result['bar']['x1'];
					$result['poly'][1] = $result['previous']['y2'];
					$result['poly'][2] = $result['bar']['x3'];
					$result['poly'][3] = $result['bar']['y3'];
					$result['poly'][4] = $result['bar']['x2'];
					$result['poly'][5] = $result['bar']['y2'];
					$result['poly'][6] = $result['bar']['x1'];
					$result['poly'][7] = $result['bar']['y2'];
					$result['poly'][8] = $result['bar']['x1'];
					$result['poly'][9] = $result['previous']['y2'];	
					
					//Line
					$result['line']['x1'] = $result['previous']['x2'];
					$result['line']['y1'] = $result['previous']['y2'];
					$result['line']['x2'] = $result['bar']['x3'];
					$result['line']['y2'] = $result['bar']['y3'];		

					//Test start index
					if ($idx == 0) {
						$result['line']['y1'] = $result['line']['y2'];
						$result['poly'][1] = $result['poly'][3];
					}								
											
				}
				
			
			}
			else {
				
				//Calc value height
				$hgtPerc = ($points['section']['positive']['height'] / 100);
				$valPerc = 
					($datastructure['plusDifference'] > 0) ?
						($value / $datastructure['plusDifference'])  * 100 :
						0;
				$valueHeight =  $hgtPerc * $valPerc;

				//Fill percentage
				$result['fill'] = $valPerc;

				//plus
				$result['bar']['y1'] =  $points['zero']['line']['y1'] - $valueHeight;
				$result['bar']['y2'] = $result['bar']['y1'];
				$result['bar']['y3'] = $points['zero']['line']['y1'];
				$result['bar']['y4'] = $result['bar']['y3'];
				
				//Main
				$result['foregroundbar']['y1'] =  $points['zero']['line']['y1'] - $valueHeight;
				$result['foregroundbar']['y2'] = $result['foregroundbar']['y1'];
				$result['foregroundbar']['y3'] = $points['zero']['line']['y1'];
				$result['foregroundbar']['y4'] = $result['foregroundbar']['y3'];
				
				//Shadow		
				//$result['shadowbar']['y1'] = ($points['zero']['line']['y1'] - $valueHeight) - $colHeightOffset; //ORG
				$result['shadowbar']['y1'] = ($value === 0) ? $points['zero']['line']['y1'] : ($points['zero']['line']['y1'] - $valueHeight) - $colHeightOffset;

				$result['shadowbar']['y2'] = $result['foregroundbar']['y1'];
				$result['shadowbar']['y3'] = $points['zero']['line']['y1'];
				$result['shadowbar']['y4'] = $result['foregroundbar']['y3'];

				
				//Value text
				$valueLength = strlen($value) * imagefontwidth($this->cfg['value-font-size']);
				$valueFontHeight = imagefontheight($this->cfg['value-font-size']);
				//$result['value']['x1'] = $colX + (($points['col']['width'] / 2) - ($valueFontHeight / 2))- ($colWidthOffset / 2);
				$result['value']['x1'] = $colX + (($points['col']['width'] / 2) - ($valueFontHeight / 2));

				//Test sector
				if( ($valueLength +($colHeightOffset + 6)) > $points['section']['positive']['height']) {
					$result['value']['y1'] = ($points['zero']['line']['y1'] + $valueLength) + 6;
				}
				else {
					if($result['fill'] < 50) {
						$result['value']['y1'] = ($points['zero']['line']['y1'] - $valueHeight) - ($colHeightOffset + 6);
					}
					else {
						$result['value']['y1'] = ($points['zero']['line']['y1']) - 6;
						
					}
				}
								
				//Polygon & line
				if($result['previous']['value'] < 0) {
					//Poly
					$result['poly'][0] = $result['bar']['x1'];
					$result['poly'][1] = $result['previous']['y3'];
					$result['poly'][2] = $result['bar']['x2'];
					$result['poly'][3] = $result['bar']['y2'];
					$result['poly'][4] = $result['bar']['x3'];
					$result['poly'][5] = $result['bar']['y3'];
					$result['poly'][6] = $result['bar']['x4'];
					$result['poly'][7] = $result['bar']['y4'];
					$result['poly'][8] = $result['bar']['x1'];
					$result['poly'][9] = $result['previous']['y2'];

					//Line
					$result['line']['x1'] = $result['previous']['x3'];
					$result['line']['y1'] = $result['previous']['y3'];
					$result['line']['x2'] = $result['bar']['x2'];
					$result['line']['y2'] = $result['bar']['y2'];
				}
				else {
					//Poly
					$result['poly'][0] = $result['bar']['x1'];
					$result['poly'][1] = $result['previous']['y2'];
					$result['poly'][2] = $result['bar']['x2'];
					$result['poly'][3] = $result['bar']['y2'];
					$result['poly'][4] = $result['bar']['x3'];
					$result['poly'][5] = $result['bar']['y3'];
					$result['poly'][6] = $result['bar']['x4'];
					$result['poly'][7] = $result['bar']['y4'];
					$result['poly'][8] = $result['bar']['x1'];
					$result['poly'][9] = $result['previous']['y2'];	

					//Line
					$result['line']['x1'] = $result['previous']['x2'];
					$result['line']['y1'] = $result['previous']['y2'];
					$result['line']['x2'] = $result['bar']['x2'];
					$result['line']['y2'] = $result['bar']['y2'];

					//Test start index
					if ($idx == 0) {
						$result['line']['y1'] = $result['line']['y2'];
						$result['poly'][1] = $result['poly'][3];
					}								
				}
				
			}

			//Append
			$columns[$key] = $result;
			
			//Save previous entry
			$previousPoints = $result;
			
			//Set new X
			$colX = $colX + $points['col']['width'];
			
			//Up index
			$idx++;
		}


		//echo "<pre>"; print_r($columns);

		//Result
		return $columns;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawImage
	 * @param: none
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the main image
	*/	
	protected function drawImage() {
		//Create image
		$this->ip = $this->createImage($this->cfg['width'], $this->cfg['height']);
		
		//allocateColor
		$backgroundColor = $this->allocateColor($this->cfg['background-color']);
		
		//Fill
		imagefill($this->ip, 0, 0, $backgroundColor);
		
		//Test for background image
		if(!empty($this->cfg['background-image'])) {
			//Create background-image
			if($bgkImage = $this->createImageFromFile($this->cfg['background-image'])) {
				imagecopy($this->ip, $bgkImage, 0, 0, 0, 0, $this->cfg['width'], $this->cfg['height']);
			}
		}		
	}
	
//-------------------------------------------------------------------------+

	/**
	 * @name: drawTitle
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the title
	*/	
	protected function drawTitle($graphPoints) {
		//Test for visible
		if($this->cfg['title-visible']) {
			//allocateColor
			$titleColor = $this->allocateColor($this->cfg['title-color']);
			
			//Draw string
			imagestring($this->ip, $this->cfg['title-font-size'], $graphPoints['title']['x1'], $graphPoints['title']['y1'], $this->cfg['title'], $titleColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawValueLabels
	 * @param: (array) $graphPoints, (array) $dataStructure
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the value labels (min, max) (Alignment by Jack Finch)
	*/	
	protected function drawValueLabels($graphPoints, $dataStructure) {
		//Test for visible
		if($this->cfg['value-label-visible']) {
			//allocateColor
			$valueLabelColor = $this->allocateColor($this->cfg['value-label-color']);
			//Draw strings
			if( $this->cfg['label-right-align'] ){
				$lblTxtMin = str_pad( $dataStructure['fakeMin'], $dataStructure['maxValueLength'], " ", STR_PAD_LEFT);
				$lblTxtMax = str_pad( $dataStructure['fakeMax'], $dataStructure['maxValueLength'], " ", STR_PAD_LEFT);
			}else{
				$lblTxtMin = $dataStructure['fakeMin'];
				$lblTxtMax = $dataStructure['fakeMax'];
			}
			
			//Test for when fakemin & max are less or equal to 0
			if($dataStructure['fakeMin'] < 0 && $dataStructure['fakeMax'] <= 0) {
				$lblTxtMax = "0";
			}
			
			
			
			imagestring($this->ip, $this->cfg['value-label-font-size'], $graphPoints['value']['min']['x1'], $graphPoints['value']['min']['y1'], $lblTxtMin, $valueLabelColor);
			imagestring($this->ip, $this->cfg['value-label-font-size'], $graphPoints['value']['max']['x1'], $graphPoints['value']['max']['y1'], $lblTxtMax, $valueLabelColor);
			//ORG imagestring($this->ip, $this->cfg['value-label-font-size'], $graphPoints['value']['min']['x1'], $graphPoints['value']['min']['y1'], $dataStructure['fakeMin'], $valueLabelColor);
			//ORG imagestring($this->ip, $this->cfg['value-label-font-size'], $graphPoints['value']['max']['x1'], $graphPoints['value']['max']['y1'], $dataStructure['fakeMax'], $valueLabelColor);
		}			
	
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawBoxBackground
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the graph box background
	*/	
	protected function drawBoxBackground($graphPoints) {
		//Test for visible
		if($this->cfg['box-background-visible']) {
			//allocateColor
			$boxBackgroundColor = $this->allocateColor($this->cfg['box-background-color'], $this->cfg['box-background-alpha']);
			
			//Draw rectangle
			imagefilledrectangle($this->ip, $graphPoints['box']['x1'], $graphPoints['box']['y1'], $graphPoints['box']['x2'], $graphPoints['box']['y2'], $boxBackgroundColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawHorizontaldividers
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the horizontal dividers
	*/	
	protected function drawHorizontaldividers($graphPoints) {
		//Test for visible
		if($this->cfg['horizontal-divider-visible']) {
			//Set offset
			$offset = ($graphPoints['box']['height'] / 4);
			//Allocate colors
			$color = $this->allocateColor($this->cfg['horizontal-divider-color'], $this->cfg['horizontal-divider-alpha']);
			$alpha = $this->allocateColor($this->hex2Rgb('FFFFFF'), 127);
			//Define line style
			$style = array($color, $color, $alpha, $alpha);
			//Set line style
			imagesetstyle($this->ip, $style);			
			//Loop 3 times
			for($i = 1; $i <= 3; $i++) {
				//Calc y
				$y = $graphPoints['box']['y1'] + ($i * $offset);
				//Draw line
				imageline($this->ip,  $graphPoints['box']['x1'], $y, $graphPoints['box']['x2'], $y, IMG_COLOR_STYLED);
			}
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawLabel
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the label
	*/	
	protected function drawLabel($graphPoints) {
		//Test for visible
		if($this->cfg['label-visible']) {
			//allocateColor
			$labelColor = $this->allocateColor($this->cfg['label-color']);
			
			//Draw string
			imagestring($this->ip, $this->cfg['label-font-size'], $graphPoints['label']['x1'], $graphPoints['label']['y1'], $this->cfg['label'], $labelColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawBoxBorder
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the graph box border
	*/	
	protected function drawBoxBorder($graphPoints) {
		//Test for visible
		if($this->cfg['box-border-visible']) {
			//allocateColor
			$boxBorderColor = $this->allocateColor($this->cfg['box-border-color'], $this->cfg['box-border-alpha']);
			
			//Draw rectangle
			imagerectangle($this->ip, $graphPoints['box']['x1'], $graphPoints['box']['y1'], $graphPoints['box']['x2'], $graphPoints['box']['y2'], $boxBorderColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawAverage
	 * @param: (array) $graphPoints, (array) $dataStructure
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the average line & text
	*/	
	protected function drawAverage($graphPoints, $dataStructure) {
		//Test visible
		if($this->cfg['average-line-visible']) {
			//allocateColor
			$avgLineColor = 
				$this->allocateColor($this->cfg['average-line-color'], 
									 $this->cfg['average-line-alpha']);
			
			//Draw line
			imageline($this->ip, 
				$graphPoints['average']['line']['x1'] - 2, 
				$graphPoints['average']['line']['y1'], 
				$graphPoints['average']['line']['x2'] + 2, 
				$graphPoints['average']['line']['y2'], 
				$avgLineColor);
			
			//Draw string
			imagestring($this->ip, 2, 
				$graphPoints['average']['text']['x1'], 
				$graphPoints['average']['text']['y1'], 
				round($dataStructure['avg'], 2), 
				$avgLineColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawZero
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the zero line & text
	*/	
	protected function drawZero($graphPoints) {
		//Test visible
		if($this->cfg['zero-line-visible']) {
			
			//if($graphPoints['zero']['line']['y1'] != 
			//	$graphPoints['box']['y1']) {
				
				//allocateColor
				$color = 
					$this->allocateColor($this->cfg['zero-line-color'], 
										$this->cfg['zero-line-alpha']);
				
				//Draw line
				imageline($this->ip, 
					$graphPoints['zero']['line']['x1'] - 2, 
					$graphPoints['zero']['line']['y1'], 
					$graphPoints['zero']['line']['x2'] + 2, 
					$graphPoints['zero']['line']['y2'], 
					$color);
				
				//Draw string
				imagestring($this->ip, 
					$this->cfg['value-label-font-size'], 
					$graphPoints['zero']['text']['x1'], 
					$graphPoints['zero']['text']['y1'], 
					0, 
					$color);
			//}
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumns
	 * @param: (array) $graphPoints, (array) $dataStructure, [(bool) $compareStructure = false]
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the columns
	*/	
	public function drawColumns($graphPoints, $dataStructure, $compareStructure = false) {
		//Var
		$colIdx = 1;
		
		//Allocate colors
		$keyColor = $this->allocateColor($this->cfg['key-color']);
		$valueColor = $this->allocateColor($this->cfg['value-color']);
		$columnColor = $this->allocateColor($this->cfg['column-color'], $this->cfg['column-alpha']); 
		$columnShadowColor = $this->allocateColor($this->cfg['column-shadow-color'], $this->cfg['column-shadow-alpha']); 
		
		//Loop
		foreach($graphPoints['columns'] as $colPoint) {

			
			//Test for random colors
			if($this->cfg['column-color-random']) {
				$colors = $this->generateRandomColor();
				$columnColor = $this->allocateColor($colors['forecolor'], $this->cfg['column-alpha']); 
				$columnShadowColor = $this->allocateColor($colors['backcolor'], $this->cfg['column-shadow-alpha']);
			}
			
			if($compareStructure) {
				$columnColor = $this->allocateColor($this->cfg['column-compare-color'], 0); 
				$columnShadowColor = $this->allocateColor($this->cfg['column-compare-shadow-color'], 0); 
			}
			
			//Column
			$this->drawColumn($colPoint, $columnColor, $columnShadowColor);
			
			//Value text
			if($this->cfg['value-visible']) {
				if($graphPoints['col']['show']) {
					if(!$compareStructure) {
						imagestringup($this->ip, $this->cfg['value-font-size'], $colPoint['value']['x1'], $colPoint['value']['y1'], $colPoint['value']['text'], $valueColor);
					}
				}
			}
						
			//Test for visible
			if($this->cfg['column-divider-visible']) {
				//Do not print first col
				if($colIdx!=1) {
					if(!$compareStructure) {
						//Allocate colors
						$columndividerColor = $this->allocateColor($this->cfg['column-divider-color'], $this->cfg['column-divider-alpha']);
						$columndividerAlphaColor = $this->allocateColor($this->hex2Rgb('FFFFFF'), 127);
						//Define style
						$dottedStyle = array($columndividerColor, $columndividerColor, $columndividerAlphaColor, $columndividerAlphaColor);
						//Set style
						imagesetstyle($this->ip, $dottedStyle);
						//Draw line
						imageline($this->ip,  $colPoint['column']['x1'], $colPoint['column']['y1'], $colPoint['column']['x1'], $colPoint['column']['y3'], IMG_COLOR_STYLED);
					}
				}
			}
					
				
			//Determin if only firts and last key are visible
			if($this->cfg['key-visible']) {
				if(!$compareStructure) {
					$printCol = true;
					if(!$graphPoints['col']['show']) {
						$printCol = (($colIdx==1) || ($colIdx==$dataStructure['cols']));
					}
					if($printCol) {
						imagestringup($this->ip, $this->cfg['key-font-size'], $colPoint['key']['x1'], $colPoint['key']['y1'], $colPoint['key']['text'], $keyColor);
					}
				}
			}
											
			//Up
			$colIdx++;			
			
		} //End loop
		
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column (Needs override)
	*/	
	protected function drawColumn($colPoint, 
								  $columnColor, 
								  $columnShadowColor) {
		throw new Exception('drawColumn is not implemented');
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: parse
	 * @param: (array) $data, [(array) $cfg=array()]
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Parses the graph
	*/	
	public function parse($data, $cfg = array()){
		try {
			//Parse config
			$this->parseConfig($cfg);

			//Get data structure
			$dataStructure = $this->parseDataStructure($data);
			
			//Calculate graph structure
			$graphPoints = $this->calculateGraph($dataStructure);

			//echo "<pre>";
			//print_r($graphPoints);
			//echo "</pre>";

			//Draw
			$this->drawImage();
			$this->drawTitle($graphPoints);
			$this->drawValueLabels($graphPoints, $dataStructure);
			$this->drawBoxBackground($graphPoints);
			$this->drawColumns($graphPoints, $dataStructure);
			$this->drawHorizontaldividers($graphPoints);
			$this->drawLabel($graphPoints);
			$this->drawBoxBorder($graphPoints);
			$this->drawAverage($graphPoints, $dataStructure);
			$this->drawZero($graphPoints);
		}
		catch(Exception $ex) {
			//Parse error message overriding original
			$this->parseError($ex);
		}
		
		//Parse image
		$this->parseImage();
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: parseCompare
	 * @param: (array) $data1, (array) $data2, [(array) $cfg=array()]
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Parses a compare graph (max 2 data arrays)
	*/	
	public function parseCompare($data1, $data2, $cfg = array()){
		try {
			//Parse config
			$this->parseConfig($cfg);

			//Get data structure
			$dataStructure1 = $this->parseDataStructure($data1);
			$dataStructure2 = $this->parseDataStructure($data2);
			$compareStructure = $this->compareDataStructures($dataStructure1, $dataStructure2);
			
			
			if(count($compareStructure['structures'])<0) {
				throw new Exception('Not enough datastructures found');
			}
			
			//Calculate graph structure
			$firstStructure = $compareStructure['structures'][0];
			$graphPoints = $this->calculateGraph($firstStructure);
			
			//Unset value-label-text
			$this->cfg['value-visible'] = false;
			$this->cfg['column-alpha'] = 30;
			$this->cfg['column-shadow-alpha'] = 127;
			
			//Draw
			$this->drawImage();
			$this->drawTitle($graphPoints);
			$this->drawValueLabels($graphPoints, $firstStructure);
			$this->drawBoxBackground($graphPoints);
				foreach($compareStructure['structures'] as $idx =>  $dataStructure) {
					$graphPoints2 = $this->calculateGraph($dataStructure);
					$this->drawColumns($graphPoints2, $dataStructure, ($idx!=1));
				}
			$this->drawHorizontaldividers($graphPoints);
			$this->drawLabel($graphPoints);
			$this->drawBoxBorder($graphPoints);
			$this->drawAverage($graphPoints, $firstStructure);
			$this->drawZero($graphPoints);
			
			
			
		}
		catch(Exception $ex) {
			//Parse error message overriding original
			$this->parseError($ex);
		}
		
		//Parse image
		$this->parseImage();
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: parseExample
	 * @param: void
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Parses an example graph
	*/	
	public function parseExample(){
		//Set config
		$cfg['title'] = 'Example graph';
		$cfg['label'] = 'orders';
		
		//Set data
		$data = array(
			'Jan' => 12,
			'Feb' => 25,
			'Mar' => 0,
			'Apr' => -7,
			'May' => 80,
			'Jun' => 67,
			'Jul' => 45,
			'Aug' => 66,
			'Sep' => -23,
			'Oct' => 23,
			'Nov' => 78,
			'Dec' => 23
		);
		
		//Parse
		$this->parse($data, $cfg);

	}

//-------------------------------------------------------------------------+

} //End class


//##########################################################################
//# horizontalGraphBase
//##########################################################################

/**
* @name			horizontalGraphBase
* @type			class (abstract)
* @extends		graph
* @package      graph
* @version      5.0.1
* @comment:		Horizontal graph super class
*/
abstract class horizontalGraphBase extends graphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateGraph
	 * @param: (array) dataStructure
	 * @return: (array) graphPoints
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates graph point structure
	*/	
	protected function calculateGraph($dataStructure) {
		//Var	
		$points = array();
		
		//Build structure (Don't change order)
		$points['offset'] = $this->calculateOffset($dataStructure);
		$points['box'] = $this->calculateGraphBox($dataStructure, $points);
		$points['col'] = $this->calculateColumnHeight($dataStructure, $points);
		$points['section']['positive'] = $this->calculatePositiveSection($dataStructure, $points);
		$points['section']['negative'] = $this->calculateNegativeSection($dataStructure, $points);
		$points['zero'] = $this->calculateZero($dataStructure, $points);
		$points['average'] = $this->calculateAverage($dataStructure, $points);
		$points['title'] = $this->calculateTitle($dataStructure, $points);		
		$points['label'] = $this->calculateLabel($dataStructure, $points);
		$points['value'] = $this->calculateValueLabels($dataStructure, $points);
		$points['columns'] = $this->calculateColumns($dataStructure, $points);
		
		//Result
		return $points;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateOffset
	 * @param: (array) dataStructure
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates graph box offset
	*/	
	protected function calculateOffset($dataStructure) {
		//Var
		$result = array(
			'top' => 20,
			'left' => 20,
			'right' => 20,
			'bottom' => 20,
		);
		
		//Test value
		if($this->cfg['value-label-visible']) {
			$result['bottom'] = 
				($dataStructure['maxValueLength'] * 
				imagefontwidth($this->cfg['value-label-font-size'])) + 
				20;
		}

		//Test label
		if($this->cfg['title-visible']) {
			$result['top'] = 
				($this->cfg['title'] !== '') ? 
				imagefontheight($this->cfg['title-font-size']) + 
				20 : 
				$result['top'];
		}
		
		//Test label
		if($this->cfg['label-visible']) {
			$result['right'] = 
				($this->cfg['label'] !== '') ? 
				(strlen($this->cfg['label']) * 
				imagefontwidth($this->cfg['label-font-size'])) + 
				20 : 
				$result['right'];
		}
		
		//Test key
		if($this->cfg['key-visible']) {
			$result['left'] = 
				($dataStructure['maxKeyLength'] * 
				imagefontwidth($this->cfg['key-font-size'])) +
				20;
		}		
		
		//Test minimum
		$result['top'] = ($result['top'] < 20) ? 20 : $result['top'];
		$result['left'] = ($result['left'] < 20) ? 20 : $result['left'];
		$result['right'] = ($result['right'] < 20) ? 20 : $result['right'];
		$result['bottom'] = ($result['bottom'] < 20) ? 20 : $result['bottom'];
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateGraphBox
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates graph box
	*/	
	protected function calculateGraphBox($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0,
			'x2' => 0,
			'y2' => 0,
			'width' => 0,
			'height' => 0
		);

		//Calculate box
		$result['x1'] = $points['offset']['left'];
		$result['y1'] = $points['offset']['top'];
		
		$result['x2'] = 
			$this->cfg['width'] - 
			$points['offset']['right'];
		$result['y2'] = 
			$this->cfg['height'] - 
			$points['offset']['bottom'];
		
		//Width
		$result['width'] = 
			(($this->cfg['width'] - 
			$points['offset']['left']) - 
			$points['offset']['right']);
			
		//Height
		$result['height'] = 
			(($this->cfg['height'] - 
			$points['offset']['top']) - 
			$points['offset']['bottom']);
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: calculateColumnHeight
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates column height
	*/	
	protected function calculateColumnHeight($datastructure, $points) {
		//Var
		$result = array(
			'height' => 0,
			'show' => true
		);

		//Calculate col width
		$result['height'] = 
			$points['box']['height'] / 
			$datastructure['cols'];
		
		//Test col text visible
		if($result['height'] < 
			imagefontheight($this->cfg['key-font-size'])) {
			$result['show'] = false;
		}
		
		//Result
		return $result;
	
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculatePositiveSection
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates positive section box
	*/	
	protected function calculatePositiveSection($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0,
			'x2' => 0,
			'y2' => 0,
			'width' => 0
		);

		if(($datastructure['fakeMin'] < 0) && 
			($datastructure['fakeMax'] > 0)) {

			//Map box
			$result['x1'] = $points['box']['x2'] - (($points['box']['width'] / 100) * $datastructure['positiveSectionPercentage']);
			$result['y1'] = $points['box']['y1'];
			$result['x2'] = $points['box']['x2'];
			$result['y2'] = $points['box']['y2'];
			
			//Quadrant width
			$result['width'] = $result['x2'] - $result['x1'];
		}
		elseif(($datastructure['fakeMin'] < 0) && 
				($datastructure['fakeMax'] <= 0)) {
			//Quadrant width
			$result['width'] = 0;
		}
		else {
			//Map box to quadrant
			$result['x1'] = $points['box']['x1'];
			$result['y1'] = $points['box']['y1'];
			$result['x2'] = $points['box']['x2'];
			$result['y2'] = $points['box']['y2'];

			//Quadrant width
			$result['width'] = $points['box']['width'];
		}
		
		//Result
		return $result;
	
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateNegativeSection
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates negative section box
	*/	
	protected function calculateNegativeSection($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0,
			'x2' => 0,
			'y2' => 0,
			'width' => 0
		);

		if(($datastructure['fakeMin'] < 0) && 
			($datastructure['fakeMax'] > 0)) {
			//Map box
			$result['x1'] = $points['box']['x1'];
			$result['y1'] = $points['box']['y1'];
			$result['x2'] = $points['section']['positive']['x1'];
			$result['y2'] = $points['box']['y2'];
			
			//Height
			$result['width'] = $result['x2'] - $result['x1'];
		}
		elseif(($datastructure['fakeMin'] < 0) && 
				($datastructure['fakeMax'] <= 0)) {
			//Map box
			$result['x1'] = $points['box']['x1'];
			$result['y1'] = $points['box']['y1'];
			$result['x2'] = $points['box']['x2'];
			$result['y2'] = $points['box']['y2'];

			//Height
			$result['width'] = $points['box']['width'];
		}
		else {
			//Height
			$result['height'] = 0;
		}

		
		//Result
		return $result;
	
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateZero
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates zero line & text position
	*/	
	protected function calculateZero($datastructure, $points) {
		//Var
		$result = array(
			'line' => array(
				'x1' => 0,
				'y1' => 0,
				'x2' => 0,
				'y2' => 0
			),
			'text' => array(
				'x1' => 0,
				'y1' => 0
			)
		);
		
		//Test
		if(($datastructure['fakeMin'] < 0) && 
			($datastructure['fakeMax'] > 0)) {

			//Zero line
			$result['line']['x1'] = $points['section']['positive']['x1'];
			$result['line']['y1'] = $points['box']['y1'];
			$result['line']['x2'] = $points['section']['positive']['x1'];
			$result['line']['y2'] = $points['box']['y2'];
			
		}
		elseif(($datastructure['fakeMin'] < 0) && 
				($datastructure['fakeMax'] <= 0)) {
			//Zero line
			$result['line']['x1'] = $points['box']['x2'];
			$result['line']['y1'] = $points['box']['y1'];
			$result['line']['x2'] = $points['box']['x2'];
			$result['line']['y2'] = $points['box']['y2'];			
		}
		else {
			//Zero line
			$result['line']['x1'] = $points['box']['x1'];
			$result['line']['y1'] = $points['box']['y1'];
			$result['line']['x2'] = $points['box']['x2'];
			$result['line']['y2'] = $points['box']['y2'];
		}

		$fontHeight = imagefontheight($this->cfg['value-label-font-size']);
		$fontWidth = imagefontwidth($this->cfg['value-label-font-size']);

		$result['text']['x1'] = $result['line']['x2'] - ($fontHeight / 2);
		$result['text']['y1'] = $result['line']['y1'] - $fontWidth;
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateAverage
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates average line & text position
	*/	
	protected function calculateAverage($datastructure, $points) {
		//Var
		$result = array(
			'line' => array(
				'x1' => 0,
				'y1' => 0,
				'x2' => 0,
				'y2' => 0
			),
			'text' => array(
				'x1' => 0,
				'y1' => 0
			)
		);

		//Test < 0
		if($datastructure['avg'] < 0) {
			//Calc
			$avgHeightPerc = 
				($points['section']['negative']['width'] / 100);
			$avgValPerc =  
				(-$datastructure['avg'] / 
				$datastructure['minDifference']) * 
				100;
			$avgValueHeight =  $avgHeightPerc * $avgValPerc;			
			
			//Set
			$result['line']['x1'] = $points['zero']['line']['x1'] - $avgValueHeight;
			$result['line']['y1'] = $points['box']['y1']; //$points['zero']['line']['y1'] + $avgValueHeight;
			$result['line']['x2'] = $result['line']['x1'];
			$result['line']['y2'] = $points['box']['y2'];
			
			//Text
			$result['text']['x1'] = $result['line']['x1'] - (imagefontheight($this->cfg['value-label-font-size']) / 2);
			$result['text']['y1'] = ($points['box']['y2'] + (strlen(ceil($datastructure['avg'])) * imagefontwidth($this->cfg['value-label-font-size']))) + 2;
		}
		else {
			//Calc
			$avgHeightPerc = 
				($points['section']['positive']['width'] / 100);
			$avgValPerc = ($datastructure['plusDifference'] > 0) ?
				($datastructure['avg'] / 
				$datastructure['plusDifference']) * 
				100 :
				0;
				
			$avgValueHeight = $avgHeightPerc * $avgValPerc;			
			
			//Set
			$result['line']['x1'] = $points['zero']['line']['x1'] + $avgValueHeight;;
			$result['line']['y1'] = $points['box']['y1'];
			$result['line']['x2'] = $result['line']['x1'];
			$result['line']['y2'] = $points['box']['y2'];	
			
			//Text
			$result['text']['x1'] = $result['line']['x1'] - (imagefontheight($this->cfg['value-label-font-size']) / 2);
			$result['text']['y1'] = ($points['box']['y2'] + (strlen(ceil($datastructure['avg'])) * imagefontwidth($this->cfg['value-label-font-size']))) + 2;
				
		}
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateLabel
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates label text position
	*/	
	protected function calculateLabel($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0
		);

		$result['x1'] = $points['box']['x2'] + 4;
		$result['y1'] = 
			($points['box']['y1'] - 
			imagefontheight($this->cfg['label-font-size'])) - 
			2;
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateTitle
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates title text position
	*/	
	protected function calculateTitle($datastructure, $points) {
		//Var
		$result = array(
			'x1' => 0,
			'y1' => 0
		);

		$result['x1'] = $points['box']['x1'];
		$result['y1'] = 
			($points['offset']['top']/2) - 
			(imagefontheight($this->cfg['title-font-size']) / 
			2);
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateValueLabels
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates value labesl text position (min, max)
	*/	
	protected function calculateValueLabels($datastructure, $points) {
		//Var
		$result = array(
			'min' => array (
				'x1' => 0,
				'y1' => 0
			),
			'max' => array (
				'x1' => 0,
				'y1' => 0
			)
		);
		
		$fontHeight = imagefontheight($this->cfg['value-label-font-size']);
		$fontWidth = imagefontwidth($this->cfg['value-label-font-size']);
		$maxWidth = ($fontWidth * strlen($datastructure['fakeMax']));
		$minWidth = ($fontWidth * strlen($datastructure['fakeMin']));
		
		//Max
		$result['max']['x1'] = $points['box']['x2'] - ($fontHeight / 2);
		$result['max']['y1'] = ($points['box']['y2'] + $maxWidth) + 4;

		//Min
		$result['min']['x1'] = $points['box']['x1'] - ($fontHeight / 2);
		$result['min']['y1'] = ($points['box']['y2'] + $minWidth) + 4;
		
		//Result
		return $result;
	}

//-------------------------------------------------------------------------+
	
	/**
	 * @name: calculateColumns
	 * @param: (array) $datastructure, (array) $points
	 * @return: (array) points
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Calculates column positions
	*/	
	protected function calculateColumns($datastructure, $points) {
		//Var
		$idx = 0;
		$colX = $points['box']['x1'];
		$colY = $points['box']['y1'];
		$previousPoints = NULL;
		$columns = array();
		
		//Loop data items
		foreach($datastructure['data'] as $key => $value) {
			
			//Calc
			$colWidthOffset = ($points['col']['height'] > 8) ? 3 : 0;
			$colHeightOffset = ($points['col']['height'] > 8) ? 3 : 0;
			
			//Var
			$result = array(
				'value' => array(
						'text' => $value,
						'x1' => 0,
						'y1' => 0,
					),
				'key' => array(
						'text' => $key,
						'x1' => 0,
						'y1' => 0,
					),
				'fill' => 0,
				'column' => array(
					'x1' => $points['box']['x1'],
					'y1' => $colY,
					'x2' => $points['box']['x2'],
					'y2' => $colY + $points['col']['height'],
					'x3' => $points['box']['x2'],
					'y3' => $colY + $points['col']['height'],
					'x4' => $points['box']['x1'],
					'y4' => $colY,
				),
				'bar' => array(
					'x1' => 0, //NEEDS CALC
					'y1' => $colY,
					'x2' => 0, //NEEDS CALC
					'y2' => $colY,
					'x3' => 0, //NEEDS CALC
					'y3' => $colY + $points['col']['height'],
					'x4' => 0, //NEEDS CALC
					'y4' => $colY + $points['col']['height']
				),
				'foregroundbar' => array(
					'x1' => 0, //NEEDS CALC
					'y1' => $colY + $colWidthOffset, 
					'x2' => 0, //NEEDS CALC
					'y2' => $colY + $colWidthOffset, 
					'x3' => 0, //NEEDS CALC
					'y3' => ($colY + $points['col']['height']) - ($colWidthOffset * 2),
					'x4' => 0, //NEEDS CALC
					'y4' => ($colY + $points['col']['height']) - ($colWidthOffset * 2) 
				),
				'shadowbar' => array(
					'x1' => 0, //NEEDS CALC
					'y1' => $colY + ($colWidthOffset * 2),
					'x2' => 0, //NEEDS CALC
					'y2' => $colY + ($colWidthOffset * 2),
					'x3' => 0, //NEEDS CALC
					'y3' => ($colY + $points['col']['height']) - $colWidthOffset,
					'x4' => 0, //NEEDS CALC 
					'y4' => ($colY + $points['col']['height']) - $colWidthOffset
				),
				'previous' => array(
					'value' => 0,
					'x1' => 0,
					'y1' => 0,
					'x2' => 0,
					'y2' => 0,
					'x3' => 0,
					'y3' => 0,
					'x4' => 0,
					'y4' => 0,
				),
				'line' => array(
					'x1' => 0,
					'y1' => 0,
					'x2' => 0,
					'y2' => 0,
				),
				'poly' => array(
				),
			);
			
			
			//$previousPoints
			if(is_null($previousPoints)) {
				$result['previous']['value'] = 0;
				$result['previous']['x1'] = $points['zero']['line']['x1'];
				$result['previous']['y1'] = $points['box']['y1'];
				$result['previous']['x2'] = $points['zero']['line']['x1'];
				$result['previous']['y2'] = $points['box']['y1'];
				$result['previous']['x3'] = $points['zero']['line']['x1'];
				$result['previous']['y3'] = $points['box']['y1'];
				$result['previous']['x4'] = $points['zero']['line']['x1'];
				$result['previous']['y4'] = $points['box']['y1'];
			}
			else {
				//Map
				$result['previous']['value'] = $previousPoints['value']['text'];
				$result['previous']['x1'] = $previousPoints['bar']['x1'];
				$result['previous']['y1'] = $previousPoints['bar']['y1'];
				$result['previous']['x2'] = $previousPoints['bar']['x2'];
				$result['previous']['y2'] = $previousPoints['bar']['y2'];
				$result['previous']['x3'] = $previousPoints['bar']['x3'];
				$result['previous']['y3'] = $previousPoints['bar']['y3'];
				$result['previous']['x4'] = $previousPoints['bar']['x4'];
				$result['previous']['y4'] = $previousPoints['bar']['y4'];
			}
			
			//Key placement
			$keyX = ($points['col']['height'] / 2) - (imagefontheight($this->cfg['key-font-size']) / 2);
			$result['key']['x1'] = 8; //($points['box']['y2'] + (strlen($key) * imagefontwidth($this->cfg['key-font-size']))) + 4;
			$result['key']['y1'] = $colY + $keyX;
			
			//Test plus or min quadrant
			
			if($value < 0) {
			
				//Calc value height
				$hgtPerc = ($points['section']['negative']['width'] / 100);
				$valPerc =  ($value / $datastructure['minDifference'])  * 100;
				$valueHeight =  $hgtPerc * $valPerc;
				
				//Fill percentage
				$result['fill'] = floor(-$valPerc);
				
				//Min
				$result['bar']['x1'] = $points['zero']['line']['x1'] + $valueHeight;
				$result['bar']['x2'] = $points['zero']['line']['x1']; //$result['bar']['x1'];
				$result['bar']['x3'] = $points['zero']['line']['x1'];
				$result['bar']['x4'] = $result['bar']['x1'];
				
				//Main		
				$result['foregroundbar']['x1'] =  $points['zero']['line']['x1'] + $valueHeight;
				$result['foregroundbar']['x2'] = $result['foregroundbar']['x1'];
				$result['foregroundbar']['x3'] = $points['zero']['line']['x1'];
				$result['foregroundbar']['x4'] = $result['foregroundbar']['x3'];


				//Shadow
				$result['shadowbar']['x1'] = ($points['zero']['line']['x1'] + $valueHeight) + $colHeightOffset;;
				$result['shadowbar']['x2'] = $result['shadowbar']['x1'];
				$result['shadowbar']['x3'] = $points['zero']['line']['x1'];
				$result['shadowbar']['x4'] = $result['shadowbar']['x3'];
		
			
				//Value text
				$valueLength = strlen($value) * imagefontwidth($this->cfg['value-font-size']);
				$valueHeight = imagefontheight($this->cfg['value-font-size']);
				
				//Text Y				
				$result['value']['y1'] = $colY + (($points['col']['height'] / 2) - ($valueHeight / 2));
				
				//Test sector
				if($valueLength > $points['section']['negative']['width']) {
					$result['value']['x1'] = ($points['zero']['line']['x1']) + 4;
				}
				else {
					//Test where value text is gonna be
					if($result['fill'] < 50) {
						$result['value']['x1'] = ($result['bar']['x1'] - $valueLength) - 4;
					}
					else {
						$result['value']['x1'] = ($points['zero']['line']['x1'] - $valueLength) - 4;
					}
				}
				
				//Polygon
				if($result['previous']['value'] < 0) {
					//Poly
					
					$result['poly'][0] = $result['previous']['x1'];
					$result['poly'][1] = $result['bar']['y1'];
					$result['poly'][2] = $result['bar']['x4'];
					$result['poly'][3] = $result['bar']['y4'];
					$result['poly'][4] = $result['bar']['x3'];
					$result['poly'][5] = $result['bar']['y3'];
					$result['poly'][6] = $result['bar']['x2'];
					$result['poly'][7] = $result['bar']['y2'];
					$result['poly'][8] = $result['previous']['x1'];
					$result['poly'][9] = $result['bar']['y1'];

					//Line
					$result['line']['x1'] = $result['previous']['x1'];
					$result['line']['y1'] = $result['previous']['y3'];
					$result['line']['x2'] = $result['bar']['x4'];
					$result['line']['y2'] = $result['bar']['y4'];
				}
				else {
					//Poly
					$result['poly'][0] = $result['previous']['x2'];
					$result['poly'][1] = $result['bar']['y2'];
					$result['poly'][2] = $result['bar']['x4'];
					$result['poly'][3] = $result['bar']['y4'];
					$result['poly'][4] = $result['bar']['x3'];
					$result['poly'][5] = $result['bar']['y3'];
					$result['poly'][6] = $result['bar']['x2'];
					$result['poly'][7] = $result['bar']['y2'];
					$result['poly'][8] = $result['previous']['x2'];
					$result['poly'][9] = $result['bar']['y2'];	
					
					//Line
					$result['line']['x1'] = $result['previous']['x3'];
					$result['line']['y1'] = $result['previous']['y3'];
					$result['line']['x2'] = $result['bar']['x1'];
					$result['line']['y2'] = $result['bar']['y3'];	
					
					//Test start index
					if ($idx == 0) {
						$result['line']['x1'] = $result['line']['x2'];
						$result['poly'][0] = $result['poly'][2];
					}								
				}
			
			}
			else {
				
				//Calc value height
				$hgtPerc = ($points['section']['positive']['width'] / 100);
				$valPerc = 
					($datastructure['plusDifference'] > 0) ?
						($value / $datastructure['plusDifference'])  * 100 :
						0;
				$valueHeight =  $hgtPerc * $valPerc;

				//Fill percentage
				$result['fill'] = $valPerc;

				//plus
				$result['bar']['x1'] = $points['zero']['line']['x1'];
				$result['bar']['x2'] = $result['bar']['x1'] + $valueHeight;
				$result['bar']['x3'] = $result['bar']['x2'];
				$result['bar']['x4'] = $result['bar']['x1'];

				
				//Main
				$result['foregroundbar']['x1'] =  $points['zero']['line']['x1'];
				$result['foregroundbar']['x2'] = $result['foregroundbar']['x1'];
				$result['foregroundbar']['x3'] = $points['zero']['line']['x1'] + $valueHeight;
				$result['foregroundbar']['x4'] = $result['foregroundbar']['x3'];
				
				//Shadow		
				$result['shadowbar']['x1'] = $points['zero']['line']['x1'];
				$result['shadowbar']['x2'] = $result['foregroundbar']['x1'];
				//$result['shadowbar']['x3'] = ($points['zero']['line']['x1'] + $valueHeight) + $colHeightOffset; //ORG
				$result['shadowbar']['x3'] = ($value === 0) ? $points['zero']['line']['x1'] : ($points['zero']['line']['x1'] + $valueHeight) + $colHeightOffset;
				$result['shadowbar']['x4'] = $result['foregroundbar']['x3'];

				
				//Value text
				$valueLength = strlen($value) * imagefontwidth($this->cfg['value-font-size']);
				$valueFontHeight = imagefontheight($this->cfg['value-font-size']);
				
				//OLD $result['value']['x1'] = $colX + (($points['col']['height'] / 2) - ($valueFontHeight / 2));
				$result['value']['y1'] = $colY + (($points['col']['height'] / 2) - ($valueFontHeight / 2));
				
				//Test sector
				if( ($valueLength +($colHeightOffset + 6)) > $points['section']['positive']['width']) {
					//OLD $result['value']['y1'] = ($points['zero']['line']['y1'] + $valueLength) + 6;
					$result['value']['x1'] = ($points['zero']['line']['x1'] + $valueLength) + 6;
				}
				else {
					if($result['fill'] < 50) {
						//OLD $result['value']['y1'] = ($points['zero']['line']['y1'] - $valueHeight) - ($colHeightOffset + 6);
						$result['value']['x1'] = ($points['zero']['line']['x1'] + $valueHeight) + ($colHeightOffset + 4);
					}
					else {
						//OLD $result['value']['y1'] = ($points['zero']['line']['y1']) - 6;
						$result['value']['x1'] = ($points['zero']['line']['x1']) + 4;
					}
				}
						
				//Polygon & line
				if($result['previous']['value'] < 0) {
					//Poly
					
					$result['poly'][0] = $result['previous']['x1'];
					$result['poly'][1] = $result['bar']['y1'];
					$result['poly'][2] = $result['bar']['x3'];
					$result['poly'][3] = $result['bar']['y3'];
					$result['poly'][4] = $result['bar']['x4'];
					$result['poly'][5] = $result['bar']['y4'];
					$result['poly'][6] = $result['bar']['x1'];
					$result['poly'][7] = $result['bar']['y1'];
					$result['poly'][8] = $result['previous']['x1'];
					$result['poly'][9] = $result['bar']['y1'];

					//Line
					$result['line']['x1'] = $result['previous']['x4'];
					$result['line']['y1'] = $result['previous']['y4'];
					$result['line']['x2'] = $result['bar']['x3'];
					$result['line']['y2'] = $result['bar']['y3'];
				}
				else {
					
					//Poly
					$result['poly'][0] = $result['previous']['x3'];
					$result['poly'][1] = $result['bar']['y1'];
					$result['poly'][2] = $result['bar']['x3'];
					$result['poly'][3] = $result['bar']['y3'];
					$result['poly'][4] = $result['bar']['x4'];
					$result['poly'][5] = $result['bar']['y4'];
					$result['poly'][6] = $result['bar']['x1'];
					$result['poly'][7] = $result['bar']['y1'];
					$result['poly'][8] = $result['previous']['x3'];
					$result['poly'][9] = $result['bar']['y1'];	
					
					
					//Line
					$result['line']['x1'] = $result['previous']['x3'];
					$result['line']['y1'] = $result['previous']['y3'];
					$result['line']['x2'] = $result['bar']['x3'];
					$result['line']['y2'] = $result['bar']['y3'];

					//Test start index
					if ($idx == 0) {
						$result['line']['x1'] = $result['line']['x2'];
						$result['poly'][0] = $result['poly'][2];
					}								
								
				}
			}

			//Append
			$columns[$key] = $result;
			
			//Save previous entry
			$previousPoints = $result;
			
			//Set new X
			//$colX = $colX + $points['col']['width'];
			$colY = $colY + $points['col']['height'];
			
			//Up index
			$idx++;
		}


		//Result
		return $columns;
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawImage
	 * @param: none
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the main image
	*/	
	protected function drawImage() {
		//Create image
		$this->ip = $this->createImage($this->cfg['width'], $this->cfg['height']);
		
		//allocateColor
		$backgroundColor = $this->allocateColor($this->cfg['background-color']);
		
		//Fill
		imagefill($this->ip, 0, 0, $backgroundColor);
		
		//Test for background image
		if(!empty($this->cfg['background-image'])) {
			//Create background-image
			if($bgkImage = $this->createImageFromFile($this->cfg['background-image'])) {
				imagecopy($this->ip, $bgkImage, 0, 0, 0, 0, $this->cfg['width'], $this->cfg['height']);
			}
		}		
	}
	
//-------------------------------------------------------------------------+

	/**
	 * @name: drawTitle
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the title
	*/	
	protected function drawTitle($graphPoints) {
		//Test for visible
		if($this->cfg['title-visible']) {
			//allocateColor
			$titleColor = $this->allocateColor($this->cfg['title-color']);
			
			//Draw string
			imagestring($this->ip, $this->cfg['title-font-size'], $graphPoints['title']['x1'], $graphPoints['title']['y1'], $this->cfg['title'], $titleColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawValueLabels
	 * @param: (array) $graphPoints, (array) $dataStructure
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the value labels (min, max)
	*/	
	protected function drawValueLabels($graphPoints, $dataStructure) {
		//Test for visible
		if($this->cfg['value-label-visible']) {
			//allocateColor
			$valueLabelColor = $this->allocateColor($this->cfg['value-label-color']);

			//Draw strings
			if( $this->cfg['label-right-align'] ){
				$lblTxtMin = str_pad( $dataStructure['fakeMin'], $dataStructure['maxValueLength'], " ", STR_PAD_LEFT);
				$lblTxtMax = str_pad( $dataStructure['fakeMax'], $dataStructure['maxValueLength'], " ", STR_PAD_LEFT);
			}else{
				$lblTxtMin = $dataStructure['fakeMin'];
				$lblTxtMax = $dataStructure['fakeMax'];
			}
			
			//Test for when fakemin & max are less or equal to 0
			if($dataStructure['fakeMin'] < 0 && $dataStructure['fakeMax'] <= 0) {
				$lblTxtMax = "0";
			}
			
			//Draw strings
			imagestringup($this->ip, $this->cfg['value-label-font-size'], $graphPoints['value']['min']['x1'], $graphPoints['value']['min']['y1'], $lblTxtMin, $valueLabelColor);
			imagestringup($this->ip, $this->cfg['value-label-font-size'], $graphPoints['value']['max']['x1'], $graphPoints['value']['max']['y1'], $lblTxtMax, $valueLabelColor);
		}			
	
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawBoxBackground
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the graph box background
	*/	
	protected function drawBoxBackground($graphPoints) {
		//Test for visible
		if($this->cfg['box-background-visible']) {
			//allocateColor
			$boxBackgroundColor = $this->allocateColor($this->cfg['box-background-color'], $this->cfg['box-background-alpha']);
			
			//Draw rectangle
			imagefilledrectangle($this->ip, $graphPoints['box']['x1'], $graphPoints['box']['y1'], $graphPoints['box']['x2'], $graphPoints['box']['y2'], $boxBackgroundColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawVerticaldividers
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the vertical dividers
	*/	
	protected function drawVerticaldividers($graphPoints) {
		//Test for visible
		if($this->cfg['horizontal-divider-visible']) {
			//Set offset
			$offset = ($graphPoints['box']['width'] / 4);
			//Allocate colors
			$color = $this->allocateColor($this->cfg['horizontal-divider-color'], $this->cfg['horizontal-divider-alpha']);
			$alpha = $this->allocateColor($this->hex2Rgb('FFFFFF'), 127);
			//Define line style
			$style = array($color, $color, $alpha, $alpha);
			//Set line style
			imagesetstyle($this->ip, $style);			
			//Loop 3 times
			for($i = 1; $i <= 3; $i++) {
				//Calc y
				$x = $graphPoints['box']['x1'] + ($i * $offset);
				//Draw line
				imageline($this->ip,  $x, $graphPoints['box']['y1'], $x, $graphPoints['box']['y2'], IMG_COLOR_STYLED);
			}
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawLabel
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the label
	*/	
	protected function drawLabel($graphPoints) {
		//Test for visible
		if($this->cfg['label-visible']) {
			//allocateColor
			$labelColor = $this->allocateColor($this->cfg['label-color']);
			
			//Draw string
			imagestring($this->ip, $this->cfg['label-font-size'], $graphPoints['label']['x1'], $graphPoints['label']['y1'], $this->cfg['label'], $labelColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawBoxBorder
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the graph box border
	*/	
	protected function drawBoxBorder($graphPoints) {
		//Test for visible
		if($this->cfg['box-border-visible']) {
			//allocateColor
			$boxBorderColor = $this->allocateColor($this->cfg['box-border-color'], $this->cfg['box-border-alpha']);
			
			//Draw rectangle
			imagerectangle($this->ip, $graphPoints['box']['x1'], $graphPoints['box']['y1'], $graphPoints['box']['x2'], $graphPoints['box']['y2'], $boxBorderColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawAverage
	 * @param: (array) $graphPoints, (array) $dataStructure
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the average line & text
	*/	
	protected function drawAverage($graphPoints, $dataStructure) {
		//Test visible
		if($this->cfg['average-line-visible']) {
			//allocateColor
			$avgLineColor = 
				$this->allocateColor($this->cfg['average-line-color'], 
									 $this->cfg['average-line-alpha']);
			
			//Draw line
			imageline($this->ip, 
				$graphPoints['average']['line']['x1'], 
				$graphPoints['average']['line']['y1'] - 2, 
				$graphPoints['average']['line']['x2'], 
				$graphPoints['average']['line']['y2'] + 2, 
				$avgLineColor);
			
			//Draw string
			imagestringup($this->ip, 2, 
				$graphPoints['average']['text']['x1'], 
				$graphPoints['average']['text']['y1'], 
				ceil($dataStructure['avg']), 
				$avgLineColor);
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawSections
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws positive & negative sections
	*/	
	protected function drawSections($graphPoints) {
		$posColor = 
			$this->allocateColor($this->hex2Rgb('00FF00'), 
								$this->cfg['zero-line-alpha']);
	
		$negColor = 
			$this->allocateColor($this->hex2Rgb('FF0000'), 
								$this->cfg['zero-line-alpha']);
		
		
		imagefilledrectangle($this->ip, $graphPoints['section']['positive']['x1'], $graphPoints['section']['positive']['y1'], $graphPoints['section']['positive']['x2'], $graphPoints['section']['positive']['y2'], $posColor);
		imagefilledrectangle($this->ip, $graphPoints['section']['negative']['x1'], $graphPoints['section']['negative']['y1'], $graphPoints['section']['negative']['x2'], $graphPoints['section']['negative']['y2'], $negColor);
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawZero
	 * @param: (array) $graphPoints
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the zero line & text
	*/	
	protected function drawZero($graphPoints) {
	
		
		//Test visible
		if($this->cfg['zero-line-visible']) {
			
			if($graphPoints['zero']['line']['x1'] != 
				$graphPoints['box']['x1']) {
				
				//allocateColor
				$color = 
					$this->allocateColor($this->cfg['zero-line-color'], 
										$this->cfg['zero-line-alpha']);


				//Draw line
				imageline($this->ip, 
					$graphPoints['zero']['line']['x1'], 
					$graphPoints['zero']['line']['y1'] - 2, 
					$graphPoints['zero']['line']['x2'], 
					$graphPoints['zero']['line']['y2'] + 2, 
					$color);
				
				//Draw string
				imagestringup($this->ip, 
					$this->cfg['value-label-font-size'], 
					$graphPoints['zero']['text']['x1'], 
					$graphPoints['zero']['text']['y1'], 
					0, 
					$color);
			}
		}
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumns
	 * @param: (array) $graphPoints, (array) $dataStructure
	 * @return: void
	 * @access: protected
	 * @exception: no
	 * @since: version 5.0.1
	 * @comment: Draws the columns (Text alignment thanks to Jack Finch)
	*/	
	protected function drawColumns($graphPoints, $dataStructure, $compareStructure = false) {
		//Var
		$colIdx = 1;
		
		//Allocate colors
		$keyColor = $this->allocateColor($this->cfg['key-color']);
		$valueColor = $this->allocateColor($this->cfg['value-color']);
		$columnColor = $this->allocateColor($this->cfg['column-color'], $this->cfg['column-alpha']); 
		$columnShadowColor = $this->allocateColor($this->cfg['column-shadow-color'], $this->cfg['column-shadow-alpha']); 
		
		//Loop
		foreach($graphPoints['columns'] as $colPoint) {

			
			//Test for random colors
			if($this->cfg['column-color-random']) {
				$colors = $this->generateRandomColor();
				$columnColor = $this->allocateColor($colors['forecolor'], $this->cfg['column-alpha']); 
				$columnShadowColor = $this->allocateColor($colors['backcolor'], $this->cfg['column-shadow-alpha']);
			}

			if($compareStructure) {
				$columnColor = $this->allocateColor($this->cfg['column-compare-color'], 0); 
				$columnShadowColor = $this->allocateColor($this->cfg['column-compare-shadow-color'], 0); 
			}
			
			//Column
			$this->drawColumn($colPoint, $columnColor, $columnShadowColor);
			
			//Value text
			if($this->cfg['value-visible']) {
				if($graphPoints['col']['show']) {
					if(!$compareStructure) {
						imagestring($this->ip, $this->cfg['value-font-size'], $colPoint['value']['x1'], $colPoint['value']['y1'], $colPoint['value']['text'], $valueColor);
					}
				}
			}
						
			//Test for visible
			if($this->cfg['column-divider-visible']) {
				//Do not print first col
				if($colIdx!=1) {
					if(!$compareStructure) {
						//Allocate colors
						$columndividerColor = $this->allocateColor($this->cfg['column-divider-color'], $this->cfg['column-divider-alpha']);
						$columndividerAlphaColor = $this->allocateColor($this->hex2Rgb('FFFFFF'), 127);
						//Define style
						$dottedStyle = array($columndividerColor, $columndividerColor, $columndividerAlphaColor, $columndividerAlphaColor);
						//Set style
						imagesetstyle($this->ip, $dottedStyle);
						//Draw line
						imageline($this->ip,  $colPoint['column']['x1'], $colPoint['column']['y1'], $colPoint['column']['x3'], $colPoint['column']['y1'], IMG_COLOR_STYLED);
					}
				}
			}
					
				
			//Determin if only firts and last key are visible
			if($this->cfg['key-visible']) {
				if(!$compareStructure) {
					$printCol = true;
					if(!$graphPoints['col']['show']) {
						$printCol = (($colIdx==1) || ($colIdx==$dataStructure['cols']));
					}
					if($printCol) {
						//Alignment code thanks to Jack Finch
						if( $this->cfg['key-right-align'] ){
							$keyTxt = str_pad( $colPoint['key']['text'], $dataStructure['maxKeyLength'], " ", STR_PAD_LEFT);
						}else{
							$keyTxt = $colPoint['key']['text'];
						}
					
						imagestring($this->ip, $this->cfg['key-font-size'], $colPoint['key']['x1'], $colPoint['key']['y1'], $keyTxt, $keyColor);
					
						//ORGimagestring($this->ip, $this->cfg['key-font-size'], $colPoint['key']['x1'], $colPoint['key']['y1'], $colPoint['key']['text'], $keyColor);
					}
				}
			}
											
			//Up
			$colIdx++;			
			
		} //End loop
		
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column (Needs override)
	*/	
	protected function drawColumn($colPoint, 
								  $columnColor, 
								  $columnShadowColor) {
		throw new Exception('drawColumn is not implemented');
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: parse
	 * @param: (array) $data, [(array) $cfg=array()]
	 * @return: void
	 * @access: public
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Parses the graph
	*/	
	public function parse($data, $cfg = array()){
		try {
			//Parse config
			$this->parseConfig($cfg);

			//Get data structure
			$dataStructure = $this->parseDataStructure($data);
			
			//Calculate graph structure
			$graphPoints = $this->calculateGraph($dataStructure);

			//Draw
			$this->drawImage();
			$this->drawTitle($graphPoints);
			$this->drawValueLabels($graphPoints, $dataStructure);
			$this->drawBoxBackground($graphPoints);
			$this->drawColumns($graphPoints, $dataStructure);
			$this->drawVerticaldividers($graphPoints);
			$this->drawLabel($graphPoints);
			$this->drawBoxBorder($graphPoints);
			$this->drawAverage($graphPoints, $dataStructure);
			$this->drawZero($graphPoints);
		}
		catch(Exception $ex) {
			//Parse error message overriding original
			$this->parseError($ex);
		}
		
		//Parse image
		$this->parseImage();
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: parseCompare
	 * @param: (array) $data1, (array) $data2, [(array) $cfg=array()]
	 * @return: void
	 * @access: public
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Parses a compare graph (max 2 data arrays)
	*/	
	public function parseCompare($data1, $data2, $cfg = array()){
		try {
			//Parse config
			$this->parseConfig($cfg);

			//Get data structure
			$dataStructure1 = $this->parseDataStructure($data1);
			$dataStructure2 = $this->parseDataStructure($data2);
			$compareStructure = $this->compareDataStructures($dataStructure1, $dataStructure2);
			
			
			if(count($compareStructure['structures'])<0) {
				throw new Exception('Not enough datastructures found');
			}
			
			//Calculate graph structure
			$firstStructure = $compareStructure['structures'][0];
			$graphPoints = $this->calculateGraph($firstStructure);
			
			//Unset value-label-text
			$this->cfg['value-visible'] = false;
			$this->cfg['column-alpha'] = 30;
			$this->cfg['column-shadow-alpha'] = 127;
			
			//Draw
			$this->drawImage();
			$this->drawTitle($graphPoints);
			$this->drawValueLabels($graphPoints, $firstStructure);
			$this->drawBoxBackground($graphPoints);
				foreach($compareStructure['structures'] as $idx =>  $dataStructure) {
					$graphPoints2 = $this->calculateGraph($dataStructure);
					$this->drawColumns($graphPoints2, $dataStructure, ($idx!=1));
				}
			$this->drawVerticaldividers($graphPoints);
			$this->drawLabel($graphPoints);
			$this->drawBoxBorder($graphPoints);
			$this->drawAverage($graphPoints, $firstStructure);
			$this->drawZero($graphPoints);
			
			
			
		}
		catch(Exception $ex) {
			//Parse error message overriding original
			$this->parseError($ex);
		}
		
		//Parse image
		$this->parseImage();
	}

//-------------------------------------------------------------------------+

	/**
	 * @name: parseExample
	 * @param: void
	 * @return: void
	 * @access: public
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Parses an example graph
	*/	
	public function parseExample(){
		//Set config
		$cfg['title'] = 'Example graph';
		$cfg['label'] = 'orders';
		$cfg['height'] = 400;
		$cfg['width'] = 300;
		
		//Set data
		$data = array(
			'Jan' => 12,
			'Feb' => 25,
			'Mar' => 0,
			'Apr' => -7,
			'May' => 80,
			'Jun' => 67,
			'Jul' => 45,
			'Aug' => 66,
			'Sep' => -23,
			'Oct' => 23,
			'Nov' => 78,
			'Dec' => 23
		);
		
		//Parse
		$this->parse($data, $cfg);

	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# horizontalLineGraph
//##########################################################################

/**
* @name			horizontalLineGraph
* @type			class
* @extends		horizontalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates a vertical line graph
*/
class horizontalLineGraph extends horizontalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column line
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Line
		imageline($this->ip, $colPoint['line']['x1'], $colPoint['line']['y1'], $colPoint['line']['x2'], $colPoint['line']['y2'], $columnColor);
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# horizontalSimpleColumnGraph
//##########################################################################

/**
* @name			horizontalSimpleColumnGraph
* @type			class
* @extends		horizontalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates simple vertical columns
*/
class horizontalSimpleColumnGraph extends horizontalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column line
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Normal col
		imagefilledrectangle($this->ip, $colPoint['bar']['x1'], $colPoint['bar']['y1'], $colPoint['bar']['x3'], $colPoint['bar']['y3'], $columnColor);	
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# horizontalColumnGraph
//##########################################################################

/**
* @name			horizontalColumnGraph
* @type			class
* @extends		horizontalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates a horizontal column graph
*/
class horizontalColumnGraph extends horizontalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column line
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Normal col
		imagefilledrectangle($this->ip, $colPoint['shadowbar']['x1'], $colPoint['shadowbar']['y1'], $colPoint['shadowbar']['x3'], $colPoint['shadowbar']['y3'], $columnShadowColor);	
		imagefilledrectangle($this->ip, $colPoint['foregroundbar']['x1'], $colPoint['foregroundbar']['y1'], $colPoint['foregroundbar']['x3'], $colPoint['foregroundbar']['y3'], $columnColor);	
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# horizontalPolygonGraph
//##########################################################################

/**
* @name			horizontalPolygonGraph
* @type			class
* @extends		horizontalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates a horizontal polygon graph
*/
class horizontalPolygonGraph extends horizontalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column line
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Poly
		imagefilledpolygon ($this->ip , $colPoint['poly'] , count( $colPoint['poly']) / 2 ,$columnColor);
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# verticalLineGraph
//##########################################################################

/**
* @name			verticalLineGraph
* @type			class
* @extends		verticalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates a vertical line graph
*/
class verticalLineGraph extends verticalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column line
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Line
		imageline($this->ip, $colPoint['line']['x1'], $colPoint['line']['y1'], $colPoint['line']['x2'], $colPoint['line']['y2'], $columnColor);
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# verticalSimpleColumnGraph
//##########################################################################

/**
* @name			verticalSimpleColumnGraph
* @type			class
* @extends		verticalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates simple vertical columns
*/
class verticalSimpleColumnGraph extends verticalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 column simple
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Simple column
		imagefilledrectangle($this->ip, $colPoint['bar']['x1'], $colPoint['bar']['y1'], $colPoint['bar']['x3'], $colPoint['bar']['y3'], $columnColor);	
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# verticalColumnGraph
//##########################################################################

/**
* @name			verticalColumnGraph
* @type			class
* @extends		verticalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates shadowed horizontal columns
*/
class verticalColumnGraph extends verticalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 shadow column
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Column with shadow
		imagefilledrectangle($this->ip, $colPoint['shadowbar']['x1'], $colPoint['shadowbar']['y1'], $colPoint['shadowbar']['x3'], $colPoint['shadowbar']['y3'], $columnShadowColor);	
		imagefilledrectangle($this->ip, $colPoint['foregroundbar']['x1'], $colPoint['foregroundbar']['y1'], $colPoint['foregroundbar']['x3'], $colPoint['foregroundbar']['y3'], $columnColor);	
	}

//-------------------------------------------------------------------------+

} //End class

//##########################################################################
//# verticalPolygonGraph
//##########################################################################

/**
* @name			verticalPolygonGraph
* @type			class
* @extends		verticalGraphBase
* @package      graph
* @version      5.0.1
* @comment:		Creates a filled polygon
*/
class verticalPolygonGraph extends verticalGraphBase {

//-------------------------------------------------------------------------+

	/**
	 * @name: drawColumn
	 * @param: (array) $colPoint, (rec) $columnColor, (rec) $columnShadowColor
	 * @return: void
	 * @access: protected
	 * @exception: yes
	 * @since: version 5.0.1
	 * @comment: Draws 1 poly column
	*/	
	protected function drawColumn($colPoint, $columnColor, $columnShadowColor) {
		//Polygon
		imagefilledpolygon ($this->ip , $colPoint['poly'] , 5 ,$columnColor);
	}

//-------------------------------------------------------------------------+

} //End class

?>
