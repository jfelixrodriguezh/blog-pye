# Paquete de datos de la Biblia (RV60)

## 1. Copia los archivos a tu proyecto

| De este zip | Hacia tu proyecto |
|---|---|
| `database/migrations/..._create_biblia_tables.php` | `database/migrations/` |
| `app/Models/Testamento.php` | `app/Models/` |
| `app/Models/Libro.php` | `app/Models/` |
| `app/Models/Versiculo.php` | `app/Models/` |
| `database/seeders/BibliaSeeder.php` | `database/seeders/` |
| `database/seeders/data/biblia/` (carpeta completa, 3 archivos .json) | `database/seeders/data/biblia/` |

## 2. Registra el seeder

En `database/seeders/DatabaseSeeder.php`, agrega `BibliaSeeder::class` a la lista de `$this->call([...])` (al principio, antes de Autores/Categorías/Tags/Posts — no depende de nada más, pero es más limpio tenerlo primero).

## 3. Corre las migraciones

```
php artisan migrate:fresh --seed
```

Deberías ver en la terminal: `31101 versículos importados (66 libros).`

## Qué corregí en tus datos originales

- **17 de los 66 nombres de libros** tenían la vocal acentuada faltante (ej. "Isaas" → "Isaías", "Nmeros" → "Números") — probablemente de una conversión de codificación anterior. El texto de los versículos en sí ya estaba perfectamente bien, no toqué ni una palabra ahí.
- Agregué **slug** a cada libro (ej. "1ra Corintios" → `1ra-corintios`) para usarlo en URLs limpias más adelante (`/biblia/genesis/1`).
- Confirmé que el orden de tus IDs (1 a 66) ya coincide con el orden canónico bíblico — lo reutilicé tal cual como columna `orden`, no tuve que reordenar nada.
- Verifiqué conteos de capítulos contra los valores conocidos (Génesis 50, Salmos 150, Apocalipsis 22, Juan 21) — todo cuadra, así que confío en que el resto de los datos está completo.
