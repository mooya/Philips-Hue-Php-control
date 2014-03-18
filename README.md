Philips-Hue-Php-control
=======================

Philips Hue control script with Php &amp; MySQL

With this Php script you can control your Philips Hue.

See screenshots at: http://www.internovus.nl/portfolio/29/philips_hue_php_control_script.html

You can create your own scenes and change the state of each light.

Through a cronjob you can put lights on X minutes before or after sunset.


Files:
------------------------------------------------
includes\sql_settings.php > put your MySQL username and password in here

includes\settings.php > set the options used in the script (such as your Bridge IP, and Bridge Hash)

When a hash does not exist on the bridge you can create the hash in the automatically launch wizard.

See the database.sql file for the database layout.

--------------
The script uses JQuery, JQuery UI and jQuery UI Touch Punch > https://github.com/furf/jquery-ui-touch-punch

In first instance the script was created for personal use and testing purposes, but now more people can enjoy this. 
