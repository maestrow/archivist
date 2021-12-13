<?php

declare(strict_types=1);

define('DEBUG', true); // set to false in production environment
error_reporting(E_ALL);

require_once 'exceptions-handling.php';
require_once 'template.php';
require_once 'models/main.php';
require_once 'models/file.php';
require_once 'models/edit.php';
require_once 'actions.php';
require_once 'logic/fileperms.php';

$config = require_once 'config.php';

$req = new ReqParams($config);

session_start();


if (get_val($_POST, 'action') == 'login') {
  $login = get_val($_POST, 'login');
  $pass = get_val($_POST, 'password');
  login($login, $pass);
} else if (get_val($_GET, 'action') == 'logout') {
  logout();
} else {
  if (!isAuthorized()) {
    viewLogin();
  } else {
    if (array_key_exists('action', $_REQUEST)) {
      $action = $_REQUEST['action'];

      switch ($action) {
        case Actions::open:
          $path = $req->ensurePath(false);
          viewMain($path);
          break;

        case Actions::cut:
          $path = $req->ensurePath();
          cut($path);
          break;

        case Actions::copy:
          $path = $req->ensurePath();
          copyAction($path);
          break;

        case Actions::del:
          $path = $req->ensurePath();
          deleteItem($path);
          break;

        case Actions::paste:
          $path = $req->ensurePath();
          paste($path);
          break;

        case Actions::rename:
          $path = $req->ensurePath();
          $name = $req->ensureFileName();
          renameAction($path, $name);
          break;

        case Actions::save:
          $path = $req->ensurePath();
          $content = $_POST["content"];
          save($path, $content);
          break;

        case Actions::upload:
          $path = $req->ensurePath();
          upload($path);
          break;

        case Actions::createFile:
          $path = $req->ensurePath();
          $name = $req->ensureFileName();
          createFile($name, $path);
          break;

        case Actions::createFolder:
          $path = $req->ensurePath();
          $name = $req->ensureFileName();
          createFolder($name, $path);
          break;

        case Actions::script:
          $path = $req->ensurePath();
          $name = $req->ensureFileName();
          runScript($name, $path);
          break;

        default:
          throw new Exception("Unknown action: '{$action}'");
      }
    } else {
      $path = $req->ensurePath(false);
      viewMain($path);
    }
  }
}


// === Views


function viewLogin($errorMessage = "")
{
  global $config;
  $centralizeTpl = new Template('centralize.php');
  $layoutTpl = new Template('layout.php');
  $loginTpl = new Template('login.php');
  $loginTpl->title = $config['title'];
  $loginTpl->subtitle = $config['subtitle'];
  $loginTpl->errorMessage = $errorMessage;
  $centralizeTpl->content = $loginTpl->render();
  $layoutTpl->title = "Login";
  $layoutTpl->content = $centralizeTpl->render();

  echo $layoutTpl->render();
}

function viewMain($path)
{
  if (is_dir($path)) {
    viewDir($path);
  } else {
    viewEdit($path);
  }
}

function viewDir($path)
{
  global $config;
  $layoutTpl = new Template('layout.php');
  $mainTpl = new Template('main.php');

  $model = createMainModel($path);

  $mainTpl->title = $config['title'];
  $mainTpl->subtitle = $config['subtitle'];
  $mainTpl->model = $model;

  $layoutTpl->title = "files";
  $layoutTpl->content = $mainTpl->render();

  echo $layoutTpl->render();
}

function viewEdit($path)
{
  $layoutTpl = new Template('layout.php');
  $editTpl = new Template('edit.php');

  $model = createEditModel($path);

  $editTpl->model = $model;

  $layoutTpl->title = "edit";
  $layoutTpl->content = $editTpl->render();

  echo $layoutTpl->render();
}


// === Actions

function login($login, $pass)
{
  global $config, $req;

  $users = $config['users'];
  $success = isset($users[$login]) && password_verify($pass, $users[$login]);
  $_SESSION['auth'] = $success;

  if ($success) {
    viewDir($req->ensurePath(false));
  } else {
    viewLogin("Invalid username or password");
  }
}

function logout()
{
  unset($_SESSION['auth']);
  header("Location: index.php");
}

function save($path, $content)
{
  file_put_contents($path, $content);
  viewDir(dirname($path));
}

function runScript($name, $currentPath = "")
{
  global $config;
  $script = $config['scripts'][$name];
  $path = realpath_wrapper(join(DIRECTORY_SEPARATOR, [__DIR__, $script]));

  exec($path, $output, $result_code);

  echo join("\n", $output);
}

function createFile($name, $atPath)
{
  $path = join(DIRECTORY_SEPARATOR, [realpath_wrapper($atPath), basename($name)]);
  if (!file_exists($path)) {
    touch($path);
  }
  viewDir($atPath);
}

function createFolder($name, $atPath)
{
  $path = join(DIRECTORY_SEPARATOR, [realpath_wrapper($atPath), basename($name)]);
  mkdir($path);
  viewDir($atPath);
}

