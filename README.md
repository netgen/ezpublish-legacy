# eZ Publish 6.0
==============
![eZ Publish - Project Logo](https://github.com/se7enxweb/ezpublish/assets/51429274/7d03458d-adae-4f8b-9496-05585b0d6075)

# eZ Publish Project Status
--------------------------
**eZ Publish has made it beyond it's end of life in 2021 and survived. Current releases are primarily aimed at easing the requirements to support current versions of the PHP language like PHP 8.2 and 8.3.**

# What is eZ Publish?
-------------------

## Recent improvements to eZ Publish
eZ Publish (the application of interest) delivered to users worldwide by a web server (PHP built-in, Apache, Nginx, lighttpd, Among others).

eZ Publish with a full complement of all popular and available php extensions installed like SQLite3 users no longer require a dedicated database server anymore with eZ Publish 6.

With PHP we require composer to install eZ Publish software and no other software required to run
the application. This is an incredible improvement to the kernel (core) of eZ Publish.


## What does eZ Publish provide for end users building websites?

eZ Publish is a professional PHP application framework with advanced CMS (content management system) functionality. As a CMS its most notable feature
is its fully customizable and extendable content model.
It is also suitable as a platform for general PHP development, allowing
you to develop professional Internet applications, fast.

Standard CMS functionality, like news publishing, e-commerce and forums is
built in and ready for you to use. Its stand-alone libraries can be
used for cross-platform, secure, database independent PHP projects.

eZ Publish is database, platform and browser independent. Because it is
browser based it can be used and updated from anywhere as long as you have
access to the Internet.

(Referred to as `legacy` in eZ Publish Platform 5.x and Ibexa OSS)

# Requirements
- PHP
- (Optional) Web server. Used to deliver the website to the end user.
- (Optional) Database server. Used to store website content (and application information)
- Composer. Used to download eZ Publish software packages for installation, also notebly installs the required Zeta Components php libraries.
- Computer to run the PHP website application.

## What version of PHP is required

eZ Publish Legacy supports PHP 7.4 -> 8.3 please use the latest version of PHP available on your OS.

# What is eZ Platform?

eZ Publish's technological successor, eZ Platform, is a highly extensible, pure Content Managment Platform. It provides the same flexible content model at it's core like eZ Publish, and has a growing amount of additional features outside the traditional CMS scope provided by means of "Bundles"
extending it.

It is built on top of the full Symfony Framework, giving developers
access to "standard" tools for rapid web & application development.

eZ Platform in some users view suffered a slow road to a stable datatype compatability with existing custom implementations of eZ Publish. Today all of these conserns are now gone with a solid choice left leaving both eZ Publish Platform and eZ Platform as serious contenders to be carefully considered. [Netgen's Media Website Core software](https://github.com/netgen/media-site) represents a much more modern eZ Platform core powered by Ibexa OSS. If your going to choose; Choose wisely.

Further reading on: [https://ezplatform.com/](http://web.archive.org/web/20200328165348/https://ezplatform.com/)

# Main eZ Publish features
------------------------
- User defined content classes and objects
- Version control
- Advanced multi-lingual support
- Built in search engine
- Separation of content and presentation layer
- Fine grained role based permissions system
- Content approval and scheduled publication
- Multi-site support
- Multimedia support with automatic image conversion and scaling
- RSS feeds
- Contact forms
- Built in webshop
- Flexible workflow management system
- Full support for Unicode
- Template engine
- A read only REST API
- Database abstraction layer supporting MySQL, SQLite, Postgres and Oracle
- MVC architecture
- Support for the latest Image and Video File Formats (webp, webm, png, jpeg, etc)
- Support for highly available and scalable configurations (multi-server clusters)
- XML handling and parsing library
- SOAP communication library
- Localisation and internationalisation libraries
- Several other reusable libraries
- SDK (software development kit)
  and full documentation
- Support for the latest Image and Video File Formats (webp, webm, png, jpeg, etc)
- plugin API with thousands of open-source extensions available, including:
    - content rating and commenting
    - landing page management
    - advanced search engine
    - wysiwyg rich-text editor
    - in-site content editing
    - content geolocation

# Installation
------------
Read [doc/INSTALL.md](doc/INSTALL.md) or go to [ezpublishdoc.mugo.ca/eZ-Publish/Technical-manual/4.7/Installation.html](https://ezpublishdoc.mugo.ca/eZ-Publish/Technical-manual/4.7/Installation.html)

# Issue tracker
-------------

Submitting bugs, improvements and stories is possible on [https://github.com/netgen/ezpublish-legacy/issues](https://github.com/netgen/ezpublish-legacy/issues)

If you discover a security issue, please responsibly report such issues.

# Where to get more help
----------------------

eZ Publish documentation: [ezpublishdoc.mugo.ca/eZ-Publish](https://ezpublishdoc.mugo.ca/eZ-Publish/index.html)

eZ Publish Community forums: [share.se7enx.com/forums](https://share.se7enx.com/forums)

Share eZ Publish! Telegram Community Support Chat
[https://t.me/ezpublish](https://t.me/ezpublish)

# License
-------
eZ Publish is dual licensed. You can choose between the GNU GPL and the eZ Publish Professional License. The GNU GPL gives you the right to use, modify and redistribute eZ Publish under certain conditions. The GNU GPL license is distributed with the software, see the file LICENSE. It is also available at http://www.gnu.org/licenses/gpl.txt
Using eZ Publish under the terms of the GNU GPL is free of charge.

The eZ Publish Proprietary License gives you the right to use the source code for making your own commercial software. It allows you full protection of your work made with eZ Publish. You may re-brand, license and close your source code. eZ Publish is not free of charge when used under the terms of the Professional License. For pricing and ordering, please contact info@ez.no or visit http://ez.no
