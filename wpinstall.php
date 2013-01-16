#!/usr/bin/php
<?php
/**
 * Main index page
 * @package default
 * @author Marko Tomic <marko@markomedia.com.au>
 * @copyright Copyright 2013 Marko Tomic
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.0.0
 * This file is part of wordpress-install.
 *
 *   wordpress-install is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   wordpress-install is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with wordpress-install.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once('includes/tools.inc.php');
$tab = (chr(9));

$WEBROOT = promptUser("Your webroot directory? (Include trailing slash. i.e. /Users/johnsmith/Sites/mysite/www/)");
$VHOSTPATH = promptUser("Enter your vhost file path: (i.e. /etc/apache2/users/mysite.conf)");
$SERVERNAME = promptUser("What is your development server name? DO NOT include http:// (i.e. mysite.dev)");
$APACHEUSER = promptUser("What is the user apache runs under? (i.e. www or yourusername)");
$MYSQLDB = promptUser("Enter MySQL Database name:");
$MYSQLHOST = promptUser("Enter MySQL host:", "127.0.0.1");
$MYSQLUSER = promptUser("Enter MySQL user:", "root");
$MYSQLPWD = promptUser("Enter MySQL password: (leave blank if not sure)", "");

//Need this to emulate the browser-based installation
$_SERVER['HTTP_HOST'] = $SERVERNAME;
$_SERVER['REQUEST_URI'] = "/";

msg('Creating DB ...');
if(strlen($MYSQLPWD)) {
    exc("mysql -h" . $MYSQLHOST . " -u" . $MYSQLUSER . " -p" . $MYSQLPWD . " -e  'CREATE DATABASE IF NOT EXISTS '" . $MYSQLDB . ";");
} else {
    exc("mysql -h" . $MYSQLHOST . " -u" . $MYSQLUSER . " -e  'CREATE DATABASE IF NOT EXISTS '" . $MYSQLDB . ";");
}

msg('Downloading Wordpress ...');
exc('wget http://wordpress.org/latest.tar.gz');

msg('Unpacking WordPresss ...');
exc('tar xzf latest.tar.gz');

msg('moving wordpress into the webroot ' . $WEBROOT);
//make sure webroot exists
exc('mkdir -p "' . $WEBROOT . '"');
exc('cp -r wordpress/* "' . $WEBROOT . '"');
exc('rm -rf wordpress');

msg("Setup folder permissions..");
//set folder permissions to apache user
exc('chown -R ' . $APACHEUSER . ':staff ' . $WEBROOT);

//add local site to the hosts file
msg("Add entry in /etc/hosts file...");
exc('echo "127.0.0.1\t' . $SERVERNAME . '" >> /etc/hosts');

msg("Setting up the vhost...");
//set up apache vhost
$VHOST='NameVirtualHost *:80' . PHP_EOL . PHP_EOL;
$VHOST.='<Directory ' . $WEBROOT . '>' . PHP_EOL;
$VHOST.=$tab . 'Options Indexes FollowSymLinks MultiViews' . PHP_EOL;
$VHOST.=$tab . 'AllowOverride All' . PHP_EOL;
$VHOST.=$tab . 'Order allow,deny' . PHP_EOL;
$VHOST.=$tab . 'Allow from all' . PHP_EOL;
$VHOST.='</Directory>' . PHP_EOL;

$VHOST.='<VirtualHost *:80>' . PHP_EOL;
$VHOST.=$tab . 'DocumentRoot ' . $WEBROOT . PHP_EOL;
$VHOST.=$tab . 'ServerName ' . $SERVERNAME . PHP_EOL;
$VHOST.=$tab . 'DirectoryIndex index.php' . PHP_EOL;
$VHOST.='</VirtualHost>';

$fw = fopen($VHOSTPATH, "w");
fwrite($fw, $VHOST);

msg("Setting up the config file...");
//Now let's set up the config file
$config_file = file($WEBROOT . 'wp-config-sample.php');
$secret_keys = file_get_contents( 'https://api.wordpress.org/secret-key/1.1/salt/' );
$secret_keys = explode( "\n", $secret_keys );
foreach ( $secret_keys as $k => $v ) {
    $secret_keys[$k] = substr( $v, 28, 64 );
}
array_pop($secret_keys);

$config_file = str_replace('database_name_here', $MYSQLDB, $config_file);
$config_file = str_replace('username_here', $MYSQLUSER, $config_file);
$config_file = str_replace('password_here', $MYSQLPWD, $config_file);
$config_file = str_replace('localhost', $MYSQLHOST, $config_file);
$config_file = str_replace("'AUTH_KEY',         'put your unique phrase here'", "'AUTH_KEY',         '{$secret_keys[0]}'", $config_file);
$config_file = str_replace("'SECURE_AUTH_KEY',  'put your unique phrase here'", "'SECURE_AUTH_KEY',  '{$secret_keys[1]}'", $config_file);
$config_file = str_replace("'LOGGED_IN_KEY',    'put your unique phrase here'", "'LOGGED_IN_KEY',    '{$secret_keys[2]}'", $config_file);
$config_file = str_replace("'NONCE_KEY',        'put your unique phrase here'", "'NONCE_KEY',        '{$secret_keys[3]}'", $config_file);
$config_file = str_replace("'AUTH_SALT',        'put your unique phrase here'", "'AUTH_SALT',        '{$secret_keys[4]}'", $config_file);
$config_file = str_replace("'SECURE_AUTH_SALT', 'put your unique phrase here'", "'SECURE_AUTH_SALT', '{$secret_keys[5]}'", $config_file);
$config_file = str_replace("'LOGGED_IN_SALT',   'put your unique phrase here'", "'LOGGED_IN_SALT',   '{$secret_keys[6]}'", $config_file);
$config_file = str_replace("'NONCE_SALT',       'put your unique phrase here'", "'NONCE_SALT',       '{$secret_keys[7]}'", $config_file);

if(file_exists($WEBROOT .'wp-config.php')) {
    unlink($WEBROOT .'wp-config.php');
}

$fw = fopen($WEBROOT . 'wp-config.php', "a");

foreach ( $config_file as $line_num => $line ) {
    fwrite($fw, $line);
}

msg("Installing WordPress...");
define('ABSPATH', $WEBROOT);
define('WP_CONTENT_DIR', 'wp-content/');
define('WPINC', 'wp-includes');
define( 'WP_LANG_DIR', WP_CONTENT_DIR . '/languages' );

define('WP_USE_THEMES', true);
define('DB_NAME', $MYSQLDB);
define('DB_USER', $MYSQLUSER);
define('DB_PASSWORD', $MYSQLPWD);
define('DB_HOST', $MYSQLHOST);

$_GET['step'] = 2;
$_POST['weblog_title'] = "My Test Blog";
$_POST['user_name'] = "admin";
$_POST['admin_email'] = "marko2009@gmail.com";
$_POST['blog_public'] = true;
$_POST['admin_password'] = "admin";
$_POST['admin_password2'] = "admin";

require_once(ABSPATH . 'wp-admin/install.php');
require_once(ABSPATH . 'wp-load.php');
require_once(ABSPATH . WPINC . '/class-wp-walker.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

msg('restarting apache');
exc('apachectl -k graceful');
msg('Your WordPress site is ready. Navigate to http://' . $SERVERNAME . ' in your web browser');