<?php
namespace booosta\base;
\booosta\Framework::init_module('base');

abstract class Base
{
  use moduletrait_base;

  public $parentobj, $topobj;
  protected $error;
  protected $__get;
  protected static $sharedInfo = [];

  public function __construct()
  {
    $this->parentobj = $GLOBALS['parentobj'] ?? null;
    $this->topobj = $GLOBALS['topobj'] ?? $this;
    $this->__get = [];

    $methods = get_class_methods($this);
    foreach($methods as $method)
      if(substr($method, 0, 8) == 'autorun_')
        call_user_func([$this, $method]);
  }

  public function makeInstance()
  {
    $params = func_get_args();
    $classname = array_shift($params);
    #\booosta\Framework::debug("start makeInstance $classname");

    try { $reflector = new \ReflectionClass($classname); }
    catch(\ReflectionException $e) 
    { 
      try {
        if(!strstr($classname, "\\")) $reflector = new \ReflectionClass("\\booosta\\" . lcfirst($classname) . "\\" . $classname);
        else throw $e;
      }
      catch(\ReflectionException $e)
      {
        if(!strstr($classname, "\\")) $reflector = new \ReflectionClass("\\booosta\\" . lcfirst($classname) . "\\" . ucfirst($classname));
        else throw $e;

      }
    }

    #\booosta\Framework::debug("in makeInstance $classname");
    $save_parentobj = $GLOBALS['parentobj'] ?? null;
    $save_topobj = $GLOBALS['topobj'] ?? null;

    $GLOBALS['parentobj'] = $this;
    $GLOBALS['topobj'] = $this->topobj;

    $obj = $reflector->newInstanceArgs($params);
    #\booosta\Framework::debug("done makeInstance $classname");

    $GLOBALS['parentobj'] = $save_parentobj;
    $GLOBALS['topobj'] = $save_topobj;

    if(!is_object($obj->parentobj)) $obj->parentobj = $this;
    if(!is_object($obj->topobj)) $obj->topobj = $this->topobj;

    $obj->after_instanciation();

    return $obj;
  }

  protected function config()
  {
    $config = \booosta\Framework::$CONFIG;

    $params = func_get_args();

    foreach($params as $param)
      if(isset($config[$param])) $config = $config[$param];
      else return null;

    return $config;
  }

  public function error($set = null)
  {
    if($set === null) return $this->error;
    if(is_object($set) && is_a($set, '\booosta\base\Base')) $this->error = $set->error();
    else $this->error = $set;
  }

  public function after_instanciation() {}

  public function __get($var)
  {
    if(!empty($this->__get[$var])) return call_user_func([$this, $this->__get[$var]]);
    return null;
  }

  public function get_vars()
  {
    return get_object_vars($this);
  }
}
