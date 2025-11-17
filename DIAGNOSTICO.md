## Diagnóstico General

### 1. Organización y estructura
- Se usa un patrón MVC ligero sin framework: `index.php` actúa como front-controller, auto-carga controladores/modelos y helpers comunes. Las vistas se renderizan con cabeceras/footers compartidos desde `application/views/partials/`.【F:index.php†L1-L110】【F:application/controllers/BaseController.php†L1-L38】
- Las rutas públicas están centralizadas en `application/config/routes.php`, mientras que el panel admin usa su propio router en `admin/index.php` con definiciones en `admin/routes.php`.【F:application/config/routes.php†L1-L35】【F:admin/index.php†L1-L120】
- La estructura de módulos está separada pero hay duplicación de helpers y lógica de ruteo entre público y admin, lo que aumenta mantenimiento (dos entrypoints y dos definiciones de rutas).【F:index.php†L12-L107】【F:admin/index.php†L1-L95】

### 2. Rutas y arquitectura
- Arquitectura MVC manual: controladores heredan de `BaseController` para incorporar layout y menú; modelos manejan PDO; helpers proveen utilidades. No hay middleware ni inyección de dependencias, lo que acopla controladores a helpers globales y superglobales.
- Rutas públicas incluyen catálogo, carrito, checkout, blog y secciones; `/admin` tiene rutas para autenticación, dashboard, órdenes, productos, categorías, clientes y usuarios. No existe versión API ni namespacing; las rutas se derivan de URL limpias mediante regex básicos (sin soporte de verbos ni middlewares).【F:application/config/routes.php†L1-L35】【F:admin/routes.php†L1-L77】
- Flujo general: `index.php` resuelve la ruta → instancia controlador → renderiza vista con parciales. Checkout consume sesión del carrito y redirige a `ver_orden` tras guardar datos; el panel admin sigue flujo similar desde `admin/index.php`.【F:index.php†L34-L110】【F:application/controllers/CheckoutController.php†L11-L205】

### 3. Panel /admin
- Router propio en `admin/index.php` con autoload dedicado y protección opcional por `auth` en cada ruta.【F:admin/index.php†L1-L105】【F:admin/routes.php†L1-L77】
- Controladores admin usan `AdminBaseController` que inyecta layouts y verifica CSRF en acciones que lo llaman explícitamente. Sin embargo, muchas rutas CRUD declaradas carecen de verificación explícita en los métodos correspondientes (p. ej. cambio de estado de órdenes o CRUD de productos depende de cada implementación).【F:application/controllers/admin/AdminBaseController.php†L1-L56】
- Manejo de sesión admin simple (`admin_auth_helper.php`) sin regeneración de ID ni restricciones de IP/UA; solo almacena datos básicos en `$_SESSION['ADMINSESS']`.【F:application/helpers/admin_auth_helper.php†L1-L48】

### 4. Base de datos
- Configuración PDO en `application/config/database.php` admite MySQL/pgsql/sqlsrv/sqlite, pero usa credenciales por defecto (`root` sin contraseña) y DSN en texto plano, sin uso de variables de entorno obligatorias ni cifrado. No hay capa de migraciones ni esquema versionado en el repositorio.【F:application/config/database.php†L1-L63】
- Modelos (p.ej. `ProductoModel`) usan consultas preparadas pero mezclan SQL directo y concatenado (uso de `RAND()` y `LIMIT` incrustado) que puede degradar rendimiento y dificulta portabilidad. No hay índices ni definiciones de tablas incluidas, por lo que no se puede validar normalización/relaciones.【F:application/models/ProductoModel.php†L11-L81】【F:application/models/ProductoModel.php†L66-L121】

### 5. Seguridad
- **CSRF ausente en checkout y carrito:** Acciones críticas (`CheckoutController::procesar`, `CarritoController::agregar/actualizar/eliminar`, etc.) no verifican tokens CSRF; basta un POST externo para manipular pedidos/carrito. Alto riesgo de CSRF/abuso de sesión.【F:application/controllers/CheckoutController.php†L37-L205】【F:application/controllers/CarritoController.php†L15-L196】
- **Validación superficial de entradas:** Helpers de sanitización usan `htmlspecialchars` y filtros básicos, pero no hay validación de dominio ni longitud en la mayoría de formularios; datos de contacto del checkout se insertan en sesión y BD sin normalizar ni escape adicional (aunque se usan sentencias preparadas). Riesgo medio de datos inconsistentes y XSS reflejado en vistas si no se escapan correctamente.【F:application/helpers/security_helper.php†L5-L70】【F:application/controllers/CheckoutController.php†L55-L205】
- **Gestión de sesión débil:** `SESSION_COOKIE_SECURE` deshabilitado por defecto, sin regeneración de ID tras login ni flags SameSite; helper de sesión expone set/get sin controles. Facilita fijación de sesión y robo de cookies. Medio.【F:application/config/security.php†L3-L10】【F:application/helpers/session_helper.php†L3-L45】
- **Construcción de URL basada en `HTTP_HOST` sin validación**: `base_url()` usa el header `Host` directamente, permitiendo envenenamiento de host para enlaces generados en correos o redirects. Medio.【F:application/helpers/url_helper.php†L12-L53】
- **Salida sin escape en alertas JS**: En checkout se imprimen alertas con valores de URL sin `htmlspecialchars`, lo que abre posible XSS si parámetros no son validados. Alto.【F:application/controllers/CheckoutController.php†L50-L106】

