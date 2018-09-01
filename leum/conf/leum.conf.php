<?php 
// The web root of the site. (no trailing slash).
define('ROOT', '/leum');
// Title of this application.
define('APP_TITLE', 'Leum');
define('TITLE_FORMAT', "%title - %appTitle");

// The directory where the media and thumbnails are stored relative to the index.php file.
// (no trailing slash.) It's advised that you use symbolic links.
define('MEDIA_DIR', '/content/media');
define('THUMB_DIR', '/content/thumbs');

// Where the leum logs are stored.
define('LOG_DIR', SYS_ROOT . '/logs');
define('LOG_LEVEL', Log::INFO);

define('API_URL', ROOT . "/api");

// Default page size.
define('PAGE_SIZE', 100);

// Size of thumbnails (pixels.)
define('THUMB_SIZE', 196);

// http://php.net/manual/en/function.password-hash.php#example-985
define('AUTH_PASS_COST', 10);



define('ACTIVE_PLUGINS', ["shows", "test_plugin", "git_status"]);




// Get the routes.
include_once "routes.conf.php";
// Get the database connection.
include_once "database.conf.php";
include_once "permissions.conf.php";
?>
