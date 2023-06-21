## SMUS
Simple Managable Url Shortener 
<p>Demo site: </p>

> Demo creds,
> Username: `guest`
> Password: `guest`

## How to setup?
1. Import the `url.sql` to your phpmyadmin/mysql database
2. Put all the files to your `htdocs`
3. Go to the folder named `core` and open `config.php` fill it up with your credentials.
4. Login using default creds Username: `ninja` Password: `ninja123`
#### Note: In changing password you need to md5 hash it like this `ninja_linkz:new_password`

## How does shortener works without seeing parameter from url?
if you download this repository and go to the folder named `z` you will see a file named `.htaccess` and below are the content inside that `.htaccess`

```
RewriteEngine On
RewriteCond $1 !^(index\.php|images|robots\.txt)
RewriteRule ^(.*)$ ./index.php?code=$1 [L]
```
this means that using `.htaccess` to forward any subdirectory before /z/ subdirectory from the url to `index.php?code=` to be read as a code.

#### pls try/visit the demo site provided for further insight.
<hr>

## Preview
> ## **Login**
![image](https://github.com/abalesluke/SMUS/assets/108006281/eaaa1021-f733-4844-9392-7a98c90d6146)

<hr>

> ## **Dashboard**
![image](https://github.com/abalesluke/SMUS/assets/108006281/128c811b-a4b5-4b13-b7d2-a553bc38d223)

