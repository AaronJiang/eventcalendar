eventcalendar
=============

A simple event calendar by PHP and Jquery 
1. Please set allowoverride to 'All' in your apache virtualhost, enable rewirte mode in apache, and make sure .htaccess file is accessable in your site path. The virtualhost configuration goes like this:

<VirtualHost *>
DocumentRoot /path/to/yoursite/public/
ServerName yousite.com 
</VirtualHost> 

2. Please create a new database and import eventcalendar.sql, make sure to config your databse connection in /sys/config/db-cred.inc.php
