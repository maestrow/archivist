<?php declare(strict_types = 1);

require_once '../template.php';

$centralizeTpl = new Template('centralize.php');
$layoutTpl = new Template('layout.php');
$loginTpl = new Template('login.php');

$loginTpl->errorMessage = "wefwef";
$centralizeTpl->content = $loginTpl->render();
$layoutTpl->title = "title title";
$layoutTpl->content = $centralizeTpl->render();

echo $layoutTpl->render();

?>