### 6. Rendimiento
- No hay caching, minificación ni compresión configurada; assets se cargan sin versiones/hash, lo que reduce cacheabilidad. Parcial head carga múltiples CSS/JS sin `defer`/`async`, afectando FCP. Bajo. 【F:application/views/partials/head.php†L1-L20】
- Consultas de `ProductoModel` usan `ORDER BY RAND()` y subconsultas por cada producto para imagen principal; en tablas grandes será costoso. Se sugiere reemplazar por precomputados o índices y usar `ORDER BY RAND()` solo con limitación por sampling. Medio.【F:application/models/ProductoModel.php†L52-L80】【F:application/models/ProductoModel.php†L206-L270】

### 7. SEO / Accesibilidad
- Falta de metadatos dinámicos: `<title>` y `meta description` son estáticos para todas las vistas, lo que perjudica SEO y diferenciación por página. No hay etiquetas `canonical` ni sitemap/robots configurados en repo. Medio.【F:application/views/partials/head.php†L1-L20】
- Accesibilidad limitada: no se observan atributos `lang` configurables ni roles/ARIA en layouts; formularios carecen de validaciones ARIA y feedback accesible. Bajo-medio (requiere revisión visual de vistas completas).

### 8. Integraciones / Dependencias
- No existe gestor de dependencias (Composer/NPM). Librerías externas se cargan desde assets locales o CDN (FontAwesome kit). Riesgo de versiones desactualizadas y sin SRI. Medio.【F:application/views/partials/head.php†L11-L20】
- No hay listado de dependencias ni verificación de vulnerabilidades; se recomienda introducir Composer para PHP y un pipeline de auditoría.

## Problemas y propuestas
- **CSRF en rutas críticas (Alto):** Incorporar `csrf_field()` en formularios y validar tokens en `CheckoutController` y `CarritoController` antes de procesar POST/GET mutables. Centralizar middleware CSRF en `BaseController` o router para evitar omisiones.【F:application/controllers/CheckoutController.php†L37-L205】【F:application/controllers/CarritoController.php†L15-L196】
- **Endurecer sesiones (Medio):** Activar `session.cookie_secure=1`, `SameSite=Lax/Strict`, regenerar ID tras login (cliente y admin), y restringir duración según `SESSION_LIFETIME`. Añadir cierre de sesión global en logout y almacenamiento de huella básica (IP/UA) si es necesario.【F:application/config/security.php†L3-L10】【F:application/helpers/admin_auth_helper.php†L1-L48】
- **Validación de entradas (Medio):** Crear un validador central (longitud, formatos, listas permitidas) para datos de checkout y autenticación; escapar salidas en vistas con `e()` y evitar concatenar datos sin sanitizar en JS alerts (reemplazar por mensajes server-side + redirect con flash).【F:application/controllers/CheckoutController.php†L50-L205】【F:application/helpers/security_helper.php†L17-L70】
- **Host header sanitization (Medio):** En `base_url()`, validar `HTTP_HOST` contra lista blanca/config y usar URL base del config/env, evitando generar enlaces con host inyectado.【F:application/helpers/url_helper.php†L12-L53】
- **Optimizar consultas (Medio):** Sustituir `ORDER BY RAND()` por selección pseudoaleatoria basada en IDs o seeds; precalcular imagen principal en la tabla (`producto_imagenes.es_principal`) y usar JOIN con índice en lugar de subconsultas repetidas. Considerar paginación y filtros indexados.【F:application/models/ProductoModel.php†L52-L80】【F:application/models/ProductoModel.php†L206-L270】
- **SEO/Assets (Bajo-Medio):** Generar títulos/descripciones dinámicas por vista, agregar `rel="canonical"`, `sitemap.xml` y `robots.txt`; aplicar `defer`/`async` en scripts, agregar versiones de cache busting a assets y considerar compresión gzip/brotli desde servidor.【F:application/views/partials/head.php†L1-L20】
- **Gestión de dependencias (Medio):** Introducir Composer para librerías (por ejemplo PHPMailer, dotenv), documentar versiones y activar análisis de seguridad; mover credenciales a `.env` y excluirlo del repositorio. 【F:application/config/database.php†L1-L63】
