<?php

$scoper_path = './vendor_prefixed/vendor/composer';
$static_loader_path = $scoper_path . '/autoload_static.php';
$static_loader = file_get_contents($static_loader_path);
$static_loader = \preg_replace('/\'([A-Za-z0-9]*?)\' => __DIR__ \. (.*?),/', '\'a$1\' => __DIR__ . $2,', $static_loader);
file_put_contents($static_loader_path, $static_loader);
$files_loader_path = $scoper_path . '/autoload_files.php';
$files_loader = file_get_contents($files_loader_path);
$files_loader = \preg_replace('/\'(.*?)\' => (.*?),/', '\'a$1\' => $2,', $files_loader);
file_put_contents($files_loader_path, $files_loader);
