# Basic RUM - All in One
## (open source Real User Web Performance Monitoring System)

## WIP

At the moment we are working on bundling all the Basic RUM components together. 

Components:

 - Dashboard - a re-branded Grafana frontend used to browse slice and dice the RUM data.
 - ClickHouse DB - a super fast database engine used for storing and querying the performance RUM data.
 - Front Basic Rum GO - a collector/an entry point that intercepts, filters and send the performance RUM data to ClickHouse DB.



## Start

To start the docker compose as service in background run:
```
make up
```

Open browser:
```
http://localhost:3500
```

You should see dashboard login page.
Enter username and password from file `basicrum_dashboard.env` environment variables (`GF_SECURITY_ADMIN_USER` and `GF_SECURITY_ADMIN_PASSWORD`).

## Logs

to check the docker compose logs run:
```
make logs
```

## Send flare

To send test data as browser:
```
make flare
```

## Stop

To stop the docker compose run:
```
make down
```
