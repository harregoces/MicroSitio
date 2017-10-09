1) crear la tabla

CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcliente` int(11) NOT NULL,
  `gtm_code` text COLLATE utf8mb4_unicode_ci,
  `ga_code` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gtmaccount` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_idcliente_unique` (`idcliente`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

2) colocar las credenciales de acceso en
Archivo : sitio/config/database.php
Linea 43

3) acceder a la URL
localhost/merchantid/{merchantid}

