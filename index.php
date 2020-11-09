<?php

/*
	index.php
	Squire 3.0 "one index to rule them all"
	Jason M. Knight, November 2020
*/

// before we do ANYTHING else, let's set up gzip compression
foreach (['gzip', 'x-gzip', 'x-compress'] as $type) {
	if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], $type) !== false) {
		ob_start('ob_gzhandler');
		ob_implicit_flush(0);
		header('Content-Encoding: ' . $type);
		register_shutdown_function(function() {
			ob_end_flush();
		});
		break;
	}
}

session_start();
/*
	regenerating the ID on every pageload reduces the time window
	in which a session key can be exploited for MITM attacks.
*/
session_regenerate_id();

/*
	prevent displaying site in frame. Some people will say do this
	in the httpd.conf or other server config, it takes NO processing
	difference here, it's easier to say do it only for our single
	entry-way from here, I prefer not to put something this important
	outside our portable code, and it's better not to make assumptions
	about what server software is in use. Not everyone uses Apache.
*/
header('X-Frame-Options: DENY');

/*
	Load functions and methods common to all pages
*/
include('libs/common.lib.php');

/*
	Many settings are loaded from ini as the setter and are
	only retrievable with a getter.
*/
Settings::loadFromIni(
	'default.ini.php',
	'user.ini.php'
);

define(
	'TEMPLATE_PATH',
	'templates/' . (Settings::get('template') ?: 'default') . '/'
);

templateLoad('common');

/*
	IIFE to isolate scope, so a code appendage can't see anything
	we want to keep local here just in case. Thankfully PHP now has
	proper IIFE
*/
(function() {
	
	$action = Request::value();
	if (!$action) $action = 'static';
	if (!is_dir('actions/' . $action)) httpError(404);
	$actionPath = 'actions/' . $action . '/' . $action . '.';
	safeInclude($actionPath . 'startup.php');
	$data = action_startup();
	template_header($data);
	if (file_exists($fn = $data['contentFilePath'] . '.content.php')) {
		safeInclude($fn);
		action_content($data);
	}
	if (file_exists($fn = $data['contentFilePath'] . '.static')) readfile($fn);
	template_footer($data);

})();
