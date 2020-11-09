<?php
/*
	ASSUMES
		variable $code is set.
*/

$codeTextList = [
	"400" => "Bad Request",
	"401" => "Unauthorized",
	"402" => "Payment Required",
	"403" => "Forbidden",
	"404" => "Not Found",
	"405" => "Method Not Allowed",
	"406" => "Not Acceptable",
	"407" => "Proxy Authentication Required",
	"408" => "Request Timeout",
	"409" => "Conflict",
	"410" => "Gone",
	"411" => "Length Required",
	"412" => "Precondition Failed",
	"413" => "Request Entity Too Large",
	"414" => "Request URI Too Long",
	"415" => "Unsupported Media Type",
	"416" => "Requested Range Not Satisfyable", // Like my ex
	"417" => "Expectation Failed",
	"500" => "Internal Server Error",
	"501" => "Not Implemented",
	"502" => "Bad Gateway",
	"503" => "Service Unavailable",
	"504" => "Gateway Timeout",
	"505" => "HTTP Version Not Supported"
];

if (empty($code)) $code = 501;

$codeText = $code . ' - ' . (
	array_key_exists($code, $codeTextList) ?
	$codeTextList[$code] :
	"Unknown or Unsupported Error"
);

http_response_code($code);
Settings::set($codeText, 'pageTitle');
Settings::set(true, 'noExtras');

template_header();

echo '
				<section class="httpError">
					<div>
						<h2>', $codeText, '</h2>
						<p>
							Your request 
							"<em>', htmlspecialchars($_SERVER['REQUEST_URI']), '</em>"
							could not be served at this time.
						</p>
					</div>
				</section>';
	
template_footer();

die();