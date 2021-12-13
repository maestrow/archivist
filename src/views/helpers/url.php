<?php declare(strict_types = 1);

require_once __DIR__ . '/../../actions.php';

class UrlHelper {
  private function build_action_url(array $params): string {
    return "index.php?" . http_build_query($params);
  }

  private function byAction (string $action, array $params = []): string {
    return $this->build_action_url(array_merge(['action' => $action], $params));
  }


  //=== Public

  public function paste($path): string {
    return $this->byAction(Actions::paste, ['path' => $path]);
  }

  public function logout(): string {
    return $this->byAction(Actions::logout);
  }

  public function script(string $script, string $currentPath): string {
    return $this->build_action_url([
      'action' => 'script',
      'path' => $currentPath,
      'name' => $script
    ]);
  }

  public function file(string $action, string $path): string {
    return $this->build_action_url([
      'action' => $action,
      'path' => $path
    ]);
  }

}

return new UrlHelper();

?>