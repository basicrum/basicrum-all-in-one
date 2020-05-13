# Basic RUM - backoffice
## (open source Real User Web Performance Monitoring System)

The backoffice of Basic RUM. A system written on Symfony 5 that aims to help performance enthusiasts to look at performance metrics and identify performance bottlenecks. Hooray!

![alt Basic RUM dashboard](https://user-images.githubusercontent.com/1024001/62764696-cb461180-ba8e-11e9-9faa-f4beb0c0ee56.jpeg)

## I want to contribute!

Checkout the [contributors notes](./CONTRIBUTING.md)

## Installation:
The instruction below are applicable only for development but still incomplete for production. This installation will be automatically initialized with demo database.
Init script will ask you to create an admin user during the process of
 installation.
```
git clone git@github.com:basicrum/backoffice.git
cd backoffice
make init
```

If you would like to preload some data, you can do it by executing :
```
make demo
```

## Accessing Basic RUM and its local database:

 - **Basic RUM:** http://127.0.0.1:8086
 - **PhpMyAdmin:** http://127.0.0.1:8087

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
