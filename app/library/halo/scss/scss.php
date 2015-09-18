<?php
require_once dirname(__FILE__) . "/scss.inc.php";

use Leafo\ScssPhp\Server;

if(!defined('SASS_FOLDER')){
	define('SASS_FOLDER', dirname(__FILE__) . '/assets/');
}
class HALOSassCompiler
{
	public static $sassFolder = SASS_FOLDER;
	
	static public $variables = '';
	static public $extraBuffer = '';
	
	public static function setSassFolder($folder){
		self::$sassFolder = $folder;
	}
	
	public static function setVariables($variables) {
		$vars = "";
		if(is_array($variables)) {
			foreach($variables as $key => $val) {
				$vars = $vars . "$" . $key . " : " . $val .";\n"; 
			}
		} else {
			$vars = $variables;
		}
		self::$variables = $vars;
	}

	public static function appendBuffer($buffer){
		self::$extraBuffer = $buffer;
	}
  /**
   * Compiles all .scss files in a given folder into .css files in a given folder
   *
   * @param string $scssFolder source folder where you have your .scss files
   * @param string $cssFolder destination folder where you want your .css files
   * @param string $formatStyle CSS output format, see http://leafo.net/scssphp/docs/#output_formatting for more.
   */
  static public function run($scssFile, $scssFolder, $cssFolder, $formatStyle = "scss_formatter")
  {
		set_time_limit(0);
		
		$scssFolder = self::$sassFolder . $scssFolder;
		$cssFolder = self::$sassFolder . $cssFolder;
		
        $scss_compiler = new scssc();
        $scss_compiler->setImportPaths($scssFolder);
        $scss_compiler->setFormatter($formatStyle);
		// get path elements from that file
		$fileParts = pathinfo($scssFolder . $scssFile);
		// get file's name without extension
		$fileName = $fileParts['filename'];
		// get .scss's content, put it into $string_sass
		$string_sass = file_get_contents($scssFolder . $fileName . ".scss");
		
		if(self::$variables) {
			$string_sass = self::$variables . "\n" . $string_sass;
		}

		if(self::$extraBuffer){
			$string_sass = $string_sass . "\n" . self::$extraBuffer;
			//clear extra buffer after usage
			self::$extraBuffer = '';
		}
		// compile this Sass code to CSS
		$output = $scss_compiler->compile($string_sass);
		// write CSS into file with the same filename, but .css extension
		if($formatStyle === 'scss_formatter_compressed') {
			$outputFile = $cssFolder . $fileName . ".min.css";
		} else {
			$outputFile = $cssFolder . $fileName . ".css";
		}
		file_put_contents($outputFile, $output);
  }
}