1) install composer
https://getcomposer.org/download/

2) instalar in instalador de laravel
composer global require "laravel/installer"

3) clonar el sitio
git clone https://github.com/harregoces/MicroSitio.git

4) en la linea de comando acceder al directorio /sitio y ejecutar
composer install

5) colocar las credenciales de acceso en
Archivo : sitio/config/database.php
Linea 43

y colocar las mismas credenciales de BD en el archivo .env



6) crear la tabla
php artisan migrate



7) redireccionar el host
127.0.0.1       micrositio.com

8) setear el VHOST
<VirtualHost *:80>

    DocumentRoot "MicroSitio\sitio"
    <Directory "MicroSitio\sitio">
        Options +Indexes +FollowSymLinks
        DirectoryIndex index.php
        Order allow,deny
        Allow from all
        AllowOverride All
    </Directory>

    ServerName micrositio.com:80

</VirtualHost>

9) acceder a la URL
localhost/merchantid/{merchantid}
localhost/merchantid/172
