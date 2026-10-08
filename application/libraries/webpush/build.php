<?php
// Run composer install --no-dev, then php -d phar.readonly=0 build.php.
if (PHP_SAPI!=='cli') exit;
$path=__DIR__.'/webpush-deps.phar';
if (file_exists($path)) unlink($path);
$archive=new Phar($path); $archive->startBuffering();
$archive->buildFromDirectory(__DIR__, '#/vendor/#');
$archive->setStub("<?php __HALT_COMPILER();");
$archive->setSignatureAlgorithm(Phar::SHA256);
$archive->compressFiles(Phar::GZ); $archive->stopBuffering();
echo "Web push runtime built.\n";
