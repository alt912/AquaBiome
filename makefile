## ============================================================
##  AquaBiome — Makefile
##  Commandes : make sync | docker | migrate | cache | start | stop | open
## ============================================================

.PHONY: help sync install docker migrate cache start stop open

## Affiche l'aide
help:
	@echo.
	@echo   make sync      - Pull GitHub + install + docker + migrate + cache
	@echo   make install   - Installe les dependances composer + assets
	@echo   make docker    - Demarre la base de donnees (Docker)
	@echo   make migrate   - Applique les migrations Doctrine
	@echo   make cache     - Vide le cache Symfony
	@echo   make start     - Lance le serveur Symfony et ouvre le navigateur
	@echo   make open      - Ouvre le projet local dans le navigateur par defaut
	@echo   make stop      - Arrete le serveur et la base de donnees
	@echo   make db-reset  - Supprime la BDD, la recree et joue les migrations

## Pull GitHub + tout setup
sync:
	git fetch --all
	git pull origin main
	composer install --no-interaction --prefer-dist
	php bin/console importmap:install
	docker compose up -d
	php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	php bin/console cache:clear

## Installe les dependances uniquement
install:
	composer install --no-interaction --prefer-dist
	php bin/console importmap:install

## Demarre la base de donnees
docker:
	docker compose up -d

## Applique les migrations
migrate:
	php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

## Vide le cache
cache:
	php bin/console cache:clear

## Lance le serveur Symfony et ouvre le navigateur
start:
	powershell -c "Start-Process -FilePath 'symfony' -ArgumentList 'serve' -WindowStyle Hidden"
	symfony open:local

## Ouvre le projet local dans le navigateur
open:
	symfony open:local

## Arrete tout
stop:
	symfony server:stop
	docker compose down

## Supprime et recree la base de donnees
db-reset:
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate --no-interaction
