<?php
namespace booosta;

class Framework
{
  static public $module_traits = [];
  static public $CONFIG = [];
  static public $classmap = [];

  public static function load()
  {
    include_once 'local/config.incl.php';

    if(php_sapi_name() != 'cli') session_start();

    foreach(glob('vendor/buzanits/booosta*') as $moduledir)
      if(is_readable("$moduledir/init.php"))
        include("$moduledir/init.php");

    foreach(glob('vendor/buzanits/booosta*') as $moduledir)
      if(is_readable("$moduledir/main.php"))
        include("$moduledir/main.php");

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
    return is_dir("lib/modules/$module");
  }
  
  public static function require_module($module)
  {
    $modules = explode(',', $module);
    foreach($modules as $mod) if(!module_exists($mod)) throw new Exception("Required module $mod missing");
  }

  public static function ifeval($condition, $scope = null)
  {
    #print "condition: $condition\n";
    if($condition == '') $condition = 'false';
    if($scope === null) $V = $GLOBALS; else $V = $scope;
    $condition = preg_replace("/\\\$([A-Za-z0-9_]+)/", '$V[$1]', $condition);
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
  
  
  public static function debug($data, $file = 'debug.msg') { file_put_contents($file, print_r($data, true) . "\n", FILE_APPEND); }
  public static function ttime($msg = '') { debug(microtime(true) . ' ' . $msg); }
}
