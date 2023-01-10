# SnowTricks
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/704cc132ce7d4daba3137710502bc62f)](https://www.codacy.com/gh/PavelKlimovich/SnowTricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=PavelKlimovich/SnowTricks&amp;utm_campaign=Badge_Grade)

## Installation

1. Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur SMTP ou adresse mail dans le fichier `.env.local` qui devra être crée à la racine du projet en réalisant une copie du fichier `.env`.

2. Téléchargez et installez les dépendances back-end du projet avec [Composer](https://getcomposer.org/download/) :
```
    composer install
```

3. Téléchargez et installez les dépendances front-end du projet avec [Npm](https://www.npmjs.com/get-npm) :
```
    npm install
```

4. Créer un build d'assets avec [Npm](https://www.npmjs.com/get-npm) :
```
    npm run build
```

5. Créez la base de données si elle n'existe pas déjà, taper la commande ci-dessous en vous plaçant dans le répertoire du projet :
```
    php bin/console doctrine:database:create
```

6. Créez les différentes tables de la base de données en appliquant les migrations :
```
    php bin/console doctrine:migrations:migrate
```

7. Installer les fixtures pour avoir des données fictives :
```
    php bin/console doctrine:fixtures:load
```
