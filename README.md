# FOSSIL BACKEND (Pringles edition)
>Used for emulating backend of cheats

### Information
Created from reverse engineering responses of a PUBG cheat.<br>
Improved and created to scale for future projects which might need a proper backend with API like responses.<br>
Removed multiple vulnerable vectors such as SQLi.<br>

### Notice
Remember to change [php.conf](https://i.htp.re/CrackingLife/Fossil-Backend/blob/master/NGINX/money/php.conf) based on what you have installed.<br>
Make sure to use the NGINX configuration i've placed in the [NGINX](https://i.htp.re/CrackingLife/Fossil-Backend/tree/master/NGINX) folder.<br>
We are using nginx's rewrite to deal with API'like behavior.<br>

# Installing

### Setting up nginx
Installing should be pretty straigh forward, just place all the files inside NGINX to your `/etc/nginx` folder.<br>
It's important you always backup your insallation if you're dealing with something new, so backup that nginx fold right now.<br>

### `commands`
```shell
cp -r NGINX/ /etc/nginx/
nginx -t #Verify everything is fine
service nginx reload
```

### Setting up website
Unless you want to edit your vhost for nginx, create the folders /sites/cheat/ .<br>
Place all files in [Website](https://i.htp.re/CrackingLife/Fossil-Backend/tree/master/Website) into /sites/cheat/<br>
Make sure to edit [config.inc.php](https://i.htp.re/CrackingLife/Fossil-Backend/blob/master/Website/includes/config.inc.php) to match your SQL server.<br>

### `commands`
```shell
cp -r Website/ /sites/cheat && chmod 755 /sites -R
nano /sites/cheat/includes/config.inc.php # Edit accordingly
```


### Setting up database
I've made it simple and included the [scheme.sql](https://i.htp.re/CrackingLife/Fossil-Backend/blob/master/Database/scheme.sql) in here.<br>
To import just use <br>
### `commands`

```shell
mysql -u user -p < scheme.sql
# Alternative if you are already logged in
mysql> source .scheme.sql

```

### HHVM
Be sure to have hh_server watch over the working directory
```shell
hh_server -d /sites/cheat/public/
```