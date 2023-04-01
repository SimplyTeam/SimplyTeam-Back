# SimplyTeam
## Lancement de l'app
### Prérequies
Avant de lancer le back, il est nécessaire d'installer les dépendances suivantes :
#### PHP 8.2
```shell
sudo apt install php8.2
```
Plus d'info : https://www.php.net/downloads

---
#### Composer
```shell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
Plus d'info : https://getcomposer.org/download/

---
#### Docker
```shell
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```
Plus d'info : https://docs.docker.com/engine/install/

---
#### Copie du .env.local en .env
```shell
cp .env.local .env
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
```envGOOGLE_CLIENT_ID={GOOGLE_CLIENT_API}
GOOGLE_CLIENT_ID={GOOGLE_CLIENT_API}
GOOGLE_CLIENT_SECRET={GOOGLE_CLIENT_SECRET}
WEBAPP_REDIRECT_URI={WEBAPP_REDIRECT_URI}
```
Le WEBAPP_REDIRECT_URI doit être l'url d'authentification du front.

### Installation des dépendances
Afin d'installer toutes les dépendances nécessaire, il suffit simplement de lancer la commande :
```shell
composer install
```

### Lancement du serveur
```shell
docker-compose up -d
```

### Lancement des migrations :
```shell
php artisan migrate
```
