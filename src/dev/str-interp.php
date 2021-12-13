<?php 
abstract class BaseActions {
  public static function all () {
    $reflection = new ReflectionClass(self::class); 
    return $reflection->getConstants();
  }

  public static function exists($action): bool {
    return array_key_exists($action, self::all());
  }

  public static function ensureExists($action): bool {
    if (self::exists($action)) {
      return true;
    }
    throw new Exception("Unexpected action '{$action}'");
  }
}

class FileActions extends BaseActions {
  public const open = 'open';
  public const del = 'del';
  public const copy = 'copy';
}

class MainActions extends BaseActions {
  public const login = 'login';
  public const logout = 'logout';
  public const paste = 'paste';
  public const save = 'save';
  public const upload = 'upload';
  public const script = 'script';
}

MainActions::ensureExists('sdasad');

?>
