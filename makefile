## ============================================================
##  AquaBiome — Makefile
##  Commandes : make sync | docker | migrate | cache | start | stop | open
## ============================================================

.PHONY: help sync install docker migrate cache start stop open push fake-normal fake-high fake-low fake-random fake-clean

## Affiche l'aide
help:
	@echo.
	@echo   make sync      - Pull GitHub + install + docker + migrate + cache
	@echo   make install   - Installe les dependances composer + assets
	@echo   make docker    - Demarre la base de donnees (Docker)
	@echo   make migrate   - Applique les migrations Doctrine
	@echo   make cache     - Vide le cache Symfony
	@echo   make push      - Envoie tous les changements sur GitHub (Add + Commit + Push)
	@echo   make start     - Lance le serveur Symfony et ouvre le navigateur
	@echo   make open      - Ouvre le projet local dans le navigateur par defaut
	@echo   make stop      - Arrete le serveur et la base de donnees
	@echo   make db-reset  - Supprime la BDD, la recree et joue les migrations
	@echo   make fake-normal - Genere des mesures normales (OK)
	@echo   make fake-high   - Genere des mesures elevees (Alerte rouge)
	@echo   make fake-low    - Genere des mesures basses (Alerte rouge)
	@echo   make fake-random - Genere des mesures completement folles (Chaos)
	@echo   make fake-clean  - Supprime toutes les donnees factices (propres)

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

## Envoie tout sur GitHub
push:
	git add .
	git commit -m "Mise a jour globale : Makefile, Commandes de test, Fix Docker et Graphes UX"
	git push origin branche-romain

## Lance le serveur Symfony et ouvre le navigateur
start:
	docker compose up -d
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

## Génération de données de test
fake-normal:
	php bin/console app:fake-data normal

fake-high:
	php bin/console app:fake-data high

fake-low:
	php bin/console app:fake-data low

fake-random:
	php bin/console app:fake-data random

fake-clean:
	php bin/console app:fake-data --clean
