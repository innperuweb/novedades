# Proyecto PHP E-Commerce Modular

## I. Descripción General del Proyecto
Proyecto PHP estructurado tipo CodeIgniter (sin framework), orientado a evolucionar hacia una E-Commerce moderna y escalable. Basado en un entorno modular con separación de vistas, controladores, modelos y helpers. La versión actual entrega una maqueta visual completa lista para ser conectada con módulos funcionales (carrito, checkout, usuarios, panel administrativo, etc.), priorizando mantenibilidad y crecimiento futuro.

## II. Estructura de Carpetas
```
/application
 ├── /config        → Archivos de configuración general, base URL y base de datos.
 ├── /controllers   → Controladores principales (Home, Productos, Blog, etc.).
 ├── /models        → Modelos con acceso a base de datos.
 ├── /views         → Vistas HTML + PHP (maquetas originales).
 │    └── /partials → Secciones compartidas (head, header, footer, scripts).
 ├── /helpers       → Funciones globales (URLs, seguridad, sesiones).
/public
 └── /assets        → Archivos CSS, JS, imágenes, fuentes y multimedia.
index.php           → Punto de entrada principal (router).
.htaccess           → Configuración de rutas limpias.
README.md           → Documentación técnica del proyecto.
```

## III. Archivos Clave del Sistema
| Archivo | Descripción |
| --- | --- |
| `index.php` | Controlador frontal. Recibe las rutas y carga los controladores correspondientes. |
| `/application/config/config.php` | Define constantes globales y `BASE_URL`. |
| `/application/config/database.php` | Clase PDO para conexión segura con MySQL. |
| `/application/helpers/url_helper.php` | Funciones `base_url()` y `asset_url()` para gestionar rutas absolutas. |
| `/application/helpers/security_helper.php` | Funciones de sanitización y protección CSRF. |
| `/application/helpers/session_helper.php` | Manejo de sesiones y verificación de login. |
| `/application/models/ProductoModel.php` | Ejemplo de modelo con estructura de consultas PDO. |

## IV. Flujo de Rutas
El archivo `index.php` actúa como router central:
```
/index.php → Lee $_GET['route']
           → Llama al controlador correspondiente
           → Carga la vista asociada desde /application/views/
```

Rutas de ejemplo ya mapeadas en la estructura:
- `/inicio` → `HomeController@index()`
- `/productos` → `ProductosController@index()`
- `/producto/detalle` → `ProductosController@detalle()`
- `/checkout` → `CheckoutController@index()`
- `/blog` → `BlogController@index()`
- `/admin` → `AdminController@login()`

## V. Sistema de Seguridad y Sesiones
La plataforma está configurada con medidas de seguridad básicas para futuras integraciones:
- `session_start()` inicializado globalmente.
- Cabeceras de seguridad agregadas (X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, etc.).
- Helpers disponibles:
  - `sanitize()` → Limpieza de entradas.
  - `csrf_token()` / `verify_csrf()` → Protección de formularios.
  - `is_logged_in()` / `require_login()` → Control de acceso.
- Configuración gestionada desde `/application/config/security.php`, incluyendo parámetros para sesiones seguras.

## VI. Base de Datos
El sistema incorpora la clase `Database` (PDO) para la conexión a MySQL:
```php
$pdo = Database::connect();
$stmt = $pdo->query("SELECT * FROM productos");
```
Actualmente no existen tablas creadas por defecto, pero la infraestructura está lista para consultas y operaciones CRUD, aisladas a través de modelos dedicados.

## VII. Helpers Disponibles
**`url_helper.php`**
- `base_url($path)` → Retorna la URL absoluta del proyecto.
- `asset_url($path)` → Retorna rutas absolutas de archivos dentro de `/public/assets/`.

**`security_helper.php`**
- `sanitize($input)` → Limpia datos de entrada.
- `csrf_token()` / `verify_csrf($token)` → Protege formularios mediante tokens.

**`session_helper.php`**
- `set_session($key, $value)`
- `get_session($key)`
- `is_logged_in()`
- `require_login()`

## VIII. Extensión del Sistema (Futuras Fases)
El proyecto está preparado para extenderse con nuevos módulos siguiendo la arquitectura MVC.

**Buscador de productos**
1. Crear `SearchController.php` en `/application/controllers/`.
2. Agregar método `buscar()` que interactúe con `ProductoModel`.
3. Crear vista `resultados_busqueda.php` en `/application/views/`.
4. Conectar la ruta `/buscar` desde `index.php`.

**Panel Administrativo (`/admin`)**
1. Crear `AdminController.php` en `/application/controllers/`.
2. Implementar métodos `login()` y `dashboard()`.
3. Crear vistas `admin/login.php` y `admin/dashboard.php`.
4. Reutilizar helpers de sesión y seguridad para proteger el acceso.

**Módulo de Usuarios**
1. Crear `UsuarioController.php` y modelo correspondiente.
2. Añadir funcionalidades de registro, login y perfil.
3. Configurar vistas en `/application/views/usuarios/`.
4. Integrar verificación CSRF y sanitización de datos.

**Carrito y Checkout**
1. Crear `CarritoController.php` y `CheckoutController.php`.
2. Usar sesiones para almacenar productos seleccionados.
3. Definir vistas para carrito y checkout.
4. Integrar cálculos de totales e impuestos en modelos/servicios.

**Reportes**
1. Extender los modelos para recopilar estadísticas.
2. Generar PDF o Excel utilizando librerías externas.
3. Proteger los endpoints con helpers de autenticación.

## IX. Recomendaciones de Producción
- Activar `session.cookie_secure = 1` cuando se despliegue bajo HTTPS.
- Mover las credenciales de base de datos a variables de entorno (`.env`).
- Asegurar permisos de escritura en `/public/assets/uploads/` para cargas dinámicas.
- Evitar subir archivos `.sql` o `.zip` sensibles al servidor de producción.

## X. Autor / Mantenimiento
- **Desarrollado por:** Jared Conde Tantaleán
- **Empresa:** Innperuweb
- **Contacto:** [contacto@innperuweb.com](mailto:contacto@innperuweb.com)
- **Fecha:** Octubre 2025
- **Versión inicial:** 1.0.0
