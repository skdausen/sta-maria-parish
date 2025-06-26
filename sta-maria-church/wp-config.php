<?php
define('WP_CACHE', true); // WP-Optimize Cache
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sta-maria-church' );
/** Database username */
define( 'DB_USER', 'admin1' );
/** Database password */
define( 'DB_PASSWORD', 'iloveispsc@2025' );
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
define( 'AUTH_KEY',         '#is=><P6U|3!m2zXH9, iE<lONv|?9lKY@Zte:M=pT2D*vHGab8bM2]-(87ar8X|' );
define( 'SECURE_AUTH_KEY',  '4YwpBq(^!m[4Xp.5>&y lD7bT[p=POj{D(+7/%hd_~-BCz,meAI(/qULYCe|<*iS' );
define( 'LOGGED_IN_KEY',    '~BD0<tu1i1sZ$Ow}k|t&$T7rSt^nC(.TqsSdDPr&mLpFrnd6:7J6S~oJv3.O2{`)' );
define( 'NONCE_KEY',        'rTL|)_`k+CK!lxuJB<SafMRy,%J>(T,&NeX.@}uRf&gC&GEuD)8>9,8kTLnX:ytL' );
define( 'AUTH_SALT',        'N] u54mB0W-uUJh-V74X!:_GMQl*+*R1 tbo !%Uy&~KC@&jK]-XG#<pUHP;d<%t' );
define( 'SECURE_AUTH_SALT', '6&QNIqc#>/{:T(BEG;qXgKy~_oL7Z.PCE ,5}@U4<o]gAH<VjF!$53e0L8q@fq82' );
define( 'LOGGED_IN_SALT',   'dfZ;X%7i11zS05m*R]5,%(}sle4Vnw6o2NhbxfB]>b@S:|6l]^d^BS(ba(NZ76.e' );
define( 'NONCE_SALT',       'H![=bNfwZWj|)3N&*{s2[gKZ(U2qgK]:mM)]>7R<%p(lVkkm1O%B F<8nOD5)i)x' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );
/* Add any custom values between this line and the "stop editing" line. */
define(constant_name: 'WP_HOME', value: 'https://192.168.1.4/sta-maria-church/');
define(constant_name: 'WP_SITEURL', value: 'https://192.168.1.4/sta-maria-church/');
define(constant_name: 'WP_MEMORY_LIMIT', value: '1024M');
define(constant_name: 'WP_ALLOW_REPAIR', value: true);
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';