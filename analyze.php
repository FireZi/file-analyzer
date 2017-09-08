<?php

define('PATH_DIR', __DIR__ . '/' . $argv[1] . '/');

require_once 'parser.php';
require_once 'caching.php';
require_once 'differences.php';
require_once 'empty_values.php';

$dir_source_names = scandir(PATH_DIR);
$new_modification_times = [];

foreach ($dir_source_names as $source_name) {
  if (preg_match("/.php/", $source_name)) {
    $new_modification_times[$source_name] = filemtime(PATH_DIR . $source_name);
  }
}

$old_modification_times = get_cached_modification_times();
$new_parsed_files = [];

foreach ($new_modification_times as $file_name => $file_time) {
  if (!array_key_exists($file_name, $old_modification_times)
      || $file_time != $old_modification_times[$file_name]) {

    $old_file = get_cached_source($file_name);
    $new_parsed_files[$file_name] = parse_code(PATH_DIR, $file_name);
    $new_file = $new_parsed_files[$file_name];
    analyze_file_diffs($new_file, $old_file);
  }
}
foreach ($old_modification_times as $file_name => $file_time) {
  if (!array_key_exists($file_name, $new_modification_times)) {
    $new_file = $empty_file;
    $old_file = get_cached_source($file_name);
    analyze_file_diffs($new_file, $old_file);
    delete_cached_source($file_name);
  }
}
destroy_deleted();

$req_file_name = explode('/', $argv[2]);
$req_file_name = $req_file_name[count($req_file_name) - 1];
gen_short_info();
$request_file = null;
if (array_key_exists($req_file_name, $new_parsed_files)) {
  $request_file = $new_parsed_files[$req_file_name];
} else {
  $request_file = get_cached_source($req_file_name);
}
foreach ($request_file['functions'] as $fname => $func) {
  if (!$funcs_short_info[$fname]['is_called']) {
    print('unused function ' . $fname . ' on line ' . $funcs_short_info[$fname]['line'] . "\n");
  }
}

save_cached_modification_times($new_modification_times);
foreach ($new_parsed_files as $file) {
  save_cached_source($file);
}
if ($is_initialized) {
  save_cached_graph();
  save_cached_info();
}