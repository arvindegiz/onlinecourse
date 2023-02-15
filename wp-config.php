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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'onlinecourse' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
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
define( 'AUTH_KEY',         'amunqK2]m7s~a}EO}8bO=^XUN]i91ZWcP/w!wd]yaX1;?ez?4A|K6M=7HO9ZJFk*' );
define( 'SECURE_AUTH_KEY',  '#.`)LN;P20,Kv>@#n/SBD+ hsrrT3uMyHF^`1JeE4f#u*$rV9r(-g%)1(1w$Yr-K' );
define( 'LOGGED_IN_KEY',    '+)}HQh|W7fib3sXgBa5xbRdO$y%wj6Md;BM.,zpSfqL~EbTFg9;:bAF_PzQ:Qpo(' );
define( 'NONCE_KEY',        'hK#/2v>vWP8y1CSLHQm{_~AJcF.vc&;X_E>rie_`54^TA@NZA,wUK&7f2b5_PmO.' );
define( 'AUTH_SALT',        'Qu~i=6MlF`I}IMD~O&uh<_m?w_0MKw]#>^i0@zJK6UVs~eLx8/hJ#<*%tFR7p,*K' );
define( 'SECURE_AUTH_SALT', 'hf! =B%2#uZyUuZKX:LGXnqp#C2Zby[[g>(gT1#.D+Uyf%jd/VCfultQ-O!tP]7}' );
define( 'LOGGED_IN_SALT',   '[14Rgxc-T=/-aW5f|)7zDlmOGh}8 vpK!Z2oy.:kHXZ81dP&+D, a/lorx8 SR`P' );
define( 'NONCE_SALT',       'i,|rpVt?5mpi>[iCC<lX0e<j9p i8)UMX=46%i3{7P^&98Tmz!9FYn0tjJBblB(6' );

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
