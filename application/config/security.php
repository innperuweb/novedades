<?php

define('SESSION_NAME', 'novedades_sess');
define('SESSION_LIFETIME', 7200); // 2 horas

ini_set('session.name', SESSION_NAME);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
ini_set('session.use_strict_mode', 1);

