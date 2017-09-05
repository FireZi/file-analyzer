<?php
require_once 'Func.php';
require_once 'File.php';
require_once 'caching.php';


$graph = [];
$funcs_short_info = [];
$is_initialized = false;

function init()
{
  global $is_initialized;
  global $graph;

  if (!$is_initialized) {
    $is_initialized = true;
    $graph = get_cached_graph();
  }
}

function vertex_add(Func $func)
{
  global $graph;
  if (!array_key_exists($func->name, $graph)) {
    $graph[$func->name] = $func;
  } else {
    foreach ($graph[$func->name]->calls as $call) {
      edge_delete($func, $call);
    }
    $graph[$func->name]->merge($func);
    $graph[$func->name]->is_deleted = false;
    foreach ($graph[$func->name]->calls as $call) {
      edge_add($func, $call);
    }
  }
}

function edge_add(Func $func, $to)
{
  global $graph;
  $fname = $func->name;

  if (!array_key_exists($to, $graph)) {
    $graph[$to] = new Func($to, null);
  }
  $graph[$fname]->calls[$to] = $to;

  if ($graph[$fname]->is_called) {
    $graph[$to]->called_edges_count++;
    if (!$graph[$to]->is_called) {
      dfs_on($to);
    }
  }
}

function call_add($call)
{
  global $graph;

  if (!array_key_exists($call, $graph)) {
    $graph[$call] = new Func($call, null);
  }
  $graph[$call]->called_count++;

  if (!$graph[$call]->is_called) {
    dfs_on($call);
  }
}

function dfs_on($fname)
{
  global $graph;

  $graph[$fname]->is_called = true;
  foreach ($graph[$fname]->calls as $call) {
    $graph[$call]->called_edges_count++;
    if (!$graph[$call]->is_called) {
      dfs_on($call);
    }
  }
}

function edge_delete(Func $func, $to)
{
  global $graph;
  $from = $func->name;

  if ($func->is_called) {
    dfs_off($to);
  }
  unset($graph[$from]->calls[$to]);
}

function vertex_delete($fname)
{
  global $graph;
  if (!$graph[$fname]->is_merged) {
    foreach ($graph[$fname]->calls as $call) {
      edge_delete($graph[$fname], $call);
    }
    $graph[$fname]->is_deleted = true;
  }
}

function call_delete($fname)
{
  global $graph;

  $graph[$fname]->called_count--;
  if (!$graph[$fname]->called_count && !$graph[$fname]->called_edges_count) {
    $graph[$fname]->is_called = false;
    foreach ($graph[$fname]->calls as $call) {
      dfs_off($call);
    }
  }
}

function dfs_off($fname)
{
  global $graph;
  $graph[$fname]->called_edges_count--;
  if (!$graph[$fname]->called_edges_count && !$graph[$fname]->called_count) {
    $graph[$fname]->is_called = false;
    foreach ($graph[$fname]->calls as $call) {
      dfs_off($call);
    }
  }
}

function update_line($fname, $line)
{
  global $graph;
  $graph[$fname]->line = $line;
}

function destroy_deleted()
{
  global $graph;

  foreach ($graph as $func) {
    if ($func->is_deleted) {
      unset($graph[$func->name]);
    } else {
      $graph[$func->name]->is_merged = false;
    }
  }
}

function gen_short_info()
{
  global $graph;
  global $is_initialized;
  global $funcs_short_info;

  if ($is_initialized || !is_info_cached()) {
    if (!$is_initialized) {
      $is_initialized = true;
      $graph = get_cached_graph();
    }
    foreach ($graph as $fname => $func) {
      $funcs_short_info[$fname] =
        array($graph[$fname]->is_called, $graph[$fname]->line);
    }
  } else {
    $funcs_short_info = get_cached_info();
  }
}