<?php
/*
	common.lib.php
	Squire 3.0 "Poor Mans" CMS foundation
	Jason M. Knight, November 2020
	
	contains common library functions and methods
	used by most if not all pages
*/

function cleanString($string) {
	return htmlspecialchars(strip_tags($string));
}

function cleanPath($path) {
	return trim(str_replace(['\\', '%5C'], '/', $path), '/');
}

/*
	A more robust version of hashCreate should allow for multiple hashes
	to be stored of the same name, with an expiration time on it.
*/

function hashCreate($name) {
	return $_SESSION[$name] = bin2hex(random_bytes(24));
}

function hashExists($name, $hash) {
	return isset($_SESSION[$name]) && ($_SESSION[$name] == $hash);
}

function httpError($code) {
	include('fragments/errorHandler.php');
}

function uriLocalize($uri) {
	if (
		substr($uri, 0, 4) == 'http' ||
		substr($uri, 0, 1) == '#'
	) return $uri;
	return ROOT_HTTP . $uri;
} // uriLocalize

function templateLoad($name, $actionPath = 'template/default') {
	if (
		file_exists($fn = TEMPLATE_PATH . $name . '.template.php') ||
		file_exists($fn = $actionPath . $name . '.template.php')
	) include_once($fn);
	else {
		error_log(
			'unable to find template file for "' . $name . '"'
		);
		die('template error');
	}
} // templateLoad

/*
	safeInclude is utterly dumbass, but since PHP just LOVES to
	bleed scope all over the place with 1970's style "includes"
	we have to do this JUST to break local scope!
*/
function safeInclude($file) {
	include($file);
}

final class Request {

	private static
		$data = false,
		$path = '';
	
	private static function set() {
		self::$path = parse_url(cleanPath($_SERVER['REQUEST_URI']), PHP_URL_PATH);
		if (strpos(self::$path, '..')) die('Hacking Attempt Detected, Aborting');
		self::$path = substr(self::$path, strlen(ROOT_HTTP) - 1);
		self::$data = (
			empty(self::$path) ?
			[ Settings::get('default_action') ] :
			explode('/', self::$path)
		);
		foreach (self::$data as &$p) $p = urldecode($p);
	} // Request::set
	
	public static function value($index = 0) {
		if (!self::$data) self::set();
		return isset(self::$data[$index]) ? self::$data[$index] : false;
	} // Request::value
	
	public static function getPath() {
		if (count(self::$data) == 0) self::set();
		return self::$path;
	} // Request::getPath
	
} // Request

final class Settings {

	private static $privateData = [];
	
	public static $publicData = [];
	
	public static function loadFromIni(...$files) {
		foreach ($files as $filename) {
			$data = parse_ini_file($filename, true);
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					switch ($key) {
						case 'DEFINE':
							foreach ($value as $dName => $dValue) define($dName, $dValue);
							continue 2;
						case 'public':
							self::$publicData[$key] = $value;
							continue 2;
					}
					if (array_key_exists($key, self::$privateData)) {
						$value = array_merge($value, self::$privateData[$key], true);
					}
				}
				self::$privateData[$key] = $value;
			}
		}
	} // Settings::loadFromIni
	
	public static function get($name, $section = false) {
		if ($section) {
			if (array_key_exists($section, self::$privateData)) {
				if (
					array_key_exists($name, self::$privateData[$section])
				) return self::$privateData[$section][$name];
			} else  if (array_key_exists($section, self::$publicData)) {
				if (
					array_key_exists($name, self::$publicData[$section])
				) return self::$publicData[$section][$name];
			}
		} else {
			if (
				array_key_exists($name, self::$privateData)
			) return self::$privateData[$name];
			if (
				array_key_exists($name, self::$publicData)
			) return self::$publicData[$name];
		}
		return false;
	} // Settings::getValue
	
	public function set($value, $name, $section = false) {
		if ($section) {
			if (!array_key_exists($section, self::$publicData)) {
				self::$publicData[$section] = [ $name => $value ];
				return;
			}
			self::$publicData[$section][$name] = $value;
		} else self::$publicData[$name] = $value;
	} // Settings::__set
	
} // Settings

define(
	'SCRIPT_PATH',
	cleanPath(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME))
);

define(
	'ROOT_HTTP',
	'/' . SCRIPT_PATH . (SCRIPT_PATH == '' ? '' : '/')
);
