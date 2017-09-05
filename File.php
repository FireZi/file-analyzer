<?php

class File
{
  public $name;
  public $functions = [];
  public $calls = [];

  public function __construct($functions, $calls)
  {
    $this->functions = $functions;
    $this->calls = $calls;
  }
}