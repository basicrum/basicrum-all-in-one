# CONTRIBUTING

Hello and thank you for your interested in contributing to Basic RUM. We want a contributor to have a pleasant experience with the project and we prepared some useful hints for contributors:

Table of Contents:
- [Study the system](#1.Study-the-system)
- [Unit Testing](#2.-Unit-Testing)
- [Code Style](#3.-Code-Style)
- [Pull Requests](#4.-Pull-Requests)
 
## 1. Study the system

This step is not mandatory it could be really helpful for a new contributor who doesn't know much about Basicu RUM architecture.

We encourage everybody to take a look our wiki and some blog posts

**Wiki:**

* [System architecture diagrams](https://github.com/basicrum/backoffice/wiki/Architecture-diagrams)
* [Misc-Notes](https://github.com/basicrum/backoffice/wiki/Misc-Notes)


**Blog Posts:**

* [Open source backend RUM tool. Wait! What?](https://calendar.perfplanet.com/2018/open-source-backend-rum-tool-wait-what)
* [Lessons learned from developing my Real User Monitoring system – Basic RUM](https://calendar.perfplanet.com/2019/lessons-learned-from-developing-my-real-user-monitoring-system-basic-rum/)

## 2. Unit Testing

We already built Unit Tests that cover the main application logic. However, in order to avoid regressions we encourage everyone to run Unit Tests locally before a Pull Request is created. We do not expect from every contributor to be able to deliver code covered by unit tests but we are open and ready to assist. 

**2.1** If you develop locally without docker, from the root folder of the project run:

```php bin/phpunit```

**2.2** If you develop on Mac or Linux and you use Docker:

```docker exec -it basicrum_bo_php php bin/phpunit```

**2.3** If you develop on Windows, from the **basicrum_bo_php** container run this command from the project's root folder:

```php bin/phpunit```

**Note:** For every new Pull Request we run Travis CI build job that performs Unit Tests. If the unit tests are failing you will receive a notification.

## 3. Code Style:

We understand that every engineer has own code style. The experience shows that during a code review both sides could engage unnecessary discussions about code style. We find this toxic and unproductive and we decided to use Code Style Fixer in order to improve the review process. 
The Code Style Fixer will automatically correct code style and there are various options how to run the process:

**3.1** If you develop locally without docker, from the root folder of the project run:

```vendor/bin/php-cs-fixer fix --config=.php_cs --verbose```

**3.2** If you develop on Mac or Linux and you use Docker:

```docker exec -it basicrum_bo_php vendor/bin/php-cs-fixer fix --config=.php_cs --verbose```

**3.3** If you develop on Windows, from the **basicrum_bo_php** container run this command from the project's root folder:

```vendor/bin/php-cs-fixer fix --config=.php_cs --verbose```

**3.4** pre-commit git hook (currently working for NON Docker local setup): 
Create **.git/pre-commit:**
```bash
touch .git/pre-commit && chmod +x .git/pre-commit
```

Paste the following into ```.git/pre-commit```:   
```bash
#!/usr/bin/env bash

echo "pre-commit hook start"

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
        ${PHP_CS_FIXER} fix --config=.php_cs --verbose ${line};
        git add "$line";
    done
else
    echo ""
    echo "Please install php-cs-fixer, e.g.:"
    echo ""
    echo "  composer require friendsofphp/php-cs-fixer:2.0.0"
    echo ""
fi

cd $CURRENT_DIRECTORY;
echo "pre-commit hook finish"
```

## 4. Pull Requests:

 - Fork the Basic RUM repository.
 - In your fork create a new branch from the latest master branch.
 - Push to your fork and create a Pull Request to Basic RUM master.
 - In the Pull Request description write a basic information for a reviewer.

In case you are not familair with GitHub's Pull Request createion, please check this artice: [About pull requests](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/about-pull-requests)
