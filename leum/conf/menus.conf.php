<?php

define('PRIMARY_MENU',
	[
		// The 'Home' link.
		[
			'class' => "title-menu-heading",
			'href' => ROOT . "/",
			'content' => APP_TITLE
		],
		// The shows link.
		[
			'href' => ROOT . "/shows/",
			'content' => "Shows"
		],
		// The movies link.
		[
			'href' => ROOT . "/movies/",
			'content' => "Movies"
		],
		// The browse link.
		[
			'href' => ROOT . "/browse/",
			'content' => "Browse"
		],
		// The edit link.
		[
			'href' => ROOT . "/edit/",
			'content' => "Edit"
		],
	]
);
?>