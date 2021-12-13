<?php declare(strict_types = 1);
class Template {
  private $vars = array();
  public $view_template_file;

  public function __construct($view_template_file) {
    $this->view_template_file = $view_template_file;
  }

  public function __get($name) {
    return $this->vars[$name];
  }

  public function __set($name, $value) {
    if($name == 'view_template_file') {
      throw new Exception("Cannot bind variable named 'view_template_file'");
    }
    $this->vars[$name] = $value;
  }

  public function render() {
    if(array_key_exists('view_template_file', $this->vars)) {
      throw new Exception("Cannot bind variable called 'view_template_file'");
    }
    extract($this->vars);
    ob_start();
    include(__DIR__ . '/views/' . $this->view_template_file);
    return ob_get_clean();
  }
}
?>