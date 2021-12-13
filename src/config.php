<?php

return array(
  'title' => 'archivist',
  'subtitle' => 'PHP file manager',
  'root' => __DIR__ . '/../content/target',
  'users' => [
    'admin' => getenv('ADMIN_PWD')
  ],
  'allow-symlinks' => true,
  'scripts' => [
    'Deploy' => 'shell/deploy.sh',
    'Say Hi' => 'shell/say_hi.sh',
  ]
);

?>