function deleteItem($path)
{
  if (file_exists($path)) {
    if (is_dir($path)) {
      if (is_dir_empty($path)) {
        rmdir($path);
      }
    } else {
      unlink($path);
    }
  }
  viewDir(dirname($path));
}

function upload($path)
{
  $uploadfile = join(DIRECTORY_SEPARATOR, [realpath_wrapper($path), basename($_FILES['userfile']['name'])]);

  if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    viewDir($path);
  } else {
    echo "Possible file upload attack!\n";
  }
}

function cut($path)
{
  if (!is_file($path)) {
    throw new Exception("cut operation allowed only on files");
  }
  $_SESSION['buffer'] = $path;
  $_SESSION['last_buffer_action'] = Actions::cut;
  viewDir(dirname($path));
}

function copyAction($path)
{
  if (!is_file($path)) {
    throw new Exception("copy operation allowed only on files");
  }
  $_SESSION['buffer'] = $path;
  $_SESSION['last_buffer_action'] = Actions::copy;
  viewDir(dirname($path));
}

function paste($path)
{
  if (isset($_SESSION['buffer'])) {
    if (!isset($_SESSION['last_buffer_action'])) {
      throw new Exception("last_buffer_action is not set");
    }

    $newname = basename($_SESSION['buffer']);
    if (dirname($_SESSION['buffer']) === $path) {
      $newname .= '_';
    }
    $target = join(DIRECTORY_SEPARATOR, [$path, $newname]);

    if ($_SESSION['last_buffer_action'] === Actions::cut) {
      rename($_SESSION['buffer'], $target);
    } else if ($_SESSION['last_buffer_action'] === Actions::copy) {
      copy($_SESSION['buffer'], $target);
    } else {
      throw new Exception("Unknown last_buffer_action: '{$_SESSION['last_buffer_action']}'");
    }
  }
  unset($_SESSION['buffer']);
  unset($_SESSION['last_buffer_action']);
  viewDir($path);
}

function renameAction($path, $newname)
{
  //echo "{$path}, {$newname}";
  $dir = dirname($path);
  rename($path, join(DIRECTORY_SEPARATOR, [$dir, $newname]));
  viewDir($dir);
}

// === Model Factories

function createMainModel($path)
{
  global $config;
  $model = new MainModel();
  $files = getDir($path);
  $model->path = $path;
  $model->files = $files;
  $model->scripts = array_keys($config['scripts']);
  if (isset($_SESSION['last_buffer_action'])) {
    $model->buffer = $_SESSION['buffer'];
    $model->bufferOp = $_SESSION['last_buffer_action'];
  }
  return $model;
}

function createEditModel($path)
{
  $model = new EditModel();
  $model->path = $path;
  $model->content = file_get_contents($path);
  return $model;
}

function getDir(string $path): array
{
  $result = [];
  $files = scandir($path, 0) ?? [];

  foreach ($files as $file) {
    if ($file == '.') {
      continue;
    }
    $f = new FileModel();
    $f->path = join(DIRECTORY_SEPARATOR, [$path, $file]);
    $f->isDir = is_dir($f->path);
    if ($f->isDir) {
      $f->isDirEmpty = is_dir_empty($f->path);
    }
    $f->mode = FilePerms::getFullPerms($f->path);
    $f->size = filesize($f->path);
    array_push($result, $f);
  }

  usort($result, function ($a, $b) {
    /** 
     * @var FileModel $a 
     * @var FileModel $b
     */
    if ($a->isDir === $b->isDir) {
      return ($a < $b) ? -1 : 1;
    }
    return ($a->isDir) ? -1 : 1;
  });

  return $result;
}


// === Helpers

function isAuthorized()
{
  return get_val($_SESSION, 'auth') === true;
}

function get_val($arr, $key)
{
  if (array_key_exists($key, $arr)) {
    return $arr[$key];
  }
  return null;
}

function is_dir_empty($dir)
{
  if (!is_readable($dir)) return null;
  return (count(scandir($dir)) == 2);
}

class ReqParams
{
  static private function startsWith($haystack, $needle, $case = true)
  {
    if ($case) {
      return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
  }

  private array $config;

  public function __construct(array $config)
  {
    $this->config = $config;
  }

  public function ensurePath(bool $required = true): string
  {
    if (!isset($_REQUEST['path'])) {
      if ($required) {
        throw new Exception("path param required");
      }
      return realpath_wrapper($this->config['root']);
    }
    $path = realpath_wrapper($_REQUEST['path']);

    if ($path === false || !self::startsWith($path, realpath_wrapper($this->config['root']))) {
      throw new Exception("path param is invalid");
    }

    return $path;
  }

  public function ensureFileName(): string
  {
    if (!isset($_REQUEST['name'])) {
      throw new Exception("name param required");
    }
    return basename($_REQUEST['name']);
  }
}

function realpath_wrapper($path): string
{
  global $config;
  if ($config['allow-symlinks']) {
    return trim(shell_exec("realpath -s {$path}"));
  } else {
    return realpath($path);
  }
}
