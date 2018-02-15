This repository that is based off of the following code drop:

simformteam-trademade-backend-db588995989a.zip

This code drop did not contain a .git file so we don't have any prior history.

Notes on Chat / Push Notifications

Based on what our past developers told us, once all certs, PEM files, and iOS are updated, notifications should start working. But chat is a deeper issue. This is what we know about chat--here's a message from our older developer: 

"Actually, i had worked this approx 1.5 years ago or older. So step by step exact i can not remember. I did it with the help of google and also 90% help from Ejabberd standard installation document. 

This document has listed all the configuration with very good step. When i had installed that time Ejabberd has approx version 14 and now they are on version 17 around. 

So there must be very good updates in this.  One more things we are using Ejabberd Community Edition. So it has few limitation compare to enterprise version. 

And among that, Push notification was not ready to use solution that time in community edition. So, we had taken reference for  one Module with little learning in Erlang language. 

Configuration : https://docs.ejabberd.im/admin/installation/
Offline message : https://github.com/raelmax/mod_http_offline  (This ERlang module trigger one curl post request to our php script which is actually stand for send push notification.)  Curl Path :http://52.8.137.106/ejabberd/1_0/ejabberdpost.php
Directory path : /var/www/html/ejabberd/1_0/ejabberdpost.php (this is pure php script written heere)