<?php
require_once 'File.php';
require_once 'Func.php';

class BraceEnumTypes {
  const FUNCTION_BRACE = 1;
  const ANOTHER_BRACE = 2;
}
const F_INIT = 346;
const F_NAME = 319;


function parse_code($source) {
  $functions = [];
  $calls = [];
  $functionsStack = [];
  $bracesStack = [];

  for ($i = 0; $i < count($source); $i++) {
    $token = $source[$i];
    if (is_string($token)) {
      if ($token == '{') {
        array_push($bracesStack, BraceEnumTypes::ANOTHER_BRACE);
      }
      if ($token == '}') {
        if ($bracesStack[count($bracesStack) - 1] == BraceEnumTypes::FUNCTION_BRACE) {
          array_pop($functionsStack);
        }
        array_pop($bracesStack);
      }
    }
    else {
      list($id, $text, $line) = $token;
      if ($id == F_NAME) {
        if (empty($functionsStack)) {
          array_push($calls, $text);
        } else {
          $curFunc = $functionsStack[count($functionsStack) - 1];
          array_push($functions[$curFunc]->calls, array($text, 0));
        }
      }
      if ($id == F_INIT) {
        while (is_string($source[$i]) || $source[$i][0] != F_NAME) {
          $i++;
        }
        list($id, $text, $line) = $source[$i];
        $functions[$text] = new Func($text, $line);
        array_push($functionsStack, $text);
        while (!is_string($source[$i]) || $source[$i] != '{') {
          $i++;
        }
        array_push($bracesStack, BraceEnumTypes::FUNCTION_BRACE);
      }
    }
  }

  return new File($functions, $calls);
}