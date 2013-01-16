wordpress-install
=============
PHP script that installs a brand new WordPress site on your Mac. The script will: 

1. Donwload the latest version of WordPress from wordpress.org

2. Set up the site directory structure in a directory you specify 

3. Create a hosts entry in your hosts file

4. Create an Apache vhost for your local site. 

5. Create a MySQL database

6. Install WordPress on your local machine!

DO NOT use this on production systems, this script will only work on your dev machine. It has only been tested on OS X Mountain Lion, but it will probably work on other versions of OS X and also Linux.


Usage
=============
```php
sudo ./wpinstall.php
```
You must run this as root.

Parameters
=============
You will be prompted to input some parameters for your local WordPress site to work

1. Your webroot directory?

2. vhost file path: (i.e. /etc/apache2/users/mysite.conf)

3. Server name? DO NOT include http:// (a valid entry would be: mysite.dev)

4. User apache runs under? (i.e. www or yourusername)

5. MySQL Database name?

6. MySQL host [127.0.0.1]

7. MySQL user [root]

8. MySQL password

9. Navigate to http://mysite.dev or whatever server name you specified in Step 3 and you should see your new WordPress site ready to go.

You can also log into your WordPress admin using the default admin/admin credentials.  I suggest you change this as soon as you install the site. You could also modify this script to allow for different database prefix and site title options, but I generally leave those to default values.

Notes
=============
* This script downloads the latest WordPress tarball from wordpress.org.  I think WordPress has a limit of 3-5 pull requests in a given timeframe so try to get the installation right the first time.  If you don't, you might have to modify the script and comment out the line that downloads the WordPress tarball.  This is a utility script that I use for myself and it works like a champ.  Feel free to fork and modify.
* The script will append '127.0.0.1 servername' into your hosts file each time you run it.  If your first attempt fails for some reason, cleanup your hosts file before running it again.

Prerequisites
=============
* wget.  I don't think wget is installed on all OS X systems by default.  You can check this by simply running
```bash
wget
```
If you don't have it, the best way to install it is to run:
```bash
brew install wget
```

* If you got an error you probably don't have homebrew installed. Try installing homebrew by running:
```ruby
ruby -e "$(curl -fsSkL raw.github.com/mxcl/homebrew/go)"
```

Then retry
```bash
brew install wget
```

* Finally, you will need MySQL. If you don't already have it, install it with homebrew:
```bash
brew install mysql
```
And you should be ready to install WordPress.
