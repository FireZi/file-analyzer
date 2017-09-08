<?php

require_once 'empty_values.php';

class BraceEnumTypes
{
  const FUNCTION_BRACE = 1;
  const ANOTHER_BRACE = 2;
}
const F_INIT = 346;
const F_NAME = 319;


function parse_code($path, $source_name)
{
  global $empty_file;
  global $empty_func;
  $source_path = $path . $source_name;
  if (!file_exists($source_path)) {
    return $empty_file;
  }

  $functions = [];
  $calls = [];
  $function_stack = [];
  $braces_stack = [];

  $source = token_get_all(file_get_contents($source_path));

  for ($i = 0; $i < count($source); $i++) {
    $token = $source[$i];
    if (is_string($token)) {
      if ($token == '{') {
        array_push($braces_stack, BraceEnumTypes::ANOTHER_BRACE);
      }
      if ($token == '}') {
        if ($braces_stack[count($braces_stack) - 1] == BraceEnumTypes::FUNCTION_BRACE) {
          array_pop($function_stack);
        }
        array_pop($braces_stack);
      }
    } else {
      list($id, $text, $line) = $token;
      if ($id == F_NAME) {
        if (empty($function_stack)) {
          $calls[$text] = $text;
        } else {
          $cur_func = $function_stack[count($function_stack) - 1];
          if ($cur_func != $text) {
            $functions[$cur_func]['calls'][$text] = $text;
          }
        }
      }
      if ($id == F_INIT) {
        while (is_string($source[$i]) || $source[$i][0] != F_NAME) {
          $i++;
        }
        list($id, $text, $line) = $source[$i];
        $functions[$text] = $empty_func;
        $functions[$text]['name'] = $text;
        $functions[$text]['line'] = $line;
        array_push($function_stack, $text);
        while (!is_string($source[$i]) || $source[$i] != '{') {
          $i++;
        }
        array_push($braces_stack, BraceEnumTypes::FUNCTION_BRACE);
      }
    }
  }

  $file_object = $empty_file;
  $file_object['name'] = $source_name;
  $file_object['functions'] = $functions;
  $file_object['calls'] = $calls;
  return $file_object;
}