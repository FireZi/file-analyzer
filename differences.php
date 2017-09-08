<?php
require_once 'structure.php';

function analyze_file_diffs($new_file, $old_file)
{
  init();
  foreach ($new_file['functions'] as $name => $func) {
    if (!array_key_exists($name, $old_file['functions'])) {
      vertex_add($func);
    } else {
      analyze_func_diffs($func, $old_file['functions'][$name]);
    }
  }
  foreach ($new_file['calls'] as $call) {
    if (!array_key_exists($call, $old_file['calls'])) {
      call_add($call);
    }
  }

  foreach ($old_file['functions'] as $name => $func) {
    if (!array_key_exists($name, $new_file['functions'])) {
      vertex_delete($name);
    }
  }

  foreach ($old_file['calls'] as $call) {
    if (!array_key_exists($call, $new_file['calls'])) {
      call_delete($call);
    }
  }

  foreach ($new_file['functions'] as $func) {
    update_line($func['name'], $func['line']);
  }
}


function analyze_func_diffs($new_func, $old_func)
{
  foreach ($new_func['calls'] as $call) {
    if (!array_key_exists($call, $old_func['calls'])) {
      edge_add($new_func, $call);
    }
  }

  foreach ($old_func['calls'] as $call) {
    if (!array_key_exists($call, $new_func['calls'])) {
      edge_delete($old_func, $call);
    }
  }
}