# Basic RUM - All in One

This is a command line installation wizard of Basic RUM.

The **Basic RUM - All in One setup** aims to simplify the installation run all the required services on the same box. This approach of course has tradeoffs and is not meant for scaling and running **Basic RUM** on multiple servers.

However, the **All in One setup** is perfect for a small website/organization that doesn't have an Ops team and has less than **50 000** page visits a day.

## How to use?

For Debian based Linux download and run:

```shell
debian-install-basicrum-all-in-one.sh
```

## What will be installed?

The installation wizard installs Docker Compose and runs the required four Basic RUM Docker containers:
 * Traefik
   * Let's Encrypt
   * SSL termination
   * Routing of various paths
 * ClickHouse
   * Timeseries data storage
 * Dashboard
   * Customized Grafana for displaying the RUM data.
 * FRONT_BASICRUM_GO
   * Collecting beacon data
   * Transforming beacon data
   * Inserting
