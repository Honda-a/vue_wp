<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_headless' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1:8889' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '#D0gZ]I^s2!S82]~]>Fwva$EU348:a1V/tu,y!qUU];2L&1TDC];mU6.XYN}>VC-' );
define( 'SECURE_AUTH_KEY',   '/Q:?#1##bHp?!I5w7|n0<k !ZvMX4B|0}i`_#@IDw[K4(l($(AqBN#$SRuW*KpL>' );
define( 'LOGGED_IN_KEY',     'kUMtKEd#2QAq6!d8n~dofc`#V(Ux=SPEq&de<~pq=~nHTFCyAc}s9rKOXu6O~IFB' );
define( 'NONCE_KEY',         '%qJ_Tw!n:TFsl~Sq{)z!:@]9jku/=*4gbm=3]&i)^@WrDq)q{HTFs-eR;eu!$iz,' );
define( 'AUTH_SALT',         '0-8_bRnhytA#57|XHpz[!RyeuG#G+#c.}yjOT:AB=@J1{{tU[u;hX@ {ZZIK#8]x' );
define( 'SECURE_AUTH_SALT',  'myEai_$3j:}YH~OJg=hN4*%4aCA3p!]Z.Wy4iUK`#Q?: 6aumDkC3[^4D/g<Sz0`' );
define( 'LOGGED_IN_SALT',    'E&^cb/qviW/5$hUL>M6]5nQhM`l8GGo45>XjlWL*brmHrbdf|?0.1.@cl{MgdKgX' );
define( 'NONCE_SALT',        'Qv`y!.oWBl @/Bs.d#-<p(=jad3dZ,FY5(u3GeF)Cr&ZfMc<#$)k$f[OV[-Ikc>z' );
define( 'WP_CACHE_KEY_SALT', '.N5-t|Y NO$w9L@NOm)CwK7h/7IS?$eZLxMQs`-.)9pvP_-uVsXyP~GMz}R:xS^p' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
