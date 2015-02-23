<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| User Login types
|--------------------------------------------------------------------------
*/

define('COMMON_USER',	1);
define('SYSTEM_ADMIN',	2);
define('DIRECTOR',		3);
define('SECRETARY',		4);
define('COORDINATOR', 	5);


/*
|--------------------------------------------------------------------------
| Subscription Status types
|--------------------------------------------------------------------------
*/

define('SUSCRIPTION_STATUS_EXCLUDED', 					-3);
define('SUSCRIPTION_STATUS_GIVEN_UP',		 			-2);
define('SUSCRIPTION_STATUS_CANCELLED',		 			-1);
define('SUSCRIPTION_STATUS_PRE_SUBSCRIBED_INCOMPLETE',	0);
define('SUSCRIPTION_STATUS_PRE_SUBSCRIBED',				1);
define('SUSCRIPTION_STATUS_WAITING_PAYMENT',			2);
define('SUSCRIPTION_STATUS_SUBSCRIPTION_OK',			3);




/* End of file constants.php */
/* Location: ./application/config/constants.php */