Existen dos modalidades de instalar el proyecto. Una Manual y otra por medio de Vagrant (método preferido).

# Instalaciòn manual.

### Prerequisitos:
   - GIT
   - PHP
   - MySql

1. Descargar e instalar composer.
 Composer es una herramienta para gestionar dependencias en PHP.
 Para instalar Composer en Windows, debemos descargarlo de su página oficial [Composer] y en la sección Windows Installer descargar Composer-Setup.exe.

2. Instalar laravel.
Una vez instalado el composer, vamos a la lìnea de comandos y ejecutar
    ```sh
    $ composer global require "laravel/installer"
    ```

3. Clonar el repositorio del sitio.
3.a Crear el directorio que contendrá el còdigo fuente del sitio web.
3.b Ir a la lìnea de comandos y nos posicinamos dentro del directorio apenas creado.
3.c Descargamos el còdigo fuente del sitio web
    ```sh
    $ git clone https://github.com/harregoces/MicroSitio.git .
    ```

4. Instalar las librerìas.
En la linea de comando acceder al directorio creado en el punto 3. y ejecutar
    ```sh
    $ cd sitio
    $ composer install
    ```

5. Configurar las credenciales de acceso al data base.
5.a Editar el archivo
    ```sh
    sitio/config/database.php
    ```
    y en la secciòn ```mysql```, si es necesario, modificar las propiedades username y password con los valores correctos para acceder al database
    ```php
    'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'micrositio'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', 'xxxxxxxx'),
            ...
        ],
    ```

    5.b. Editar el archivo
    ```sh
    sitio/.env
    ```
    Verificar las propiedades database, username y password contengan los valores correctos para crear la conexiòn con el database.
    ```sh
    DB_DATABASE=micrositio
    DB_USERNAME=root
    DB_PASSWORD=xxxxxxxx
    ```


6. Crear las tablas.
Desde la linea de comandos, en la raìz del directorio del sitio web, ejecutar el comando.
    ```sh
    $ php artisan migrate
    ```

7. Redireccionar el host.
    Editar  el archivo hosts y agregar la siguiente linea.
    ```sh
    127.0.0.1       micrositio.com
    ```
    En windows, el archivo hosts generalmente se encuentra en ```Windows\System32\Drivers\etc\hosts``` mientras que en sistemas linux se encuentra en ```/etc/hosts```


8. Configurar el virtual VHOST
Agregar el virtual host al web server utilizado.
Si se usa Apache, il virtual host deberìa ser como el siguiente:
    ```sh
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
    ```
    Si se usa nginx:
    ```sh
    server {
        listen 80;

        server_name micrositio.com;

        root /shared/development/code;
        index  index.php index.html index.htm;

        location / {
                proxy_http_version 1.1;
                proxy_set_header Upgrade $http_upgrade;
                proxy_set_header Connection keep-alive;
                proxy_set_header Host $host;
                proxy_cache_bypass $http_upgrade;
               try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
                try_files $uri =404;
                fastcgi_split_path_info ^(.+\.php)(/.+)$;
                fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }
    }
    ```
    reiniciar el web server luego de haber agregado el virtual host

9. Verificar la instalaciòn.
Para verificar la instalaciòn del sitio web, navegar a la siguiente direcciòn en el browser de preferencia
http://micrositio.com/merchantid/{merchantid}
http://micrositio.com/merchantid/172


# Instalaciòn utilizando Vagrant.

Vagrant es una herramienta para desarrolladores que facilita la creación de entornos virtuales para desarrollo.
En Vagrant podemos instalar y configurar software dentro de una máquina virtual para asì simular en el servidor en el que se alojará la aplicación Web.

### Prerequisitos:

   - GIT
   - Còdigo fuente del sitio previamente clonado
     ```sh
     $ git clone https://github.com/harregoces/MicroSitio.git .
     ```

