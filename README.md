# SimplyTeam
## Lancement de l'app
### Prérequies
Avant de lancer le back, il est nécessaire d'installer les dépendances suivantes :
#### PHP 8
1. Mettre à jour les packages
```shell
sudo apt update && sudo apt upgrade
```

2. installer php8.1
```shell
sudo apt install php8.1
```

3. installer les extensions mbstring, curl et postgresql
```shell
sudo apt install php8.1-mbstring php8.1-curl php8.1-pgsql php8.1-pdo-pgsql php8.1-dom php8.1-xml
```

Plus d'info : https://www.php.net/downloads

---
#### Composer
1. Installer composer
```shell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

2. Le rendre accessible depuis n'importe quel directory :
```shell
sudo mv composer.phar /usr/local/bin/composer
```

Plus d'info : https://getcomposer.org/download/

---
#### Docker
##### Set le repository docker
1. Mise à jour de l'index des paquets apt et des paquets d'installation pour permettre à apt d'utiliser un dépôt via HTTPS :
```shell
$ sudo apt-get update

$ sudo apt-get install \
    ca-certificates \
    curl \
    gnupg
```

2. Ajout des clé GPG officiel de docker :
```shell
$ sudo mkdir -m 0755 -p /etc/apt/keyrings

$ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
```

3. Utilisez la commande suivante pour configurer le repo :
```shell
echo \
  "deb [arch="$(dpkg --print-architecture)" signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  "$(. /etc/os-release && echo "$VERSION_CODENAME")" stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```

##### Installer docker engine

```shell
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```
Plus d'info : https://docs.docker.com/engine/install/

---
#### Copie du .env.example en .env
```shell
cp .env.example .env
```
---
#### Un compte mail (outlook ou autre) permettant de servir de service smtp
Pour cela, il est nécessaire de créer un compte mail sur n'importe quel plateforme et de modifier les informations du .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME={test-mail@outlook.com}
MAIL_PASSWORD={MotDePasse}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS={test-mail@domain.com}
MAIL_FROM_NAME="Votre nom"
```
**Assurez vous que le compte utilisé ne possède pas la double authentification**

---
#### Un compte google API
1. Connectez-vous à la console des développeurs Google à l'adresse suivante : https://console.developers.google.com/
2. Créez un nouveau projet en cliquant sur le bouton "Sélectionner un projet" en haut de la page et en cliquant sur "Nouveau projet" dans la fenêtre qui apparaît.
3. Donnez un nom à votre projet et cliquez sur "Créer".
4. Dans la colonne de gauche, cliquez sur "Identifiants".
5. Dans l'onglet "Identifiants", cliquez sur le bouton "Créer des identifiants" et sélectionnez "ID client OAuth".
6. Sélectionnez "Application Web" comme type de client.
7. Dans la section "Origines autorisées", ajoutez l'URI de redirection pour votre application Laravel (http://localhost:80/auth/google/callback).
8. Dans la section "URI de redirection autorisées", ajoutez l'URI de redirection pour votre application web (WEBAPP_REDIRECT_URI).
9. Cliquez sur "Créer" pour créer votre ID client.
10. Copiez l'ID client et le secret client et utilisez-les pour remplir les variables d'environnement GOOGLE_CLIENT_ID et GOOGLE_CLIENT_SECRET dans le fichier .env de votre application Laravel.
11. Utilisez l'URI de redirection pour votre application Laravel (http://localhost:80/auth/google/callback) pour remplir la variable d'environnement GOOGLE_REDIRECT_URI dans le fichier .env de votre application Laravel.
12. Utilisez l'URI de redirection pour votre application web (WEBAPP_REDIRECT_URI) pour remplir la variable d'environnement WEBAPP_REDIRECT_URI dans le fichier .env de votre application Laravel.

Les variables d'environnements à modifiés sont donc celles-ci :
```env
GOOGLE_CLIENT_ID={GOOGLE_CLIENT_API}
GOOGLE_CLIENT_ID={GOOGLE_CLIENT_API}
GOOGLE_CLIENT_SECRET={GOOGLE_CLIENT_SECRET}
WEBAPP_REDIRECT_URI={WEBAPP_REDIRECT_URI}
```
**La variable d'environnement WEBAPP_REDIRECT_URI doit être set de la façon suivante :**
```python
WEBAPP_REDIRECT_URI=http://localhost:3000/
```

#### Mettre à jour la variable REDIRECTED_URL_MAIL
```env
REDIRECTED_URL_MAIL=http://localhost:3000/workspaces
```

#### Mettre à jour les informations DB_ du .env :
```env
DB_CONNECTION=postgresql
DB_HOST=172.21.73.3
DB_PORT=5432
DB_DATABASE=simplyteam
DB_USERNAME=postgres
DB_PASSWORD=postgres
```
**! EN CAS D'ERREUR **
```txt
    169▕         // If the configuration doesn't exist, we'll throw an exception and bail.
    170▕         $connections = $this->app['config']['database.connections'];
    171▕ 
    172▕         if (is_null($config = Arr::get($connections, $name))) {
  ➜ 173▕             throw new InvalidArgumentException("Database connection [{$name}] not configured.");
    174▕         }
    175▕ 
    176▕         return (new ConfigurationUrlParser)
    177▕                     ->parseConfiguration($config);
```

Remplacez : 
```env
DB_CONNECTION=postgresql
```
par
```env
DB_CONNECTION=pgsql
```
### Installation des dépendances
Afin d'installer toutes les dépendances nécessaire, il suffit simplement de lancer la commande :
```shell
composer install
```

### Lancement du serveur
```shell
sudo docker compose up -d
```

### Lancement des migrations :
```shell
php artisan migrate
```

### Générer une encryption key **!OBLIGATOIRE!**
```shell
php artisan key:generate
```

### Créations des clés oauth :
```shell
php artisan passport:install
```

### Lancement des tests :
```shell
php artisan test --env=.env
```

### En cas d'erreur :
Se fier au fichier commandUseToFix.md
