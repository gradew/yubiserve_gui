Information
===========

This is a web application that will help perform the exact same operations as with db_conf.py, only from a web page, making it easier for sysadmins to get a quick glance at the status of their YubiFarm ;-)

Created by Julien Derivière (contact@gradew.net)

Installation
============
You will need the following:
- jquery.min.js, placed in the js/ subfolder
- the complete Twitter Bootstrap package, placed in the bootstrap/ subfolder

Configuration
=============
config.php-dist contains all the information you will need to adjust to suit your needs. Don't forget to rename/copy it to "config.php" first!

Then, using the webserver of your choice, configure a virtual host that will point to the directory you've checked out yubiserve_gui to.
