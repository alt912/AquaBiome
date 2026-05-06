## ============================================================
##  AquaBiome — PowerShell Make Runner
##  Usage : .\make.ps1 <target>
##  Targets: sync | install | docker | migrate | cache | start | stop | open | push
##           fake-normal | fake-high | fake-low | fake-random | fake-clean | db-reset
## ============================================================

param(
    [Parameter(Position=0)]
    [string]$Target = "help"
)

function Run {
    param([string]$Cmd)
    Write-Host "> $Cmd" -ForegroundColor Cyan
    Invoke-Expression $Cmd
    if ($LASTEXITCODE -ne 0) {
        Write-Host "ERREUR : la commande a echoue (code $LASTEXITCODE)" -ForegroundColor Red
        exit $LASTEXITCODE
    }
}

switch ($Target) {

    "help" {
        Write-Host ""
        Write-Host "  .\make.ps1 sync         - Pull GitHub + install + docker + migrate + cache" -ForegroundColor Green
        Write-Host "  .\make.ps1 install       - Installe les dependances composer + assets" -ForegroundColor Green
        Write-Host "  .\make.ps1 docker        - Demarre la base de donnees (Docker)" -ForegroundColor Green
        Write-Host "  .\make.ps1 migrate       - Applique les migrations Doctrine" -ForegroundColor Green
        Write-Host "  .\make.ps1 cache         - Vide le cache Symfony" -ForegroundColor Green
        Write-Host "  .\make.ps1 push          - Envoie tous les changements sur GitHub" -ForegroundColor Green
        Write-Host "  .\make.ps1 start         - Lance le serveur Symfony et ouvre le navigateur" -ForegroundColor Green
        Write-Host "  .\make.ps1 open          - Ouvre le projet local dans le navigateur" -ForegroundColor Green
        Write-Host "  .\make.ps1 stop          - Arrete le serveur et la base de donnees" -ForegroundColor Green
        Write-Host "  .\make.ps1 db-reset      - Supprime la BDD, la recree et joue les migrations" -ForegroundColor Green
        Write-Host "  .\make.ps1 fake-normal   - Genere des mesures normales (OK)" -ForegroundColor Green
        Write-Host "  .\make.ps1 fake-high     - Genere des mesures elevees (Alerte rouge)" -ForegroundColor Green
        Write-Host "  .\make.ps1 fake-low      - Genere des mesures basses (Alerte rouge)" -ForegroundColor Green
        Write-Host "  .\make.ps1 fake-random   - Genere des mesures completement folles (Chaos)" -ForegroundColor Green
        Write-Host "  .\make.ps1 fake-clean    - Supprime toutes les donnees factices" -ForegroundColor Green
        Write-Host ""
    }

    "sync" {
        Write-Host "`n=== SYNC ===" -ForegroundColor Yellow
        Run "git fetch --all"
        Run "git pull origin main"
        Run "composer install --no-interaction --prefer-dist"
        Run "php bin/console importmap:install"
        Run "docker compose up -d"
        Run "php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration"
        Run "php bin/console cache:clear"
        Write-Host "`nSync termine avec succes !" -ForegroundColor Green
    }

    "install" {
        Write-Host "`n=== INSTALL ===" -ForegroundColor Yellow
        Run "composer install --no-interaction --prefer-dist"
        Run "php bin/console importmap:install"
    }

    "docker" {
        Write-Host "`n=== DOCKER ===" -ForegroundColor Yellow
        Run "docker compose up -d"
    }

    "migrate" {
        Write-Host "`n=== MIGRATE ===" -ForegroundColor Yellow
        Run "php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration"
    }

    "cache" {
        Write-Host "`n=== CACHE CLEAR ===" -ForegroundColor Yellow
        Run "php bin/console cache:clear"
    }

    "push" {
        Write-Host "`n=== PUSH ===" -ForegroundColor Yellow
        Run "git add ."
        Run "git commit -m 'Mise a jour globale : Makefile, Commandes de test, Fix Docker et Graphes UX'"
        Run "git push origin branche-romain"
    }

    "start" {
        Write-Host "`n=== START ===" -ForegroundColor Yellow
        Run "docker compose up -d"
        Start-Process -FilePath "symfony" -ArgumentList "serve" -WindowStyle Hidden
        Write-Host "> symfony serve (arriere-plan)" -ForegroundColor Cyan
        Start-Sleep -Seconds 2
        Run "symfony open:local"
    }

    "open" {
        Write-Host "`n=== OPEN ===" -ForegroundColor Yellow
        Run "symfony open:local"
    }

    "stop" {
        Write-Host "`n=== STOP ===" -ForegroundColor Yellow
        Run "symfony server:stop"
        Run "docker compose down"
    }

    "db-reset" {
        Write-Host "`n=== DB RESET ===" -ForegroundColor Yellow
        Run "php bin/console doctrine:database:drop --force"
        Run "php bin/console doctrine:database:create"
        Run "php bin/console doctrine:migrations:migrate --no-interaction"
    }

    "fake-normal" {
        Run "php bin/console app:fake-data normal"
    }

    "fake-high" {
        Run "php bin/console app:fake-data high"
    }

    "fake-low" {
        Run "php bin/console app:fake-data low"
    }

    "fake-random" {
        Run "php bin/console app:fake-data random"
    }

    "fake-clean" {
        Run "php bin/console app:fake-data --clean"
    }

    default {
        Write-Host "Cible inconnue : '$Target'. Utilisez .\make.ps1 help pour voir les commandes." -ForegroundColor Red
        exit 1
    }
}
