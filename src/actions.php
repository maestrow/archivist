<?php declare(strict_types = 1);

class Actions {

  // Public helpers
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


  // File Actions
  public const open = 'open';
  public const edit = 'edit';
  public const del = 'del';
  public const cut = 'cut';
  public const copy = 'copy';
  public const rename = 'rename';

  // Main Actions
  public const login = 'login';
  public const logout = 'logout';
  public const paste = 'paste';
  public const save = 'save';
  public const upload = 'upload';
  public const script = 'script';
  public const createFile = 'createFile';
  public const createFolder = 'createFolder';
}

?>