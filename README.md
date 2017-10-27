Starting5 powered by Symfony3
=========

INSTALLATION
=========

Cloner le projet
=========
    git clone https://github.com/eungtony/starting5-project.git
 
Créer le .env à la racine du projet
=========
 
    MYSQL_ROOT_PASSWORD=  
    MYSQL_DATABASE=starting5  
    MYSQL_USER=  
    MYSQL_PASSWORD=
 
 
Lancer les commandes:
=========
`Docker-compose build`   
`Docker-compose up -d`
 
Composer install
=========
 
 Dans le terminal, accéder à la racine du projet, et lancer la commande ‘composer Install’
 
 Pour finir le composer Install
  
    ‘DATABASE_HOST’ = db
    ‘DATABASE_NAME’ = starting5  
    ‘DATABASE_PASSWORD’={MYSQL_PASSWORD}
 
 Si l'erreur ci-dessous pète après la création du parameters.yml
 
    [Symfony\Component\Debug\Exception\ContextErrorException]
    Warning: date_default_timezone_get(): It is not safe to rely on the system's timezone settings.
    You are *required* to use the date.timezone setting or the date_default_timezone_set() function.
    In case you used any of those methods and you are still getting this warning, you most likely
    misspelled the timezone identifier. We selected the timezone 'UTC' for now, 
    but please set date.timezone to select your timezone.  
      
Ajouter la ligne suivante à votre php.ini

    date.timezone = "Europe/Paris"
 
Accéder à ‘localhost:88’
=========
