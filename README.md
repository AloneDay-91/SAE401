# SAE401 - Cahier de texte
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

### Fonctionnalités
En cours de réflexion

## Mise en production du projet
- Mettre sur le serveur de production votre projet Symfony, depuis GitHub ou en uploadant une archive (sans le dossier `vendor/` et `var/`).
- Créer un fichier `.env.local` et changer le `APP_ENV=dev` en `APP_ENV=prod`.
- Pointer le serveur vers le dossier `public/`
- Installer les dépendances PHP`composer install --optimize-autoloader`
- Installer les dépendances JS si Webpack Encore `npm install --force`
- Compiler les assets en mode production : `npm run build`, pour webpack encore
- Mettre à jour les droits d'accès sur les dossiers `var/` et `public/` : `chmod -R 777 var/ public/`