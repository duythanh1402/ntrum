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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dbyxpnfqhosting_devanzenvn' );

/** Database username */
define( 'DB_USER', 'dbyxpnfqhosting_devanzenvn' );

/** Database password */
define( 'DB_PASSWORD', 'mg8d6JzdcAC08JnEVqWX3BLtnL4LNX3e' );

/** Database hostname */
define( 'DB_HOST', 'localhost:/var/lib/mysql/mysql.sock' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'Ibk(,ha}-JBOks}9M?Yduc$nJ;Sss.F9G]i&:Clt?s?6^U ^m !Tf$Vng.ObhMLn' );
define( 'SECURE_AUTH_KEY',   'Q1u@VhKE|ndhsEGK<>|ho#1>g^u:9zZUW36z:$F#L20K`htG)(Xh<&8*v/YW$R?)' );
define( 'LOGGED_IN_KEY',     'OopJc@V[)UDMoFW>U_z(7GrDSG,M_3b?>tDz*lZRF{I)U;tLPQxHta-yp[nFQ[l[' );
define( 'NONCE_KEY',         'VFg$Dh^7mRZ%9;ZVca;&D6.][;e1g!$k-x{~D:&>NP]kYEuLP#-/twe.$2%{o#`c' );
define( 'AUTH_SALT',         '_Oo@Kfn(T(d+#=57XF5IH1:lv2-IOr9vrx;`hSv{m%l+jGhF3lh>h0=x_K3Sb`nq' );
define( 'SECURE_AUTH_SALT',  'W#f8_QZ!pX5`Y8!ejM>6+OJuzJ=;iBt3=Dpg/j>rZI[l3M+-@TH]D5,LD= BkIa8' );
define( 'LOGGED_IN_SALT',    'qXFnwwPSC/RBU]GR{hh8qnXI#Zb~!Ab7`lM4N.t?%G=p6~&:USmzswhpL6hJ%Rzt' );
define( 'NONCE_SALT',        'Pf&7EeY-qGf]4;kt)PV3u`WYe-DE]M7FyLLM&qA2D1zX-V;TGHq<Gb:WvL{qam$v' );
define( 'WP_CACHE_KEY_SALT', 'sN<}OelHr,%lN%*lRZ3cXHR5U+:y0qr@, NZgyonSOMttkheCW}/d+OAfw6:,Jqi' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = '8hi_default';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_AUTO_UPDATE_CORE', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
