<?php

declare(strict_types=1);
require_once __DIR__ . '/helpers/classnames.php';
require_once __DIR__ . '/helpers/url.php';
require_once __DIR__ . '/../actions.php';
$url = new UrlHelper();

/** @var EditModel $model model */
$m = $model;

?>
<form class="edit" method="POST">
  <input type="hidden" name="path" value="<?php echo $m->path ?>"/>
  <div class="edit__header">
    <div class="edit__path">
      <div class="edit__path-label">path:</div>
      <div><?php echo $m->path ?></div>
    </div>

    <div class="edit__actions">
      <button type="submit" name="action" value="<?php echo Actions::save ?>">Save</button>
      <a href="<?php echo $url->file('open', $m->getDirPath()) ?>">Back</a>
    </div>
  </div>
  <div class="edit__body">

    <textarea class="edit__textarea" name="content"><?php echo htmlspecialchars($m->content) ?></textarea>

  </div>
</form>