# Documentation Technique pour Flood Detection

## Table des matières
1. [Introduction](#introduction)
2. [Structure du Projet](#structure-du-projet)
3. [Configuration](#configuration)
4. [Installation](#installation)
5. [Migration de la Base de Données](#migration-de-la-base-de-données)
6. [Utilisation](#utilisation)
7. [Documentation de l'API](#documentation-de-lapi)
8. [Tests](#tests)
9. [Déploiement](#déploiement)
10. [Documentation du Code Source](#documentation-du-code-source)
11. [Guide d'Installation](#guide-dinstallation)
12. [Manuel Utilisateur](#manuel-utilisateur)

## Introduction
Ce document fournit des informations techniques sur le projet Flood Detection, y compris la structure du projet, les étapes d'installation, la configuration, et plus encore.

## Structure du Projet
Le projet est structuré comme suit :
```
back/
├── .env
├── .env.dev
├── .env.test
├── .gitignore
├── .php-cs-fixer.dist.php
├── .phpactor.json
├── composer.json
├── composer.lock
├── phpcs.xml.dist
├── phpstan.dist.neon
├── phpunit.xml.dist
├── README.md
├── symfony.lock
├── t.html
├── .vscode/
│   └── ...
├── bin/
├── config/
│   └── routes/
│       └── nelmio_api_doc.yaml
├── docker/
├── migrations/
├── public/
├── src/
├── templates/
├── tests/
├── var/
└── vendor/
```

## Configuration
Les fichiers de configuration principaux incluent `.env`, `.env.dev`, et `.env.test`. Assurez-vous de configurer correctement les variables d'environnement.

### Variables d'Environnement
- **APP_ENV** : Environnement de l'application (`dev`, `prod`, etc.).
- **APP_SECRET** : Secret de l'application.
- **DATABASE_URL** : URL de connexion à la base de données.
- **CORS_ALLOW_ORIGIN** : Origines autorisées pour les requêtes CORS.
- **JWT_SECRET_KEY** : Clé secrète JWT.
- **JWT_PUBLIC_KEY** : Clé publique JWT.
- **JWT_PASSPHRASE** : Passphrase JWT.

## Installation
1. Installez les dépendances :
    ```bash
    composer install
    ```

2. Créez un réseau Docker :
    ```bash
    docker network create app
    ```

3. Lancez Docker Compose :
    ```bash
    docker compose up -d
    ```

## Migration de la Base de Données
1. Exécutez la migration vers une nouvelle instance de fichier SQL :
    ```bash
    docker compose exec -itu 1000 app php bin/console doctrine:migrations:migrate
    ```

## Utilisation
- Pour arrêter les conteneurs :
    ```bash
    docker compose down
    ```

## Documentation de l'API
Pour accéder à la documentation de l'API, ouvrez votre navigateur et allez à l'adresse suivante :
```
http://localhost/api/doc
```

### Utilisation de Swagger
Swagger est utilisé pour générer et afficher la documentation de l'API. Il permet de tester les différentes routes de l'API directement depuis l'interface utilisateur.

- **Accéder à Swagger** : Rendez-vous sur `http://localhost/api/doc` pour voir la documentation interactive.
- **Tester les endpoints** : Utilisez l'interface Swagger pour envoyer des requêtes aux différents endpoints de l'API et voir les réponses en temps réel.

## Tests
Pour exécuter les tests, utilisez la commande suivante :
```bash
php bin/phpunit
```

## Déploiement
Les étapes de déploiement incluent la configuration des variables d'environnement, la migration de la base de données, et le lancement des conteneurs Docker.

## Guide d'Installation
Ce guide fournit des instructions détaillées pour installer et configurer le projet Flood Detection.

### Prérequis
- Docker
- Docker Compose
- PHP 7.4 ou supérieur
- Composer

[Guide d'Installation](README.md)
