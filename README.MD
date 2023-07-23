# Basic RUM - All in One
## (open source Real User Web Performance Monitoring System)

## WIP

At the moment we are working on bundling all the Basic RUM components together. 

Components:

 - Dashboard - a re-branded Grafana frontend used to browse slice and dice the RUM data.
 - ClickHouse DB - a super fast database engine used for storing and querying the performance RUM data.
 - Front Basic Rum GO - a collector/an entry point that intercepts, filters and send the performance RUM data to ClickHouse DB.

