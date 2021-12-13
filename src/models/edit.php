<?php declare(strict_types = 1);

class EditModel {
  public string $path;
  public string $content;

  public function getFileName(): string {
    return basename($this->path);
  }

  public function getDirPath(): string {
    return dirname($this->path);
  }
}

?>