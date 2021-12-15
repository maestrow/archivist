<?php declare(strict_types = 1); 
  // combine all CSS files
  // Inspired by https://wp-mix.com/combine-all-css-files-php/

  $filterFn = fn($i) => stringEndsWith($i, '.css');
  
  $path = __DIR__;
  $files = array_filter(scanDirRec($path), $filterFn);
  
  header('Content-type: text/css');

	foreach($files as $file) {
    $fullPath = join(DIRECTORY_SEPARATOR, [$path, $file]);
    if (isset($_GET['list'])) { //ToDo: and environment = development
      echo $file, "\n";
    } else {
      include_once($fullPath);
      echo "\n\n";
    }
	} 

  //=== Functoins

  function getFullPath($path) {
    return realpath(join(DIRECTORY_SEPARATOR, [__DIR__, $path]));
  }

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
?>
