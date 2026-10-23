# Prex GIPHY API

API REST en Laravel que se integra con la API de [GIPHY](https://developers.giphy.com/docs/api/) para buscar GIFs, consultar un GIF por ID y guardar GIFs favoritos por usuario. Autenticación OAuth2.0 y registro de todas las interacciones.

Construida con **arquitectura hexagonal + DDD**.

## Stack

| Componente | Versión |
|---|---|
| PHP | 8.4 |
| Laravel | 13 |
| MySQL | 8.0 |
| Tests | Pest 5 (PHPUnit 13) |
| Servidor web | Nginx + PHP-FPM |
| Contenedores | Docker Compose |

## Requisitos

- Docker (Docker Desktop, OrbStack o Docker Engine con el plugin Compose)
- Git
- Una API key de GIPHY: se crea gratis en [developers.giphy.com](https://developers.giphy.com/dashboard/) → *Create an API Key* → tipo **API**.

No hace falta tener PHP, Composer ni MySQL instalados: todo corre dentro de Docker.

## Levantar el proyecto

```bash
git clone <url-del-repo> prex-giphy-challenge
cd prex-giphy-challenge

cp .env.example .env
# Editar .env y completar GIPHY_API_KEY

docker compose up -d --build
```

La primera vez tarda unos minutos: construye la imagen, instala dependencias, genera la `APP_KEY` y corre las migraciones automáticamente.

Para verificar que levantó:

```bash
docker compose ps                 # app, nginx y mysql corriendo; mysql (healthy)
curl http://localhost:8080        # {"name":"Prex GIPHY API","status":"ok"}
curl -i http://localhost:8080/up  # HTTP/1.1 200 OK
```

### Servicios

| Servicio | Qué es | Puerto en tu máquina |
|---|---|---|
| `nginx` | Servidor web, entrada de la API | `8080` |
| `app` | PHP-FPM 8.4 con Laravel | — (interno, 9000) |
| `mysql` | Base de datos MySQL 8.0 | `3307` |

Bases de datos: `prex` (aplicación) y `prex_testing` (tests). Usuario `prex`, contraseña `secret` (solo para desarrollo).

### Linux y Windows

- **Linux:** el contenedor corre con un usuario no-root con UID/GID 1000. Si tu usuario tiene otro UID (`id -u`), construí la imagen con el tuyo para evitar errores de permisos en `storage/` y `vendor/`:
  ```bash
  HOST_UID=$(id -u) HOST_GID=$(id -g) docker compose up -d --build
  ```
- **Windows:** usar Docker Desktop con WSL 2. Si clonás el repo dentro del sistema de archivos de WSL, aplica lo mismo que en Linux.

## Comandos útiles

Atajos del `Makefile` (requieren `make`; al lado, el comando equivalente):

| Comando | Equivale a | Para qué |
|---|---|---|
| `make up` | `docker compose up -d` | Levantar |
| `make down` | `docker compose down` | Apagar |
| `make build` | `docker compose build` | Reconstruir la imagen |
| `make logs` | `docker compose logs -f` | Ver logs |
| `make shell` | `docker compose exec app sh` | Entrar al contenedor |
| `make migrate` | `docker compose exec app php artisan migrate` | Correr migraciones |
| `make test` | `docker compose exec app php artisan test` | Correr los tests |
| `make coverage` | `docker compose exec app php artisan test --coverage` | Tests con cobertura |

Para empezar con la base vacía: `docker compose down -v` (borra el volumen de MySQL).

## Tests

Los tests corren **dentro de Docker** contra la base `prex_testing` (nunca contra la base de la aplicación):

```bash
make test
```

Otras formas de correrlos:

```bash
docker compose exec app php artisan test --testsuite=Arch     # solo arquitectura
docker compose exec app php artisan test --filter="health"    # por nombre
docker compose exec app php artisan test --coverage           # con cobertura (PCOV)
```

Suites:

| Suite | Qué contiene |
|---|---|
| `Arch` | Reglas de arquitectura verificadas automáticamente: el dominio no depende de Laravel, la capa de aplicación no depende de infraestructura, `strict_types` en todo `src/`, sin funciones de debug |
| `Unit` | Lógica de negocio aislada, sin framework ni base de datos |
| `Feature` | Endpoints HTTP de punta a punta |

## Estructura del proyecto

El código propio vive en `src/`, organizado primero por módulo de negocio y después por capa:

```
src/
├── IdentityAccess/   autenticación y tokens
├── Gifs/             búsqueda y detalle de GIFs (integración con GIPHY)
├── Favorites/        GIFs favoritos de cada usuario
├── Audit/            registro de interacciones con la API
└── Shared/           elementos comunes
    └── cada módulo: Domain/ · Application/ · Infrastructure/
```

| Capa | Contiene | Depende de |
|---|---|---|
| `Domain` | Entidades, value objects, puertos (interfaces) y excepciones de negocio. PHP puro | Nada |
| `Application` | Casos de uso | `Domain` |
| `Infrastructure` | Controllers, adapters (Eloquent, GIPHY, Passport), configuración | Todo |

`app/` conserva solo el arranque de Laravel.
