<?php

define('PATH_C', PATH_DIR . '.cache/');
define('PATH_C_MT', PATH_C . 'modification_times');
define('PATH_C_S', PATH_C . 'sources/');
define('PATH_C_G', PATH_C . 'graph');
define('PATH_C_I', PATH_C . 'functions_info');

require_once 'analyze.php';
require_once 'empty_values.php';

function get_cached_modification_times()
{
  if (!file_exists(PATH_C)) {
    mkdir(PATH_C);
    return array();
  }
  if (!file_exists(PATH_C_MT)) {
    return array();
  }

  return unserialize(file_get_contents(PATH_C_MT));
}


function save_cached_modification_times($new_modification_time)
{
  file_put_contents(PATH_C_MT, serialize($new_modification_time));
}


function get_cached_source($source_name)
{
  global $empty_file;
  $file = $empty_file;
  $file['name'] = $source_name;

  if (!file_exists(PATH_C_S)) {
    mkdir(PATH_C_S);
    return $file;
  }

  if (!file_exists(PATH_C_S . $source_name)) {
    return $file;
  }

  return unserialize(file_get_contents(PATH_C_S . $source_name));
}


function delete_cached_source($source_name)
{
  unlink(PATH_C_S . $source_name);
}


function save_cached_source($source)
{
  file_put_contents(PATH_C_S . $source['name'], serialize($source));
}


function get_cached_graph()
{
  if (!file_exists(PATH_C_G)) {
    return array();
  }

  return unserialize(file_get_contents(PATH_C_G));
}


function save_cached_graph()
{
  global $graph;
  file_put_contents(PATH_C_G, serialize($graph));
}

function is_info_cached()
{
  if (file_exists(PATH_C_I)) {
    return true;
  }
  return false;
}

function get_cached_info()
{
  return unserialize(file_get_contents(PATH_C_I));
}

function save_cached_info()
{
  global $funcs_short_info;
  file_put_contents(PATH_C_I, serialize($funcs_short_info));
}