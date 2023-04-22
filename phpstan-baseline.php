<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPathname\\(\\) on SplFileInfo\\|string\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/libCustomPack/libCustomPack.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isFile\\(\\) on SplFileInfo\\|string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/libCustomPack/libCustomPack.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
