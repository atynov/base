
# Requirements 
* PHP 8.0 or higher
* Yii2 v.2.0.47 and newest

# Installation
To install the app, run the following command`s in the console:

    $ composer install
    
    $ php init
    
    
    $ php init --env=Development --overwrite=All --delete=All
    
    OR
    
    $ php init --env=Production --overwrite=All --delete=All
    
    $ php yii migrate --migrationPath=@yii/rbac/migrations
    
    $ php yii migrate
    
    $ php yii rbac/init


# Directories for write

    /backend/web/assets
    
    /backend/web/uploads

    /backend/runtime

    /frontend/web/assets
    
    /frontend/web/uploads

    /frontend/runtime


# Добавление модуля 
--\backend\config\main.php  - доб в массив 'bootstrap'
--\common\config\main.php - доб в массив 'modules'
--\backend\config\main.php - доб в массив 'modules'
если есть миграция то \console\config\main.php - доб в массив 'migrationNamespaces'

# Создание мирации в модуле
    -- php yii migrate/create notification_add_record --migrationPath=@modules/notification/migrations
    

