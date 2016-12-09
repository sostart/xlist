<?php

// 需要打包的文件后缀
$exts = ['php'];

// 包的名称, 注意它不仅仅是一个文件名, 在stub中也会作为入口前缀
$file = 'heresy.phar';

$phar = new Phar(__DIR__ . '/' . $file, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $file);

// 开始
$phar->startBuffering();

// 将后缀名相关的文件打包
foreach ($exts as $ext) {
    $phar->buildFromDirectory(__DIR__, '/\.' . $ext . '$/');
}

// 去除当前打包文件
$phar->delete(pathinfo(__FILE__, PATHINFO_BASENAME));

// 压缩
$phar->compressFiles(Phar::GZ); // Phar::NONE

// 设置入口
/*
$phar->setStub("<?php Phar::mapPhar('{$file}'); require 'phar://{$file}/bootstrap.php'; __HALT_COMPILER(); ?>");
*/
$phar->setStub( $phar->createDefaultStub('bootstrap/heresy.php') );

// 完成
$phar->stopBuffering();

echo "{$file}\n";