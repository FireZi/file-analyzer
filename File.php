<?php

class File {
  public $fileName;
  public $lastChange;
  public $functions = [];
  public $calls = [];

  public function __construct($functions, $calls) {
    $this->functions = $functions;
    $this->calls = $calls;
  }
}