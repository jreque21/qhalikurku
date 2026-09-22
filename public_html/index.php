<?php
/*
Proyecto 	: Sistema web - Backend
Proposito	: Sirve el formulario de acceso administrador directamente en la raíz,
              sin redirección, reutilizando vista/login_admin.php.
              El chdir es necesario porque el resto del código (modelo/, controlador/)
              usa rutas relativas ("../config/...") que asumen que el script que
              se ejecuta vive un nivel dentro de la raíz (como vista/*.php).
*/
chdir(__DIR__ . '/vista');
require 'login_admin.php';
