<?php

class Func
{
  public $name;
  public $line;
  public $called_count = 0;
  public $called_edges_count = 0;
  public $is_called = false;
  public $is_deleted = false;
  public $is_merged = false;
  public $calls = [];

  public function __construct($name, $line)
  {
    $this->name = $name;
    $this->line = $line;
  }

  public function merge(Func $func_inf)
  {
    $this->is_merged = true;
    $this->line = $func_inf->line;
    $this->calls = $func_inf->calls;
  }
}