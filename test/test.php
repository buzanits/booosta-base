<?php
require_once __DIR__ . '/../vendor/autoload.php';

use booosta\Framework as b;
b::load();

class Test1 extends booosta\base\Module 
{
  public function blub() { print $this->config('site_name') . PHP_EOL; }
}

$a = new Test1();
$a->blub();
#$a->bla();

#b::debug(111);
