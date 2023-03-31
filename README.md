# SimplyTeam
## Lancement de l'app
### Prérequies
Avant de lancer le back, il est nécessaire d'installer les dépendances suivantes :
#### PHP 8.2
```shell
sudo apt install php8.2
```
Plus d'info : https://www.php.net/downloads

#### Composer
```shell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
Plus d'info : https://getcomposer.org/download/

#### Docker
```shell
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```
Plus d'info : https://docs.docker.com/engine/install/

#### Copie du .env.local en .env
```shell
cp .env.local .env
```

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
