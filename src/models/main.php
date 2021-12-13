<?php declare(strict_types = 1);

class MainModel {
  public string $path;
  public array $files = [];
  public array $scripts = [];
  public ?string $buffer = null;
  public ?string $bufferOp = null;
}

?>