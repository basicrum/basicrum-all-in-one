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
rm may-july-2019.sql.gz
```
Linux:  Load http://127.0.0.1:8086 in your browser

Mac OS with docker machine: Run `docker-machine ip` and load http://(put docker ip here):8086

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
