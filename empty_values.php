<?php

$empty_file = [
  'name' => null,
  'functions' => [],
  'calls' => [],
];

$empty_func = [
  'name' => null,
  'line' => null,
  'called_count' => 0,
  'called_edges_count' => 0,
  'is_called' => 0,
  'calls' => [],
  'is_moved' => false,
  'is_deleted' => false,
];