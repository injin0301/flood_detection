# Installation de Docker Compose pour Flood Detection

## Prérequis

- Docker
- Docker Compose

## Configuration

1. Copiez le fichier `.env.default` en `.env` dans le dossier `docker` :

```bash
cp docker/.env.default docker/.env
```

2. Ajoutez les variables d'environnement suivantes dans le fichier `.env` :

```env
APP_DIR=path/dir/project
APP_PORT=80

# BDD_DIR=/var/docker/gsmn-bdd
BDD_DIR=chemin/vers/le/dossier/de/la/base/de/donner
BDD_PORT=5432
```

### Explication

Ces variables d'environnement configurent les chemins et les ports nécessaires pour l'application et la base de données.

- **APP_DIR** : Chemin vers le répertoire de votre projet. Sous Linux, utilisez un chemin de type `/home/utilisateur/projet`. Sous Windows, utilisez un chemin de type `C:\\Users\\utilisateur\\projet`.
- **APP_PORT** : Port sur lequel l'application sera accessible. Par défaut, il est configuré sur `80`.
- **BDD_DIR** : Chemin vers le répertoire de la base de données. Sous Linux, utilisez un chemin de type `/home/utilisateur/base_de_donnees`. Sous Windows, utilisez un chemin de type `C:\\Users\\utilisateur\\base_de_donnees`.
- **BDD_PORT** : Port sur lequel la base de données sera accessible. Par défaut, il est configuré sur `5432`.

Assurez-vous de remplacer les chemins et les ports par ceux qui correspondent à votre configuration locale.

## Installation

1. Créez un réseau Docker :

```bash
docker network create app
```

2. Lancez Docker Compose :

```bash
docker compose up -d
```

## Migration de la base de données

1. Exécutez la migration vers une nouvelle instance de fichier SQL :

```bash
docker compose exec -itu 1000 app bin/console doctrine:migrations:migrate
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

Swagger facilite la compréhension et l'utilisation de l'API en fournissant une interface utilisateur conviviale pour explorer et tester les fonctionnalités disponibles.

### Autorisation

Il est nécessaire d'avoir l'autorisation pour utiliser les API, sauf pour se connecter et créer un compte.