<?php
namespace booosta\base;

$module = \booosta\Framework::$wrappermodule;

eval("
namespace booosta\\base;
include_once 'vendor/booosta/$module/src/init.php';
class Wrapperbase { use \\booosta\\$module\\webapp; protected \$modulename = '$module'; }
");

class Wrapper extends Wrapperbase
{
  protected $includes = '';
  public $base_dir = '';

  public function exec()
  {
    $this->moduleinfo[$this->modulename] = true;

    if(is_callable([$this, "preparse_$this->modulename"])) $this->{"preparse_$this->modulename"}();
  }

  public function get_includes()
  {
    return $this->includes;
  }

  public function add_includes($str)
  {
    $this->includes .= $str;
  }
}

