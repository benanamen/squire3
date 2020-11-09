<?php

function action_startup() {

	/*
		In "real" version this would validate the form, sending
		the message if valid, and sending the appropriate handler.
		
		Handlers such as:
			actions/contact/contact.invalidForm.php
			actions/contact/contact.failed.static
			actions/contact/contact.success.static
	*/

	return [
		'contentFilePath' => 'actions/contact/contact.success'
	];

} // action_startup