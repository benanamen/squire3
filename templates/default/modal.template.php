<?php

/*
	modal.template.php
	Squire 3.0 Modal Template
	Jason M. Knight, November 2020
*/

function template_modalHeader($id, $title) {

	echo '
	<div id="', $id, '" class="modal">
		<a href="#" class="modalClose" hidden aria-hidden="true"></a>
		<div><section>
			<a href="#" class="modalClose" hidden aria-hidden="true"></a>
			<h2>', $title, '</h2>';
		
} // template_modalHeader

function template_modalFooter($id) {
	
	echo '
		</section></div>
	<!-- #', $id, '.modal --></div>';

} // template_modalFooter

function template_modalFormHeader($id, $title, $action, $method = 'POST') {

	echo '
	<form action="', ROOT_HTTP, $action, '" method="', $method, '" id="', $id, '" class="modal">
		<a href="#" class="modalClose" hidden aria-hidden="true"></a>
		<div><section>
			<a href="#" class="modalClose" hidden aria-hidden="true"></a>
			<h2>', $title, '</h2>';

} // template_modalFormHeader

function template_modalFormFooter($id) {
	
	echo '
		</section></div>
	<!-- #', $id, '.modal --></form>';

} // template_modalFormFooter

function template_modalInclude($data, $id, $type = 'modal') {
	$modalFilePrefix = 'modals/' . $id . '.' . $type . '.';
	if (file_exists($modalFile = $modalFilePrefix . 'php')) {
		safeInclude($modalFile);
		($id . '_modalRun')($data, $id);
	}
	if (file_exists($modalFile = $modalFilePrefix . 'static')) {
		readFile($modalFile);
	}
}
