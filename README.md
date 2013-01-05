wpinstall_osx
=============
Bash script that installs a brand new WordPress site on your Mac. The script will create a hosts entry for you and an Apache vhost.


Usage
=============
```bash
sudo ./wpinstall.sh
```


Parameters
=============
You will be prompted to input some parameters for your local WordPress site to work

1. Your webroot directory?

2. vhost file path: (i.e. /etc/apache2/users/mysite.conf)

3. Server name? DO NOT include http:// (i.e. mysite.dev)

4. User apache runs under? (i.e. www or johnsmith)

5. Navigate to http://mysite.dev or whatever server name you specified in Step 3 and complete the wordpress installation


Notes
=============
* This script downloads the latest WordPress tarball from wordpress.org.  I think WordPress has a limit of 3-5 pull requests in a given timeframe so try to get the installation right the first time.  If you don't, you might have to modify the script and comment out the line that downloads the WordPress tarball.  This is a utility script that I use for myself and it works like a champ.  Feel free to fork and modify.
* The script will append '127.0.0.1 servername' into your hosts file each time you run it.  If your first attempt fails for some reason, cleanup your hosts file before running it again.
Prerequisites
=============
1. wget.  I don't think wget is installed on all OS X systems by default.  The best way to install it is to run:
```bash
brew install wget
```

2. If you got an error you probably don't have homebrew installed. Try installing homebrew by running:
```ruby
ruby -e "$(curl -fsSkL raw.github.com/mxcl/homebrew/go)"
```

Then retry
```bash
brew install wget
```

And you should be good to go
