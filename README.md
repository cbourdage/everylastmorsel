Every Last Morsel 
=========================

RELEASE NOTES
---------------
ELM Version 0.0.21.
Released on July 22, 2012.

INSTALLATION
---------------------------
1. Clone Every-Last-Morsel in your apache DocumentRoot. Using symbolic links can be iffy since we're using a Virtual Host.
1. Copy application/configs/sample.application.ini to application/configs/application.ini.
2. Set up resources.db.params according to your database setup.
3. Create an SQL database with the same name as resources.db.params.dbname.
4. Set up apache similar to the following:

/etc/apache2/httpd.conf

```
DocumentRoot "/Users/username/Sites/"

<Directory />
    Options None
    AllowOverride None
    Order Deny,Allow
    Deny from all
</Directory>

# Same as DocumentRoot
<Directory "/Users/username/Sites/">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>

# Uncomment this line from the bottom of the file
Include /private/etc/apache2/extra/httpd-vhosts.conf

/etc/apache2/httpd-vhosts.conf

NameVirtualHost *:80

# NameOfYourComputer can be found by `hostname`
# Directory is also the same as DocumentRoot
<VirtualHost *:80>
    ServerName NameOfYourComputer
    DocumentRoot "/Users/username/Sites/"
    <Directory "/Users/username/Sites/">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>

# This is the virtual host for ELM. Make sure the ELM directory is within the DocumentRoot.
<VirtualHost *:80>
    ServerName elm.localhost
    DocumentRoot "/Users/atul/Dropbox/Development/Sites/Every-Last-Morsel/http/"
    <Directory "/Users/atul/Dropbox/Development/Sites/Every-Last-Morsel/http/">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

5. Add the following entires to /etc/hosts

```
127.0.0.1 localhost
127.0.0.1 elm.localhost	localhost
```

6. Hit the root url (http/index.php) to perform install and upgrades.



UPGRADES
---------------------------

All data updates should be handled by upgrade scripts. All upgrade scripts live in
the application/data/db directory and correspond to the app version defined in the
application.ini.

If there is an upgrade that contains data, create the necessary upgrade file
data/upgrade-0.0.1-0.0.2.php and then set the application.ini version to the desired
version (0.0.2 in our example).

