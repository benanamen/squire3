<?php

/*
	common.template.php
	Squire 3.0 Base Template
	Jason M. Knight, November 2020
*/


/*
	template_header
	outputs everything from the DOCTYPE to the <main> tag
*/

function template_header($data = []) {

	echo '<!DOCTYPE html><html lang="', (
		Settings::get('lang') ?: 'en'
	), '"><head><meta charset="', (
		Settings::get('encoding') ?: 'utf-8'
	), '">
<meta
	name="viewport"
	content="width=device-width,height=device-height,initial-scale=1"
>
<meta
	http-equiv="X-UA-Compatible"
	content="IE=9"
>';

	template_headSettings('meta');
	template_headSettings('link');
	
	echo '
<!--[if !IE]>-->';


	foreach (Settings::get('style') as $name => $media) echo '
<link
	rel="stylesheet"
	href="', ROOT_HTTP . TEMPLATE_PATH, $name, '"
	media="', $media, '"
>';

	echo '
	<link
		rel="stylesheet"
		href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"
		integrity="sha256-h20CPZ0QyXlBuAw7A+KluUYx/3pK+c7lYEpqLTlxjYQ="
		crossorigin="anonymous"
		media="screen,projection,tv"
	>
	<link
		href="//fonts.googleapis.com/css2?family=Poppins" rel="stylesheet"
		media="screen,projection,tv"
	>
<!--<![endif]-->
<title>
	', (
		($pageTitle = Settings::get('pageTitle')) ? $pageTitle . ' - ' : ''
	), Settings::get('siteTitle'), '
</title>
</head><body>

	<input
		type="checkbox"
		id="toggle_darkMode"
		class="toggle remember"
		hidden
		aria-hidden="true"
	>

	<input
		type="checkbox"
		id="toggle_stickyTop"
		class="toggle remember"
		hidden
		aria-hidden="true"
	>
	
	<div id="fauxBody"><div id="fauxInner">

		<header id="top">
			<h1><a href="', ROOT_HTTP, '">', Settings::get('h1Content'), '</a></h1>
			<div id="mainMenu">
				<a href="#" class="modalClose" hidden aria-hidden="true"></a>
				<div><nav>
					<a href="#" class="modalClose" hidden aria-hidden="true"></a>
					<ul>';

	$currentPage = Settings::get('currentPage');

	foreach (Settings::get('mainMenu') as $line) {
		if ($line['text'] == $currentPage) echo '
						<li>
							<em>', $line['text'], '</em>
						</li>';

		else echo '
						<li>
							<a href="', uriLocalize($line['href']), '">
								', $line['text'], '
							</a>
						</li>';
	}

	echo '
					</ul>
				</nav></div>
			<!-- #mainMenu --></div>
			
			<a href="#mainMenu" class="mainMenuOpen" hidden aria-hidden="true"></a>

			<label for="toggle_darkMode" class="label_darkMode" hidden aria-hidden="true">
				<i><!-- day/night icon --></i>
				<span>
					Switch to
					<span>Light<span>/</span></span>
					<span>Dark</span>
					Theme
				</span>
			</label>
			
			<label for="toggle_stickyTop"></label>

		</header>


		<div class="mainGroup ', (
			Settings::get('noExtras') ? 'noExtras' : 'extras'
		), '">
			<main>
				<!--[if IE ]>
					<h2 style="color:red;">Error, Outdated Browser Detected</h2>
					<p>
						<strong style="color:red;">You are recieving a vanilla version of this page because your browser is a decade or more out of date. For full / proper appearance, please revisit in a modern browser.</strong>
					</p>
				<![endif]-->
';

} // template_header

/*
	template_footer
	Creates everything after the <main> tag.
*/


function template_footer($data = []) {

	echo '
			</main>';
			
	if (!Settings::get('noExtras')) {

		$extras1 = Settings::get('extras1');
		$extras2 = Settings::get('extras2');

		if ($extras1 || $extras2) {
			safeInclude(TEMPLATE_PATH . 'extras.template.php');
			if ($extras1) template_extrasPart('extras1', $extras1);
			if ($extras2) template_extrasPart('extras2', $extras2);
		}
		
	}
	
	echo '

		<!-- .mainGroup --></div>

		<footer id="bottom">
';

	safeInclude('fragments/footer.content.php');

	echo '
		</footer>

	<!-- #fauxInner, #fauxBody --></div></div>';
	
	$modals = Settings::get('modals');
	$modalForms = Settings::get('modalForms');
	
	if ($modals || $modalForms) {
	
		templateLoad('modal');
	
		if ($modals) foreach ($modals as $id => $title) {
			template_modalHeader($id, $title);
			template_modalInclude($data, $id);
			template_modalFooter($id);
		}
		
		if ($modalForms) {
			templateLoad('forms');
			foreach ($modalForms as $id => $mData) {
				template_modalFormHeader(
					$id, $mData['title'], $mData['action'], $mData['method']
				);
				template_modalInclude($data, $id, 'modalForm');
				template_modalFormFooter($id);
			}
		}
		
	} // modals
		
	echo '
	
	<script src="', ROOT_HTTP, TEMPLATE_PATH, 'scripts/default.template.js"></script>

</body></html>';

} // template_footer

/*
	template_headSettings
	Used to create <link> and <meta> inside <head>
*/

function template_headSettings($tag) {

	if (!($data = Settings::get($tag))) return;
	foreach ($data as $name => $fields) {
		echo "\r\n<", $tag;
		foreach ($fields as $key => $value) {
			switch ($key) {
				case 'href':
				case 'src':
					$value = ROOT_HTTP . $value;
					break;
			}
			echo "\r\n\t", $key, '="', htmlspecialchars($value), '"';
		}
		echo "\r\n>";
	}

} // template_headSettings

function template_sectionHeader($id, $title, $hDepth = 2) {

	echo '
	
					<section id="', $id, '">
						<h', $hDepth, '>', $title, '</h', $hDepth, '>
						<div>';
				
} // template_sectionHeader

function template_sectionFooter($id) {

	echo '
						</div>
					<!-- #', $id, ' --></section>';

} // template_sectionFooter
