<?php

abstract class BaseActions {
  public static function all () {
    $reflection = new ReflectionClass(self::class); 
    return $reflection->getConstants();
  }
}

class FileActions extends BaseActions {
  public const open = 'open';
  public const del = 'del';
  public const copy = 'copy';
}

FileActions::all();

?>