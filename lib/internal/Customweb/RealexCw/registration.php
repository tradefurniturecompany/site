<?php
$pathToLib = realpath(dirname(dirname(dirname(__FILE__))));
set_include_path(implode(PATH_SEPARATOR, array(
	get_include_path(),
	$pathToLib,
)));

// Some server configuration disallow the changing of the include path. We have
// to provide here a better error message, than simply wait until a require fails.
if (strpos(get_include_path(), $pathToLib) === false) {
	die("The include path could not be changed. Please change the server configuration to allow changing the include path by using the function 'set_include_path'.");
}

if (defined('VENDOR_PATH')) {
	$vendorDir = require VENDOR_PATH;
} else {
	$vendorDir = require __DIR__ . '/../../../../app/etc/vendor_path.php';
}
$vendorAutoload = __DIR__ . "/../../../../{$vendorDir}/autoload.php";

if (file_exists($vendorAutoload)) {
    $composerAutoloader = include $vendorAutoload;

    $libDirectory = dirname(dirname(__DIR__));
    $namespaces = [
		'Customweb',
		'Crypt',
		'Math',
		'Net',
		'File',
		'System'
	];
	foreach ($namespaces as $namespace) {
	    $composerAutoloader->set($namespace, $libDirectory);
	}
}