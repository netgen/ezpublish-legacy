## eZ Publish 4 INSTALL


Requirements
------------

### Apache version:

   The latest version of the 1.3 branch.
   or
   Apache 2.x run in "prefork" mode.

### PHP version:

   The latest version of the 5.2 branch is strongly recommended.

   Note that you will have to increase the default "memory_limit" setting
   which is located in the "php.ini" configuration file to 64 MB or larger. (Don't
   forget to restart Apache after editing "php.ini".)

   The date.timezone directive must be set in php.ini or in
   .htaccess. For a list of supported timezones please see
   http://php.net/manual/en/timezones.php

### Composer version:

   The latest version of the 2.x branch is recommended.

### Database server:
   MySQL 4.1 or later (UTF-8 is required)
   or
   PostgreSQL 8.x
   or
   Oracle 11g

### eZ Components:
   Enterprise edition includes a version of eZ Components that is tested and certified
   with this version of eZ Publish-

   The community edition requires the latest stable release of Zeta Components.
   To install this, you also need to use composer.


GitHub Installation Guide
------------------

- Clone the repository

`git clone git@github.com:se7enxweb/ezpublish.git;`

- Install eZ Publish required PHP libraries like Zeta Components and eZ Publish extensions as specified in this project's composer.json.

`cd ezpublish; composer install;`

For the rest of the installation steps you will find the installation guide at https://ezpublishdoc.mugo.ca


Composer Installation Guide
------------------

- Download the package from se7enxweb/ezpublish

`mkdir ezpublish;`

- Install eZ Publish required PHP libraries like Zeta Components and eZ Publish extensions as specified in this project's composer.json.

`cd ezpublish; composer require se7enxweb/ezpublish:v6.0.0;`

For the rest of the installation steps you will find the installation guide at https://ezpublishdoc.mugo.ca
