# SAE401 - Cahier de texte | [![wakatime](https://wakatime.com/badge/user/655aa6eb-7c71-402f-b161-4ab28498501a/project/4bb73051-6ad3-469a-a2b2-911a73ad9be6.svg)](https://wakatime.com/badge/user/655aa6eb-7c71-402f-b161-4ab28498501a/project/4bb73051-6ad3-469a-a2b2-911a73ad9be6)
Membre du groupes : [BABALOLA Job-Faël](https://github.com/Kkeryyann), [BIROST Théo](https://github.com/TheoBirost), [BRUZEK Elouan](https://github.com/AloneDay-91), [GIRMA Valentin](https://github.com/Valgrm), [LOUIS Florent](https://github.com/Lewis3306)

## Présentation du projet

L'objectif est de concevoir une application web qui permet aux étudiants de centraliser et de suivre facilement les dates de rendu de leurs évaluations, travaux et autres échéances. Cette application ne servira pas à soumettre les travaux, mais uniquement à informer et rappeler les dates importantes.

## Cahier des charges

### Technologies utilisées
![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)
![MySQL](https://img.shields.io/badge/mysql-%2300758F.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Vue.js](https://img.shields.io/badge/vuejs-%2335495e.svg?style=for-the-badge&logo=vue.js&logoColor=%234FC08D)
![Tailwind CSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)
![JWT](https://img.shields.io/badge/JWT-black?style=for-the-badge&logo=JSON%20web%20tokens)

## Installation et initialisation du projet
### Partie Local
- Cloner le projet : `git clone https://github.com/AloneDay-91/SAE401.git`
- Installer les dépendances PHP pour le Back : `composer install`
- Installer les dépendances JS pour le Front (sae401/front) et le Back (sae401/back) : `npm install`
- Créer un fichier `.env.local` et configurer la connexion à la base de données ainsi que l'adresse sur serveur de mail
- Créer la base de données : `php bin/console doctrine:database:create`
- Créer les tables : `php bin/console doctrine:schema:update --force`
- Générer les fichiers jwt : `php bin/console lexik:jwt:generate-keypair`
- Changer l'url de l'api dans le fichier `.env` du dossier Front
- Build le Front (sae401/front) `npm run build` et le Back (sae401/back)
- Lancer le serveur front : `npm run dev` ou `npx vite`

### Partie Serveur
- Créer un dossier `sae401` dans le dossier `/var/www` de votre serveur
- Mettre le projet dans le dossier `sae401`
- Créer un fichier de configuration pour Apache
- Activer le site : `sudo a2ensite nom_du_fichier`

## Mise en production du projet
- Mettre sur le serveur de production votre projet Symfony, depuis GitHub ou en uploadant une archive (sans le dossier `vendor/` et `var/`).
- Créer un fichier `.env.local` et changer le `APP_ENV=dev` en `APP_ENV=prod`.
- Changer le mailer pour utiliser un serveur de mail (ex: `MAILER_DSN=smtp://user:password@host:port`)
- Pointer le serveur vers le dossier `public/`
- Installer les dépendances PHP`composer install --optimize-autoloader`
- Installer les dépendances JS si Webpack Encore `npm install --force`
- Compiler les assets en mode production : `npm run build`, pour webpack encore
- Mettre à jour les droits d'accès sur les dossiers `var/` et `public/` : `chmod -R 777 var/ public/`
