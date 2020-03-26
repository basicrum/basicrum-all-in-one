# Basic RUM - backoffice
## (open source Real User Web Performance Monitoring System)

Backoffice of Basic RUM. A system written on Symfony 4 that aims to help performance enthusiasts look at performance metrics and identify performance bottlenecks. Hooray!

![alt Basic RUM dashboard](https://user-images.githubusercontent.com/1024001/62764696-cb461180-ba8e-11e9-9faa-f4beb0c0ee56.jpeg)

## Installation:
The instruction below are applicable only for development but still incomplete for production. This installation will be automatically initialized with demo database.
```
git clone git@github.com:basicrum/backoffice.git
cd backoffice
docker-compose -f docker/docker-compose.yml build --no-cache
docker-compose -f docker/docker-compose.yml up -d
docker exec basicrum_bo_php composer update symfony/flex --no-plugins --no-scripts
curl https://www.revampix.com/basic_rum/test_data/may-july-2019.sql.gz -o may-july-2019.sql.gz
gunzip -k may-july-2019.sql.gz
cat may-july-2019.sql | docker exec -i basicrum_bo_mysql sh -c 'mysql -uroot -prootsecret'
docker exec -it basicrum_bo_php php bin/console c:c
docker exec -it basicrum_bo_php php bin/console basicrum:cache:clean
docker exec -it basicrum_bo_php php bin/console basicrum:beacon:init-folders
rm may-july-2019.sql.gz
```

Need to issue the following two commands by cron, in order to automaticaly process beacons:
```
docker exec -it basicrum_bo_php php bin/console basicrum:beacon:bundle-raw
docker exec -it basicrum_bo_php php bin/console basicrum:beacon:import-bundle
```

Now with symfony/webpack-encore-bundle need to create manifest.json file. Here is how-to:
```
mkdir public/build
echo "{}" > public/build/manifest.json
```
### PHP-CS-FIXER  
In order to continue development need to configure [php_cs_fixer](https://github.com/FriendsOfPhp/PHP-CS-Fixer). It will be installed together with other project dependencies. Now need to add pre-commit git hook:  
```bash
touch .git/pre-commit && chmod +x .git/pre-commit
```

and paste the following into ```.git/pre-commit```:   
```bash
#!/usr/bin/env bash

echo "pre commit hook start"

CURRENT_DIRECTORY=`pwd`
GIT_HOOKS_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

PROJECT_DIRECTORY="$GIT_HOOKS_DIR/../.."

cd $PROJECT_DIRECTORY;
PHP_CS_FIXER="vendor/bin/php-cs-fixer"

HAS_PHP_CS_FIXER=false

if [ -x "$PHP_CS_FIXER" ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    git status --porcelain | grep -e '^[AM]\(.*\).php$' | cut -c 3- | while read line; do
        ${PHP_CS_FIXER} fix --config-file=.php_cs --verbose ${line};
        git add "$line";
    done
else
    echo ""
    echo "Please install php-cs-fixer, e.g.:"
    echo ""
    echo "  composer require friendsofphp/php-cs-fixer:2.0.0"
    echo ""
fi

cd $CURRENT_DIRECTORY;
echo "pre commit hook finish"

```


After installation you need to create a first - super admin user:
```
docker exec -it basicrum_bo_php php bin/console basicrum:superadmin:create
```
Provide required information and feel free to login at the following address:

Linux:  Load http://127.0.0.1:8086 in your browser

Mac OS with docker machine: Run `docker-machine ip` and load http://(put docker ip here):8086

Once logged in you can create new users. For that click humburger menu in top left corner and click to Manage Users. And there click create user button in order to create your first user.


## Key features:
 - Performance over time by Mobile, Tablet and Desktop devices.
 - Diagram Generator by metrics like **time to first paing**, **time to first byte**, **document ready** and etc.
 - Waterfall visualization of loaded page resources
 - Device distribution diagram.
 - Boomerang JS agent builder.
 - Adding release dates in order to track performance changes before and after releases.
 - and more...

## Performance over time:
![alt Perofrmance over time by devices](https://user-images.githubusercontent.com/1024001/62764918-4d363a80-ba8f-11e9-81d1-8392165c4cad.png)

## Diagram Generator
![alt Diagram Generator - Time To First Paint](https://user-images.githubusercontent.com/1024001/62765008-7f479c80-ba8f-11e9-8eb6-ccd50b9fbf3e.png)

## Waterfall visualization
![alt Page Resouces waterfall diagram](https://user-images.githubusercontent.com/1024001/62765059-9f775b80-ba8f-11e9-92cc-bc693b2806cc.png)

## Boomerang JS - Agent Builder
![alt Boomerang JS - Agent Builder](https://user-images.githubusercontent.com/1024001/62765086-b61db280-ba8f-11e9-93fb-8cc200276c0f.png)
