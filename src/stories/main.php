<?php declare(strict_types = 1);

require '../template.php';

$files = [
  [ 'name' => "wef", 'mode' => "777" ],
  [ 'name' => "wefwef", 'mode' => "777" ],
  [ 'name' => "wefwef", 'mode' => "777" ],
  [ 'name' => "vdfvzsdv", 'mode' => "666" ],
  [ 'name' => "baera", 'mode' => "777" ],
  [ 'name' => "ergaerg", 'mode' => "454" ],
  [ 'name' => "dfbzdfzb", 'mode' => "777" ],
];

$layoutTpl = new Template('layout.php');
$mainTpl = new Template('main.php');

$mainTpl->path = "oweij foiwej fowij f";
$mainTpl->files = $files;

$layoutTpl->title = "title title";
$layoutTpl->content = $mainTpl->render();

echo $layoutTpl->render();

?>