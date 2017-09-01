<?php
require_once 'parser.php';

$source = file_get_contents($argv[1]);
$source = token_get_all($source);
//var_export(parse_code($source));
print_r(parse_code($source));
