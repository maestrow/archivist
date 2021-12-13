<?php declare(strict_types = 1);

class FileModel {
  public string $path;
  public int $size;
  public string $mode;
  public bool $isDir;
  public ?bool $isDirEmpty = null;

  public function getName(): string {
    return basename($this->path);
  }
}

?>