<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'assignment2' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'X?6P0t3vMR,UW&}?@IRxoA.B~-&=E!7}KDW}]N]2WN~/mnZB$tu%w]-W]IEt96[r' );
define( 'SECURE_AUTH_KEY',  '*ldrv_YJ5BHhqlLzZyp{nbj&Us2qFEhqs/)p%G<&N3:]?K!*cFmqDzJf/(i^BtB[' );
define( 'LOGGED_IN_KEY',    '/3h4K@9qX:PW=v[7SdOi7HdEn@uBg9UliAnn1>1{IUCxVd3ft2QsRiUTo1Dwss>C' );
define( 'NONCE_KEY',        '9;8N>_yv=)z`x%_]fWgb/=@7J|6osX/d}_7l%s=8PH+ku12!%V8rdzy2Ehb%4Q1u' );
define( 'AUTH_SALT',        'MnNR(~z/SY:+7&(po27OTTzu3,PNvs1Xw(~o_vNT(uWJP?qlQjFrg#PIh@fHW&mR' );
define( 'SECURE_AUTH_SALT', '{ VDK21}SGY{[a:yqhNo0@>PD}!.KI?=[,6q suk7|d?7YUWp,|BYH~,2Xs.H9X ' );
define( 'LOGGED_IN_SALT',   '2xd.):+n`jM-[.r(X ?qI^Nb%EB`g}l_rf}5U6`b(X+1;fnK2Mt;K-=x1^t>ZPS~' );
define( 'NONCE_SALT',       'vd>_RU:N|;IzDnA=t-hNz.xAF-L:|B&@^Xa=q*7GK>TSY7x)bWN^~i%=!Dupw0to' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
