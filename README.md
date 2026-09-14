# Peregrinos y Extranjeros

Blog cristiano construido con **Laravel** y **Livewire 4**, con una sección pública (blog, Biblia online, podcast, himnario y videos de YouTube) y un panel de administración basado en la plantilla **Cork v5** para gestionar todo el contenido.

## Stack técnico

- PHP 8.3+
- Laravel Framework ^13.17
- Livewire 4 (componentes de clase, estilo Volt: `new class extends Component { ... }`)
- Spatie Laravel-Permission ^8.3 (roles y permisos)
- Bootstrap 5.3 + Alpine.js (interactividad del lado del cliente)
- Quill 2 (editor de texto enriquecido para descripciones/contenido)
- SweetAlert2 (confirmaciones de eliminación)
- Tom Select (selects con búsqueda, para categorías/tags)
- Vite + Sass (assets del admin, base de la plantilla Cork en modo claro y oscuro)
- Base de datos: MySQL

No hay controladores tradicionales: toda la lógica pública vive en `routes/web.php` (rutas simples y closures) y toda la lógica interactiva (listados, formularios, CRUD) vive en componentes Livewire dentro de `resources/views/components/`.

## Requisitos previos

- PHP >= 8.3 con las extensiones habituales de Laravel (mbstring, pdo_mysql, fileinfo, gd, etc.)
- Composer 2
- Node.js 18+ y npm
- MySQL 8 (o compatible)
- Servidor local recomendado: Laragon, Valet o similar

## Instalación

```bash
# 1. Clonar el proyecto y entrar a la carpeta
git clone <repo> blog-pye
cd blog-pye

# 2. Instalar dependencias de PHP
composer install

# 3. Copiar el archivo de entorno y generar la clave de la app
cp .env.example .env
php artisan key:generate

# 4. Configurar la base de datos en .env (ver sección siguiente)

# 5. Instalar dependencias de JS y compilar assets
npm install
npm run build      # producción
# npm run dev       # desarrollo, con recarga en caliente

# 6. Crear la base de datos y correr migraciones + seeders
php artisan migrate --seed

# 7. Enlazar el storage público (fotos de autores, imágenes de posts, partituras, audios locales)
php artisan storage:link
```

Con eso el sitio queda funcional en `/` (público) y `/admin/dashboard` (panel, requiere iniciar sesión en `/login`).

### Variables de entorno importantes

