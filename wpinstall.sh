#!/bin/sh

echo -n "What's your webroot directory? (Include trailing slash. i.e. /Users/johnsmith/Sites/mysite/www/)"
read -e WEBROOT

echo -n "Enter your vhost file path: (i.e. /etc/apache2/users/mysite.conf)"
read -e VHOSTPATH

echo -n "What's your development server name? DO NOT include http:// (i.e. mysite.dev)"
read -e SERVERNAME

echo -n "What is the user apache runs under? (i.e. www or johnsmith)"
read -e APACHEUSER

echo "Downloading Wordpress ..."
wget http://wordpress.org/latest.tar.gz

echo "Unpacking WordPresss"
tar xzf latest.tar.gz

echo "moving wordpress intto the webroot $webroot"
#make sure webroot exists
mkdir -p $WEBROOT
cp -r wordpress/* $WEBROOT
rm -rf wordpress

#set folder permissions to apache user
chown -R $APACHEUSER $WEBROOT

#add local site to the hosts file
echo "127.0.0.1\t$SERVERNAME" >> /etc/hosts

#set up apache vhost
vhost="NameVirtualHost *:80\n\n"
vhost=$vhost"<Directory '$WEBROOT'>\n"
vhost=$vhost"\tOptions Indexes FollowSymLinks MultiViews\n"
vhost=$vhost"\tAllowOverride All\n"
vhost=$vhost"\tOrder allow,deny\n"
vhost=$vhost"\tAllow from all\n"
vhost=$vhost"</Directory>\n\n"

vhost=$vhost"<VirtualHost *:80>\n"
vhost=$vhost"\tDocumentRoot $WEBROOT\n"
vhost=$vhost"\tServerName $SERVERNAME\n"
vhost=$vhost"\tDirectoryIndex index.php\n" 
vhost=$vhost"</VirtualHost>"

echo $vhost > $VHOSTPATH

echo "restarting apache"
apachectl -k graceful

echo "Your site is ready. Navigate to http://$SERVERNAME in your web browser"
