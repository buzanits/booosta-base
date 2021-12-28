<?php
namespace booosta\base;
\booosta\Framework::init_module('module');

abstract class Module extends Base
{
  protected $modulename;
  protected $moduleinfo;

  public function __construct()
  {
    parent::__construct();
    if($this->modulename === null) $this->modulename = get_class($this);
    if($this->moduleinfo === null) $this->moduleinfo = [];
  }
}
