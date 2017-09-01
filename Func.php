<?php

class Func{
  public $funcName;
  public $line;
  public $calls = [];

  public function __construct($funcName, $line) {
    $this->funcName = $funcName;
    $this->line = $line;
  }
}