1. Descargar e instalar la ùltima versiòn de [VirtualBox](https://www.virtualbox.org/wiki/Downloads).
Asegúrese de descargar el instalador o paquete adecuado para su plataforma.
Instale el paquete usando el procedimiento estàndard de su sistema operativo manteniendo las opciones de configuraciòn predeterminadas
1.a Si se instala en Windows, hacer doble clic en el archivo de instalación y seguir las instrucciones.
1.b Si se instala en una Mac, abrir el archivo DMG y arrastrarlo hacia tu carpeta de aplicaciones.


2. Descargar e instalar la ùltima versiòn de [Vagrant](https://www.vagrantup.com/downloads.html).
Asegúrese de descargar el instalador o paquete adecuado para su plataforma.
Instale el paquete usando el procedimiento estàndard de su sistema operativo manteniendo las opciones de configuraciòn predeterminadas
1.a Si se instala en Windows, hacer doble clic en el archivo de instalación y seguir las instrucciones.
1.b Si se instala en una Mac, abrir el archivo DMG y arrastrarlo hacia tu carpeta de aplicaciones.


## Configuraciòn

1. Create y configurar la màquina virtual sobre la que se ejecutarà el sitio web:
1.a. Posicionarse en el directorio raìz del còdigo fuente.
1.b. ingresar al directorio Vagrant
     ```sh
     $ cd vagrant
     ```
    1.c. Iniciar la màquina virtual
     ```sh
     $ vagrant up
     ```
     La primera vez que se ejecuta este comando, se crea la màquina virtual linux sobre la que se ejecutarà el sitio web e instalarà todos los programas y dependencias que el sitio requiere (PHP7, Composer, mysql, etc).
     Luego la ejecuciòn de este comando sòlo iniciarà la màquina virtual, es decir iniciarà el servidor host de la aplicaciòn.

     Para detener la ejecuciòn de la màquina virtual se usa el comando.
     ```sh
     $ vagrant halt
     ```

2. Redireccionar el host.
   Editar  el archivo hosts y agregar la siguiente linea.
    ```sh
    192.168.39.103       micrositio.com
    ```
    En windows, el archivo hosts generalmente se encuentra en ```Windows\System32\Drivers\etc\hosts``` mientras que en sistemas linux se encuentra en ```/etc/hosts```

3. Verificar la instalaciòn.
Para verificar la instalaciòn del sitio web, navegar a la siguiente direcciòn en el browser de preferencia
http://micrositio.com/merchantid/{merchantid}
Ejemplo: http://micrositio.com/merchantid/172

4. Para leer los datos del google tag manager account se usa la url
http://micrositio.com/getmerchantaccountbyid/{merchantid}
Ejemplo: http://micrositio.com/getmerchantaccountbyid/172

Si GTM está instalado, se retorna un JSON como el siguiente:

{
    "code": "INSTALLED",
    "container": {
        "id": 5,
        "idcliente": 202,
        "gtm_code": "{\"access_token\":\"ya29.Glv2BLnupQgGLzFF_UxZ_dkK3-d-g_srXjo3GzOP0ajCJw4J0i5sbSUYQ1oqpR1qvuozwc_xruuhjtk0o80GKe7_62wuazL4GlWHalNyNno4Czlsw9WrY3SjUWrJ\",\"token_type\":\"Bearer\",\"expires_in\":3600,\"refresh_token\":\"1\\\/tBMZwfL0mmGUjd308Vmi9UTkKG4yLpT18gDK59Ujm6NhSMY8bZPXCjFtzBXDjqPm\",\"created\":1509519539}",
        "ga_code": "\"{\\\"access_token\\\":\\\"ya29.Glv2BBA35fVUBBKgbcYx2nT7u1hIiL2go0D37XDVozsjWFrihWiZJsWkA0X41_N687GkQgSWfiufcAywKG9u5EF9citOLnpC-9F692yZbTdnBtMlNhliV-2CDAEl\\\",\\\"token_type\\\":\\\"Bearer\\\",\\\"expires_in\\\":3600,\\\"refresh_token\\\":\\\"1\\\\\\\/2blSk1eGCSD_XH8O7VzsYWOo4dxiBJ8suodMAYNXdeU\\\",\\\"created\\\":1509519559}\"",
        "gtmaccount": "{\"containerId\":\"7728507\",\"containerName\":\"nutmeg.com\",\"containerPath\":\"accounts\\\/1607827098\\\/containers\\\/7728507\",\"publicId\":\"GTM-TDNHJSS\",\"accountId\":\"1607827098\"}",
        "workspaceid": "accounts\/1607827098\/containers\/7728507\/workspaces\/8",
        "ga_account": "86175990",
        "ga_property": "UA-86175990-1",
        "ga_view": "132136873",
        "uat_id": "14",
        "created_at": null,
        "updated_at": null
    }
}

En caso de que Google Analytics esté instalado, dentro del JSON se puede leer la cuenta de Analytics instalada, dentro de la propiedad ga_property.
 
Si GTM no está instalado, el servicio devolverá el siguiente JSON:
  
{"code":"NOT_INSTALLED","container":null}

5. Para acceder a todas las gráficas disponibles, las url deben ser de la siguiente manera:
http://micrositio.com/dashboard/merchantid/{merchantid}/
Ejemplo: http://micrositio.com/dashboard/merchantid/200/

6. Para acceder a un gráfico en particular, para por ejemplo embeberlo como iframe, se puede acceder a través del siguiente link

http://micrositio.com/dashboard/merchantid/{merchantid}/type/{tipo_grafico}
Ejemplo: http://micrositio.com/dashboard/merchantid/200/type/sessionspie

7. Los tipos de gráficos disponibles son:

* sessionspie
* sessionspercountrypie
* sessionsperbrowser
* thisweeklastweekbysessions
* activeusers
* thisyearlastyearbyusers
* topbrowsersbypageviewpie
* topcountriesbysessionspie
* randomcharts

8. En la siguiente URL es posible ver un ejemplo de gráficos embebidos en iframes:

http://micrositio.com/dashboardiframes/merchantid/200

9. Si se requiere redireccionar el usuario directamente a los workflows de OAuth2 de GTM, se puede redireccionar a través de la siguiente URL:

http://micrositio.com/installplugingtm/merchantid/{merchantid}/?returnurl={returnurl}
Ejemplo: http://micrositio.com/installplugingtm/merchantid/200/?returnurl=http%3A%2F%2Fmicrositio.com%2Fmerchantid%2F202%2F%3Freturned

9. Una vez instalado GTM, Si se requiere redireccionar el usuario directamente a los workflows de OAuth2 de Google Analytics, se puede redireccionar a través de la siguiente URL:

http://micrositio.com/installpluginga/merchantid/{merchantid}/?returnurl={returnurl}
Ejemplo: http://micrositio.com/installpluginga/merchantid/200/?returnurl=http%3A%2F%2Fmicrositio.com%2Fmerchantid%2F202%2F%3Freturned

10. Para desinstalar GTM y GA es posible acceder directamente al siguiente link
http://micrositio.com/uninstall/merchantid/{merchantid}/
Ejemplo: http://micrositio.com/uninstall/merchantid/202/