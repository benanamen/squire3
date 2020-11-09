<?php

function template_submitsAndHiddens($id, $submitText, $hidden = [], $hash = true) {

	echo '
			<div class="submitsAndHiddens">
				<button>', $submitText, '</button>';
				
	if ($hash) $hidden[$id . '_hash'] = hashCreate($id . '_hash');
				
	foreach ($hidden as $name => $value) echo '
				<input type="hidden" name="', $name, '" value="', $value, '">';
				
	echo '
			<!-- .submitsAndHiddens --></div>';

} // template_modalFormSubmitsAndHiddens