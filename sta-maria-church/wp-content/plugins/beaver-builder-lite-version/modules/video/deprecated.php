<?php

FLBuilder::register_module_deprecations( 'video', [
	'v1' => [
		'config' => [
			'include_wrapper' => true,
		],
		'files'  => [
			'includes/frontend.php',
		],
	],
] );
