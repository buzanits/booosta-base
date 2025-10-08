<?php
namespace booosta\base;

$module = \booosta\Framework::$wrappermodule;

if(!class_exists("\\booosta\\base\\Wrapperbase"))
eval("
namespace booosta\\base;
include_once 'vendor/booosta/$module/src/init.php';
class Wrapperbase { use \\booosta\\$module\\webapp; protected \$modulename = '$module'; }
");

class Wrapper extends Wrapperbase
{
  protected $includes = '';
  public $base_dir = '';
  static protected $first = true;

  public function exec()
  {
    $this->moduleinfo[$this->modulename] = true;

    if(is_callable([$this, "preparse_$this->modulename"])) $this->{"preparse_$this->modulename"}();
  }

  public function get_includes($onlyfirst = false)
  {
    if($onlyfirst && !self::$first):
      self::$first = false;
      return '';
    endif;

    self::$first = false;
    return $this->includes;
  }

  public function add_includes($str)
  {
    $this->includes .= $str;
  }
}

