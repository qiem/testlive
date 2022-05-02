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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define('AUTH_KEY',         'MQofc5HX+lLpR78nvSuWTBFApRcM+Z+lB7dJKYvT+A3W78k0JcaL9Ec90gupdzAnM6nNwO+76TXkD/MSnGzIBA==');
define('SECURE_AUTH_KEY',  'qgTza9WIK119m/tK+0TR96cBEGv+eXr2Z26FwXqbHeTwR+vYCuANhJ7+vtZOGveoOGLqVQp1NvRSQv3p75FjLg==');
define('LOGGED_IN_KEY',    'KirbTV1CkRcN1u+cRX1kGH4KVEMHNFAmWQwTdixtHHPEPLEjvAZX9AoSsuUNzvqcjZT06QTqZoVWUNJBnzJJZg==');
define('NONCE_KEY',        'CRUgHnpcQ0hoWam6gwVfuPE6amRRFUEt9YLeOq0t3n0rdNbCILiyybcnhA+PCJw0QyE1zzrqtkteVJ6JnAaK5Q==');
define('AUTH_SALT',        'JmSNYg/oUDbsAAd9B8oc4EhbPf0GRQFgugm5uQCiIGdZS4wEUFeci3S7RUa4lok5DVd7Ogtx3gk0Hy/xzkEpYA==');
define('SECURE_AUTH_SALT', 'IIimnAnmJxwWjxe02cteRAxsPzWGmSh124EbvR4gMLZECEWt8q7k5e8mSoODSUC1vQYkn1aeIi+MEPjrtvYhAg==');
define('LOGGED_IN_SALT',   'cXwdJrwXV2TX4t3vv4LpfTkeKzUxRz7/qibwEwUwYzlHLElJcY6OvqDXNuKJ8YiGihqkadMdd7jQMZHrSwHfAw==');
define('NONCE_SALT',       'gvguWFaJXMe0IQRIRZvbo40XFnVTc+t/nzSETE8Mx01sOZt4pEBAOJ/eTu+Piuhztj3o2S8WSyRxO3o+a+oZ7w==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
