###  Installation

**Command line examples represent a linux debian/ubuntu installation**

1. php version 7.3  or 7.4 or above in server
1. php-zip  `apt-get install php-zip` or  `apt-get install php7.4-zip`
1. php-xml  `apt-get install php-xml` or  `apt-get install php7.4-xml`
1. Unzip nhdp.zip into Web-server path. e.g. `unzip nhdp.zip` e.g. `/var/www/html/`
1. Create Folder uploads  e.g. `/var/www/html/nhdp/uploads` 
1. Secure the `/var/www/html/nhdp/` folder with `.htaccess` and `.htpasswd`  e.g. `/var/www/html/nhdp/.htaccess`

```apacheconf
AuthUserFile /etc/apache2/.htpasswd
AuthName "Please Enter Password"
AuthType Basic
Require valid-user
```

/etc/apache2/.htpasswd

```apacheconf
user:encrypted-pass
```

*  Server will be accessible under https://my.domain/nhdp/upload.php

#### Create Distribution file (only for release by the developer)
cd ~/projects/
zip -r /tmp/nhdp.zip  upload/ -x "upload/.git/*" "upload/docxtotei/.git/*" "upload/.gitmodules" "upload/.gitignore" "upload/.idea/*" "upload/docxtotei/.idea/*" 



sudo apt-get install texlive-full texlive-latex-base texlive-latex-extra texlive-latex-recommended texlive-generic-extra



