<?php
define ("DB_SERVER", "localhost");
define ( 'DB_USER', 'root' );
define ( 'DB_PASSWORD', '' );
define ( 'DB_NAME', 'awn' );

define("TBL_USERS", "awn.users");
define ( 'COL_USERNAME', 'username' );
define ( 'COL_OBJID', 'OBJID' );
define ( 'COL_USERIDCOOKIE', 'useridcookie' );
define ( 'COL_ACCESSLEVEL', 'userlevel' );

define ( 'TBL_ACTIVE_USERS', 'awn.active_users' );
define ( 'TBL_CROPS', 'irrigation.tblcropdefaults' );
define ( 'TBL_SOILS', 'irrigation.tblsoildefaults' );
define ( 'TBL_FIELD', 'irrigation.tblfield' );
define ( 'TBL_INDIVID_FIELD', 'irrigation.tblindividfield' );
define ( 'TBL_AGRIMET_STATIONS', 'irrigation.mtagrimetstations' );
define ( 'TBL_STATIONS', 'awn.METADATA' );

define ( 'TBL_ACTIVE_GUESTS', 'awn.active_guests' );
define ( 'TBL_BANNED_USERS', 'banned_users' );
define ( 'COL_IP','ip');

define("GUEST_NAME", "guest");
define("GUEST_LEVEL", 0);

define("EMAIL_FROM_NAME","WSU AgWeatherNet/ISM");
define("EMAIL_FROM_ADDR","sehill@wsu.edu");
/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define ( 'TRACK_VISITORS', true );

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define ( 'USER_TIMEOUT', 25 );
define ( 'GUEST_TIMEOUT', 5 );

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define ( 'COOKIE_EXPIRE', 60 * 60 * 24 * 365 ); //100 days by default
define ( 'COOKIE_PATH', '/' ); //Avaible in whole domain
define ( 'COOKIE_DOMAIN', '.wsu.edu' ); //Avaible in whole domain

//define("ID_COOKIE", "ismUserID78");
//define("USERNAME_COOKIE", "ismUserName78");
define("ID_COOKIE", "awncookid");
define("USERNAME_COOKIE", "awncookname");

?>