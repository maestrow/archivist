<?php

declare(strict_types=1);
require_once __DIR__ . '/helpers/classnames.php';
require_once __DIR__ . '/helpers/url.php';

function humanFileSize($size, $unit = "")
{
  if ((!$unit && $size >= 1 << 30) || $unit == "g")
    return number_format($size / (1 << 30), 1) . " g";
  if ((!$unit && $size >= 1 << 20) || $unit == "m")
    return number_format($size / (1 << 20), 1) . " m";
  if ((!$unit && $size >= 1 << 10) || $unit == "k")
    return number_format($size / (1 << 10), 1) . " k";
  return number_format($size);
}

$url = new UrlHelper();

/** @var MainModel $model model */
$m = $model;

?>
<div class="main">
  <div class="main__header">
    <a class="main__logo-link" href="." title="На главную">
      <img class="main__logo" src="images/logo.svg" alt="лого" />
      <div>
        <div class="main__title"><?php echo $title ?></div>
        <div class="main__subtitle"><?php echo $subtitle ?></div>
      </div>
    </a>
    <div class="main__header-right">
      <div class="main__info">
        <div class="main__label">path:</div>
        <div><?php echo $m->path ?></div>
        <a class="main__action-link main__act-logout" href="<?php echo $url->logout() ?>">
          logout
          <img class="main__icon" src="images/logout.svg" alt="" />
        </a>
      </div>
      <div class="main__info">
        <div class="main__label">buffer:</div>
        <div><?php echo "{$m->bufferOp} {$m->buffer}" ?></div>
      </div>
      <div class="main__actions">


        <?php if ($m->buffer !== null) { ?>
          <a class="main__action-link" href="<?php echo $url->paste($m->path) ?>">
            <img class="main__icon" src="images/paste.svg" alt="" />
            Paste
          </a>
        <?php } ?>

        <a class="main__action-link" href="#" id="create-file-link" data-path="<?php echo $m->path ?>">
          <img class="main__icon" src="images/create_file.svg" alt="" />
          New File
        </a>
        <a class="main__action-link" href="#" id="create-folder-link" data-path="<?php echo $m->path ?>">
          <img class="main__icon" src="images/create_folder.svg" alt="" />  
          New Folder
        </a>

        <!-- The data encoding type, enctype, MUST be specified as below. 
        https://www.php.net/manual/en/features.file-upload.post-method.php -->
        <form class="main__upload-form main__action-link" enctype="multipart/form-data" method="POST">
          <input type="hidden" name="action" value="upload" />
          <input type="hidden" name="path" value="<?php echo $m->path ?>" />
          <!-- MAX_FILE_SIZE must precede the file input field -->
          <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
          <!-- Name of input element determines name in $_FILES array -->
          <?php require 'upload-button.php'; ?>
        </form>
      </div>

      <div class="main__scripts">
        <?php foreach ($m->scripts as $value) { ?>
          [<a href="<?php echo $url->script($value, $m->path) ?>"><?php echo $value ?></a>]
        <?php } ?>
      </div>
    </div>



  </div>
  <table class="main__table">
    <thead>
      <tr>
        <th></th>
        <th>Name</th>
        <th>Size</th>
        <th>Permissions</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php
      /** @var FileModel $file file */
      foreach ($m->files as $file) { ?>
        <tr class="<?php echo Css::combine(["item" => true, "item--dir" => $file->isDir]) ?>">
          <td class="item__col-primary-actions">
            <a 
              class="<?php echo Css::combine(['item--hidden' => $file->isDir]) ?>"
              href="<?php echo $url->file('cut', $file->path) ?>"
            >
              <img src="images/cut.svg" alt="cut" title="cut" />
            </a>
            <a 
              class="<?php echo Css::combine(['item--hidden' => $file->isDir]) ?>"
              href="<?php echo $url->file('copy', $file->path) ?>"
            >
              <img src="images/copy.svg" alt="copy" title="copy" />
            </a>
            <a 
              class="<?php echo Css::combine([
                'item--hidden' => $file->getName() === '..', 
                'item__act-rename' => true
              ]) ?>"
              data-path="<?php echo $file->path ?>" 
              data-name="<?php echo $file->getName() ?>" 
              href="#"
            >
              <img src="images/rename.svg" alt="rename" title="rename" />
            </a>
          </td>
          <td class="item__col-name">
            <a href="<?php echo $url->file('open', $file->path) ?>"><?php echo $file->getName() ?></a>
          </td>
          <td class="item__col-size">
            <?php echo !$file->isDir ? humanFileSize($file->size) : "" ?>
          </td>
          <td class="item__col-mode">
            <?php echo $file->mode ?>
          </td>
          <td class="item__secondary-actions">
            <a 
              class="<?php echo Css::combine([
                'item__act-del' => true,
                'item--hidden' => $file->isDir && !$file->isDirEmpty
              ]) ?>"
              href="#"
              data-path="<?php echo $file->path ?>" 
              data-name="<?php echo $file->getName() ?>" 
            >
              <img src="images/delete.svg" alt="delete" title="delete" />
            </a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <script>
    (function() {
      const getUrl = function(data) {
        return Object.entries(data).map(function([k, v]) {
          return encodeURIComponent(k) + '=' + encodeURIComponent(v)
        }).join('&');
      }

      const onRenameClick = function(e) {
        e.preventDefault();
        var newname = prompt("Enter new name", e.currentTarget.dataset.name);
        if (newname && newname != e.currentTarget.dataset.name) {
          url = getUrl({
            'action': 'rename',
            'path': e.currentTarget.dataset.path,
            'name': newname
          });
          //console.log(url);
          location.search = url;
        }
      };

      const onDeleteClick = function (e) {
        e.preventDefault();
        if (confirm(`Delete "${e.currentTarget.dataset.name}" ?`)) {
          var url = getUrl({
            'action': 'del',
            'path': e.currentTarget.dataset.path
          });
          location.search = url;
        }
      }

      const createHandlerFactory = (action, promptMsg) => (e) => {
        e.preventDefault();
        var name = prompt(promptMsg);
        if (name) {
          url = getUrl({
            'action': action,
            'path': e.currentTarget.dataset.path,
            'name': name
          });
          location.search = url;
        }
      }

      const subscribe = function (cls, event, handler) {
        var sel = document.getElementsByClassName(cls);
        for (var i = 0; i < sel.length; i++) {
          sel[i].addEventListener(event, handler);
        }
      }

      subscribe('item__act-rename', 'click', onRenameClick);
      subscribe('item__act-del', 'click', onDeleteClick);

      document
        .getElementById('create-folder-link')
        .addEventListener('click', createHandlerFactory('createFolder', 'Enter folder name'));

      document
        .getElementById('create-file-link')
        .addEventListener('click', createHandlerFactory('createFile', 'Enter file name'));

    })();
  </script>
</div>