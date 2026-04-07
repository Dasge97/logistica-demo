# Despliegue de Logistica y Tarifas

## Resumen

La aplicacion se despliega como un monolito Symfony con tres servicios principales:

- `app`: contenedor PHP-FPM con la aplicacion;
- `web`: Nginx sirviendo la parte publica;
- `database`: PostgreSQL.

La configuracion base ya existe en `compose.yaml` y el runtime de PHP se construye con `Dockerfile`.

## Requisitos

- Docker
- Docker Compose
- acceso al repositorio del proyecto

## Variables recomendadas

Crea un fichero `.env.local` o exporta variables reales antes del despliegue:

```bash
APP_ENV=prod
APP_SECRET=<cambiar-por-una-clave-real>
POSTGRES_DB=app
POSTGRES_USER=app
POSTGRES_PASSWORD=<password-segura>
DATABASE_URL=postgresql://app:<password-segura>@database:5432/app?serverVersion=16&charset=utf8
```

## Primer arranque

```bash
docker compose build
docker compose up -d
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec app php bin/console app:usuario:crear-admin admin@tudominio.local Administrador <password>
docker compose exec app php bin/console app:catalogos:cargar-base
docker compose exec app php bin/console app:pedidos:cargar-demo
```

## Actualizacion de una instalacion existente

```bash
git pull
docker compose build
docker compose up -d
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

## Verificaciones minimas

- login disponible en `/acceso`;
- dashboard accesible tras autenticar;
- listado de pedidos visible;
- simulador logistico funcional;
- snapshots visibles cuando exista al menos un pedido confirmado.

## Checklist operativa

- cambiar `APP_SECRET` por una clave real;
- no usar passwords de ejemplo;
- no subir `.env.local` al repositorio;
- ejecutar migraciones antes de abrir el servicio al trafico;
- revisar que el puerto `8080` no colisiona con otros servicios;
- colocar Traefik o Nginx externo delante si se va a publicar en internet.

## Comandos utiles

```bash
docker compose ps
docker compose logs -f app
docker compose logs -f web
docker compose exec app php bin/console about
docker compose exec app php bin/console app:pedidos:resolver <pedido-id>
docker compose exec app php bin/phpunit
```
