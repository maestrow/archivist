<?php

function stringEndsWith($haystack,$needle,$case=true) {
  $expectedPosition = strlen($haystack) - strlen($needle);
  if ($case){
      return strrpos($haystack, $needle, 0) === $expectedPosition;
  }
  return strripos($haystack, $needle, 0) === $expectedPosition;
}

function scanDirRec($target) {
  $files = scandir($target, 0) ?? [];
  foreach ($files as $file) {
    if (is_dir($target . DIRECTORY_SEPARATOR . $file) && $file !== '.' && $file !== '..') {
      $subs = scanDirRec($target . DIRECTORY_SEPARATOR . $file);
      foreach ($subs as $sub) {
        array_push($files, $file . DIRECTORY_SEPARATOR . $sub);
      }
    }
  }
  return $files;
}

$arr = array_filter(scanDirRec('..'), fn($i) => stringEndsWith($i, '.css'));

print_r($arr);

?>
