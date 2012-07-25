Every Last Morsel Read Me 

RELEASE NOTES
---------------
ELM Version 0.0.21.
Released on July 22, 2012.

INSTALLATION
---------------------------

* Database setup - requires create db
* Application setup - application/config/application.ini contains all application 
specific settings based on environment. Update database settings and url information.
* Hit the root url to perform install and upgrades.


UPGRADES
---------------------------

All data updates should be handled by upgrade scripts. All upgrade scripts live in
the application/data/db directory and correspond to the app version defined in the
application.ini.

If there is an upgrade that contains data, create the necessary upgrade file
data/upgrade-0.0.1-0.0.2.php and then set the application.ini version to the desired
version (0.0.2 in our example).

