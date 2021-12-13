<?php declare(strict_types = 1);

class FilePerms {

  private const type = [
    0xC000 => 's', // socket
    0xA000 => 'l', // symbolic link
    0x8000 => 'r', // regular
    0x6000 => 'b', // block special
    0x4000 => 'd', // directory
    0x2000 => 'c', // character special
    0x1000 => 'p', // FIFO pipe
  ];

  private static function getType(int $perms): string {
    $key = $perms & 0xF000;
    if (key_exists($key, self::type)) {
      return self::type[$key];
    }
    return 'u'; // unknown
  }

  public static function getFullPerms(string $path): string {
    $perms = fileperms($path);
    
    $info = self::getType($perms);

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
                (($perms & 0x0800) ? 's' : 'x' ) :
                (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
                (($perms & 0x0400) ? 's' : 'x' ) :
                (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
                (($perms & 0x0200) ? 't' : 'x' ) :
                (($perms & 0x0200) ? 'T' : '-'));

    return $info;
  }
}

?>