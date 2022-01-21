<?php
namespace booosta;

class Framework
{
  static public $module_traits = [];
  static public $CONFIG = [];
  static public $module_config = [];
  static public $classmap = [];

  public static function load()
  {
    include_once 'local/config.incl.php';

    if(php_sapi_name() != 'cli') session_start();
    error_reporting(E_COMPILE_ERROR | E_ERROR | E_PARSE);

    foreach(glob('vendor/booosta/*') as $moduledir)
      if(is_readable("$moduledir/src/init.php"))
        include("$moduledir/src/init.php");

    foreach(glob('vendor/booosta/*') as $moduledir)
      if(is_readable("$moduledir/src/main.php"))
        include("$moduledir/src/main.php");

    if(is_dir('incl'))
      foreach(glob('incl/*.incl.php') as $inclfile)
        if(is_readable($inclfile)) include_once($inclfile);
  }

  public static function add_module_trait($module, $traitname)
  {
    self::$module_traits[$module][] = "\\booosta\\$traitname";
  }

  public static function init_module($module)
  {
    $traits = '';
    if(isset(self::$module_traits[$module])) $traits = implode(',', self::$module_traits[$module]);
    if($traits != '') $use = "use $traits;"; else $use = '';
    #print("namespace booosta\\$module { trait moduletrait_$module { $use } }\n");
    eval("namespace booosta\\$module { trait moduletrait_$module { $use } }");
  }

  public static function module_exists($module)
  {
    return is_dir("vendor/booosta/$module");
  }
  
  public static function require_module($module)
  {
    $modules = explode(',', $module);
    foreach($modules as $mod) if(!module_exists($mod)) throw new Exception("Required module $mod missing");
  }

  public static function ifeval($condition, $scope = null)
  {
    #\booosta\Framework::debug("condition: $condition");
    if($condition == '') $condition = 'false';
    if($scope === null) $V = $GLOBALS; else $V = $scope;
    $condition = preg_replace("/\\\$([A-Za-z0-9_]+)/", '$V["$1"]', $condition);
    #\booosta\Framework::debug("condition after: $condition");
    return eval("if($condition) return true; else return false;");
  }
  
  public static function check_utf8($str)
  {
    $len = strlen($str);
    for($i = 0; $i < $len; $i++):
      $c = ord($str[$i]);
      if($c > 128):
        if(($c > 247)) return false;
        elseif ($c > 239) $bytes = 4;
        elseif ($c > 223) $bytes = 3;
        elseif ($c > 191) $bytes = 2;
        else return false;
  
        if(($i + $bytes) > $len) return false;
  
        while ($bytes > 1):
          $i++;
          $b = ord($str[$i]);
          if($b < 128 || $b > 191) return false;
          $bytes--;
        endwhile;
      endif;
    endfor;
    return true;
  }
  
  
  public static function to_utf8($str)
  {
    if(!check_utf8($str)) return utf8_encode($str);
    return $str;
  }
  
  
  public static function preg_get($regexp, $haystack)
  {
    preg_match($regexp, $haystack, $result, PREG_OFFSET_CAPTURE);
    return $result[1][0];
  }
  

  /*
  We need this file to be able to do something like
  chdir('../../..');
  even if Booosta is symlinked into the webspace. chdir() stays in the linked directory tree what is bad.

  If you need to chance an other amount than 3 levels, you must set $dirlevels__ to a different value before including this file
  */

  public static function croot($dirlevels__ = 4)
  {
    #$dir__ = dirname($_SERVER['SCRIPT_FILENAME']);
    $dir__ = __DIR__;
    $tmp__ = explode('/', $dir__);
    for($i__=0;$i__<$dirlevels__;$i__++) array_pop($tmp__);
    $dir__ = implode('/', $tmp__);
    chdir($dir__);
    unset($dirlevels__, $dir__, $tmp__, $i__);
  }
  

  public static function debug($data, $file = 'debug.msg') { file_put_contents($file, print_r($data, true) . "\n", FILE_APPEND); }
  public static function ttime($msg = '') { debug(microtime(true) . ' ' . $msg); }
}