En `.env`, además de las estándar de Laravel:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_pye
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://tu-dominio-o-localhost
```

`APP_URL` debe apuntar a la URL real del sitio porque de ahí se generan los enlaces absolutos (por ejemplo, en las páginas de audio/podcasts) y las rutas de `asset()`/`Storage`.

### Despliegue a un servidor

Al subir el proyecto a un servidor nuevo:

1. Corre `composer install --no-dev --optimize-autoloader` y `npm install && npm run build`.
2. Configura `.env` con las credenciales reales de la base de datos y `APP_ENV=production`, `APP_DEBUG=false`.
3. Corre `php artisan migrate --seed --force` (el `--force` es obligatorio en producción). Esto deja la Biblia, el himnario, los permisos, el rol Admin y el usuario administrador por defecto listos — **no** carga posts/autores/categorías/tags de ejemplo (esos seeders están desactivados a propósito, ver más abajo).
4. Corre `php artisan storage:link` — el enlace simbólico de `public/storage` es local a cada máquina y no se transfiere al subir los archivos.
5. Da permisos de escritura a `storage/` y `bootstrap/cache/`.
6. Configura el servidor web (Nginx/Apache) apuntando el document root a la carpeta `public/`.

## Usuario administrador por defecto

El seeder `AdminUserSeeder` crea (o actualiza) un usuario administrador:

| Campo | Valor |
|---|---|
| Nombre | `pyeadmin` |
| Correo (para iniciar sesión) | `pyeadmin@correo.com` |
| Contraseña | `*159753*PyE` |
| Rol | Admin (todos los permisos) |

**Importante:** el login del sitio es por correo, no existe un campo de "usuario" en la base de datos. Se recomienda cambiar esta contraseña desde un cliente de base de datos o creando un usuario nuevo apenas el sitio esté en producción, ya que estas credenciales quedan documentadas aquí.

## Roles y permisos

El sistema usa Spatie Laravel-Permission con permisos granulares por módulo (`ver`, `crear`, `editar`, `eliminar` + `<módulo>`, por ejemplo `editar posts`) y permisos generales de acceso a cada sección del admin (`manage posts`, `manage podcasts`, `manage himnos`, `manage taxonomies`, `manage videos`, `manage users`).

Por defecto, el seeder `RolePermissionSeeder` **solo crea el rol `Admin`**, con todos los permisos sincronizados. Los demás roles (por ejemplo, un rol "Editor de posts" o "Encargado de himnos") se crean manualmente desde **Admin → Roles**, donde se puede elegir el nombre y marcar los permisos exactos que tendrá. Esto le da control total a quien administre el sitio sobre qué puede hacer cada usuario, sin roles predefinidos que luego haya que ajustar.

## Datos de ejemplo (solo desarrollo local)

En `database/seeders/DatabaseSeeder.php` hay seeders de contenido de ejemplo comentados a propósito para que no se carguen en el servidor: `AutorSeeder`, `CategorySeeder`, `TagSeeder` y `PostSeeder` (crean autores, categorías, tags y posts falsos con Faker). Si se necesitan de nuevo en un entorno local, basta con descomentar esas líneas antes de correr `php artisan db:seed`.

Los seeders que sí están activos siempre (contenido real de la aplicación, no de prueba): `BibliaSeeder` (texto bíblico completo), `RolePermissionSeeder`, `AdminUserSeeder` y `HimnarioSeeder` (himnario con letras, tonos, partituras y autores propios).

---

## Módulos del lado público

| Sección | Ruta | Descripción |
|---|---|---|
| Home | `/` | Feed principal de posts publicados. |
| Post individual | `/post/{slug}` | Artículo completo, con autor, categoría, tags, tiempo de lectura, posts relacionados y navegación anterior/siguiente. |
| Biblia Online | `/biblia`, `/biblia/{libro}/{capitulo}` | Lectura de la Biblia por libro y capítulo, con navegación entre capítulos y libros. |
| Buscar en la Biblia | `/biblia/buscar` | Búsqueda de versículos por palabra o frase. |
| Podcast | `/podcasts`, `/podcasts/{episode}` | Listado de episodios y vista de detalle con reproductor (audio propio o embed de Spotify), notas del episodio y episodios relacionados. |
| Himnos | `/himnos`, `/himnos/{himnario}/{numero}` | Himnario con letra, partitura, audio y video de YouTube embebido por himno. |
| YouTube | `/youtube` | Galería de videos del canal de YouTube en formato de tarjetas con reproductor en modal (lightbox). |
| Perfil de autor | `/autor/{autor}` | Página pública con foto, biografía y el conteo/listado de posts, episodios e himnos publicados por ese autor. |
| Buscar en el blog | `/buscar` | Búsqueda general de contenido del blog. |
| Iniciar sesión | `/login` | Acceso al panel de administración (no hay registro público). |

El layout público (`layouts/public.blade.php`) incluye menú responsive (hamburguesa en móvil), buscador, modo claro/oscuro con persistencia, y pie de página fijo.

## Módulos del lado de administración

Todo bajo el prefijo `/admin`, protegido por sesión iniciada y, según el módulo, por el permiso `manage <módulo>` correspondiente.

| Módulo | Ruta | Qué permite hacer |
|---|---|---|
| Dashboard | `/admin/dashboard` | Resumen general: totales y publicados de posts, podcasts, episodios e himnos, además de los últimos posts y episodios creados. |
| Posts | `/admin/posts` | CRUD de artículos del blog: título, categoría, tags, autor, imagen destacada y contenido con editor Quill. |
| Categorías | `/admin/categories` | CRUD de categorías, reutilizables entre posts, podcasts e himnos (con banderas para indicar a qué tipo de contenido aplica cada una). |
| Tags | `/admin/tags` | CRUD de etiquetas para posts y episodios. |
| Podcasts | `/admin/podcasts` | Listado de podcasts (programas). |
| Episodios | `/admin/episodes` | CRUD de episodios: audio propio o embed de Spotify, categorías, tags, autor y notas del episodio. |
| Himnarios | `/admin/himnarios` | Listado de himnarios (colecciones de himnos). |
| Tonos | `/admin/tonos` | Catálogo de tonos/tonalidades musicales usados por los himnos. |
| Himnos | `/admin/himnos` | CRUD de himnos: letra por estrofas, tono, partitura, audio, video de YouTube y autor. |
| Autores | `/admin/autors` | CRUD de autores: nombre, foto y biografía con editor Quill. Incluye un enlace de "vista previa" al perfil público de cada autor. |
| Videos | `/admin/videos` | CRUD de videos de YouTube que se muestran en la sección pública `/youtube` (título, URL y estado activo/inactivo). |
| Usuarios | `/admin/usuarios` | Listado de usuarios del sistema (requiere permiso `manage users`). |
| Roles | `/admin/roles` | Creación y edición de roles, asignando de forma granular cuáles de los permisos disponibles tiene cada uno. |

El layout de administración (`layouts/app.blade.php`) comparte los mismos componentes de navegación responsive y modo claro/oscuro que el sitio público, y usa el editor Quill (vía Alpine + `wire:ignore`) en todos los campos de texto enriquecido.

## Estructura de carpetas relevante

```
app/Models/            Modelos Eloquent (Post, Autor, Episode, Himno, Video, etc.)
routes/web.php          Todas las rutas públicas y de administración
resources/views/        Vistas Blade
  components/           Componentes Livewire (listados y formularios del admin, feeds públicos)
  layouts/               layouts/public.blade.php (sitio) y layouts/app.blade.php (admin)
database/seeders/       Seeders (contenido real + datos de ejemplo para desarrollo)
database/seeders/data/  JSON fuente para la Biblia y el himnario
public/images/brand/    Logo del sitio (navbar, favicon, variantes)
```
