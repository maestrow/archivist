<?php declare(strict_types = 1);

class Css {
  public static function combine(array $data) {
    $classes = [];
    foreach ($data as $key => $value) {
      if (gettype($value) === 'boolean') {
        if ($value === true) {
          array_push($classes, $key);
        }
      } else {
        array_push($classes, $value);
      }
    }

    return join(" ", $classes);
  }
}

?>