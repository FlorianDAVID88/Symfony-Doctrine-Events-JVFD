# Symfony-Doctrine-Events-JVFD
Application web en Symfony sur l'idée de la gestion d'événements (projet créé pour la 3ème année de BUT Informatique)

## Prérequis
<ul style="list-style: square;">
    <li style="align-items: center;">
        <img src="https://cdn.worldvectorlogo.com/logos/docker-4.svg" height="20" alt="Docker logo">
        <a href="https://www.docker.com/">Docker</a>
    </li>
    <li style="align-items: center;">
        <img src="https://cdn.icon-icons.com/icons2/2699/PNG/512/sqlite_logo_icon_169724.png" height="20" alt="SQLite logo">
        <a href="https://www.sqlite.org/index.html">SQLite</a>
    </li>
</ul>

## Création du conteneur Docker
Après avoir cloné le projet, se rendre à sa racine et créer le conteneur Docker :
```bash
docker-compose up -d --build
```

## Utilisation du conteneur
À partir de cette étape, se rendre dans le conteneur Docker nommé
<b>*symfony-doctrine-events-jvfd_php*</b>,
puis exécuter les commandes qui suiveront :

### Installation des modules Composer
```bash
cd doctrine-events
```
puis
```bash
composer install
```
### Base de données SQLite
#### Création de la base (nommée *db_events.db*)
```bash
php bin/console doctrine:database:create
```

#### Update force
```bash
php bin/console doc:sc:up -f
```

#### Insertion des données avec les fixtures
```bash
php bin/console doctrine:fixtures:load
```

### Installation de Symfony
```bash
wget https://get.symfony.com/cli/installer -O - | bash
```
puis
```bash
export PATH="$HOME/.symfony5/bin:$PATH"
```

### Lancement du serveur
```bash
symfony server:start
```