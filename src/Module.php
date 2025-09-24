<?php
namespace booosta\base;
\booosta\Framework::init_module('module');

abstract class Module extends Base
{
  protected $modulename;
  protected $moduleinfo;
  protected $needs_jquery = false;

  public function __construct()
  {
    parent::__construct();
    if($this->modulename === null) $this->modulename = get_class($this);
    if($this->moduleinfo === null) $this->moduleinfo = [];
  }

  public function loadHTML()
  {
    $c = explode('\\', get_class($this));
    $class = strtolower(array_pop($c));
    \booosta\Framework::$wrapperclass = $class;

    $module = strtolower(array_pop($c));
    \booosta\Framework::$wrappermodule = $module;
    #\booosta\Framework::debug(get_class($this)); \booosta\Framework::debug($class); \booosta\Framework::debug($module);

    require 'vendor/booosta/base/src/Wrapper.php';
    $wrapper = new \booosta\base\Wrapper();
    $wrapper->exec();

    $result = '';

    if($this->needs_jquery === true) $result .= "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js'></script>";
    elseif($this->needs_jquery) $result .= "<script src='https://ajax.googleapis.com/ajax/libs/jquery/$this->needs_jquery/jquery.min.js'></script>";
    
    $result .= $wrapper->get_includes();
    if(is_callable([$this, 'get_js'])) $result .= '<script>' . $this->get_js() . '</script>';
    if(is_callable([$this, 'get_htmlonly'])) $result .= $this->get_htmlonly();

    return $result;
  }
}

