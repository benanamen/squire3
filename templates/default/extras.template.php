<?php

/*
	extras.template.php
	Squire 3.0 Base Template
	Jason M. Knight, November 2020
*/

/*
	Extras are non-main content like sidebars
*/
function template_extrasPart($id, $extras) {

	echo '

				<div id="', $id, '">';

	foreach ($extras as $id => $section) {
		if (is_array($section)) {
			template_sectionHeader($id, $section['title']);
			if (array_key_exists('php', $section)) {
				include_once('extras/' . $section['php'] . '.extra.content.php');
				($section['php'] . '_SectionRun')();
				$include = null;
			}
			if (array_key_exists('static', $section)) readFile(
				'extras/' . $section['static'] . '.extra.static'
			);
		} else {
			template_sectionHeader($id, $section);
			readFile('extras/' . $id . '.extra.static');
		}
		template_sectionFooter($id);
	}

	echo '
				<!-- #', $id, ' --></div>';

} // template_extrasPart
