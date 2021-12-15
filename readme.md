# archivist - Zero-dependency PHP file manager

## Development

Features:
- Development inside docker container using VSCode and Remote-Containers extension. This repository is taken as a basis: https://github.com/microsoft/vscode-remote-try-php
- Debugging
- Apache web server: Type `apache2ctl start` insde container's terminal (just open terminal in VSCode - it'll be opened inside container)
- after start: go to http://localhost:8080/admin/. Use admin:admin account.

### CLI

Use `code-remote-container` script to open  VSCode with folder in container.

```bash
make attach   # open bash in container
make repl     # run php repl in container
```

in container:
```
apache2ctl start
```


### Debugging features

There three debugging configurations:
- Application
- Current script
- Attach debugger to web server

Debugging features provided with `felixfbecker.php-debug` extension (see `.devcontainer/devcontainer.json`) and `.vscode/launch.json` config.

`php --ini` - to know where php.ini file is located.

`/usr/local/etc/php/conf.d/xdebug.ini`:

```
zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20190902/xdebug.so
xdebug.mode = debug
xdebug.start_with_request = yes
xdebug.client_port = 9000
```


### Architecture, dev patterns, conventions

- vscode with devcontainer
- https://fonts.google.com/icons

Convensions:
- CSS:
  - css BEM framework
  - css.php
- PHP without framework philosophy
- stories folder
- dev folder for repl scripts
- user require_once instead require. Do not use brackets, emphasizing that this is directive and not function.
- phpcs & VariableAnalysis
- /** @var FileModel $file file */ 

Interesting solution in files:
- template.php
- exception-handling.php
- css.php
- app can work in non root folder, i.e.: admin:
  - devcontainer.json: `"postCreateCommand": "sudo chmod a+x \"$(pwd)\" && sudo ln -s \"$(pwd)/src\" /var/www/html/admin",`
  - Home link href: "." or "./"
  - in js: `location.search = url`
  - urls constructed with url.php helper starts with "index.php?" without leading "/".
  - forms doesn't have action attribute