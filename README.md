# SnowTricks - Figures de Snowboard

## Presentation du projet
Site communautaire Snow Tricks réalisé avec [**Symfony 6**](https://symfony.com/).
Réalisé dans le cadre de la formation _développeur d'application PHP/symfony_ d'OpenClassrooms.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/383f44a9ae874cd2bfb1af4c0c20654d)](https://app.codacy.com/gh/kseb49/SnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Description
Site collaboratif dont l'objectif est de faire connaître le snowboard au grand public et d'aider à l'apprentissage des figures.

- Page d'accueil
    * Liste des Figures
- Page détaillant une figure
    * Formulaire pour commentaire
    * Liste des commentaires
- Page ajout d'une figure
- Page création compte
- Page connexion
- Page reset mdp

_[*voir en ligne](https://www.gloomaps.com/7sMwylrhb4)_

## Prè-requis

PHP
[**PHP 8.1**](https://www.php.net/downloads) ou supèrieur

MySQL
**MySQL 8.0** ou supèrieur.

Composer
[**Composer 2.4**](https://getcomposer.org/download/) ou supèrieur.

## Installation

Cloner le projet

```https://github.com/kseb49/SnowTricks.git```

Installer les dépendances

 ```composer install```

 Configurer le fichier .env avec vos valeurs:
 ```
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4
MAILER_DSN=smtp://user:pass@smtp.example.com:port
```
Pour une mise en production :

```
APP_ENV=prod
APP_SECRET=!new32characterskey!
```

 Créez la base de données et les tables:

```symfony console doctrine:database:create```

```symfony console doctrine:migrations:migrate```

Charger les données initiales

```symfony console doctrine:fixtures:load```