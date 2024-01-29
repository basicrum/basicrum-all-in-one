#!/bin/bash
{
  set -o pipefail  # trace ERR through pipes
  set -o errtrace  # trace ERR through 'time command' and other functions
  set -o nounset   ## set -u : exit the script if you try to use an uninitialised variable
  set -o errexit   ## set -e : exit the script if any statement returns a non-true return value

  DOCKER_COMPOSE_YAML=$(cat <<- 'EOF'
version: "3.7"

services:
  basicrum_clickhouse_server:
    image: clickhouse/clickhouse-server:23.6.2.18-alpine
    healthcheck:
      test: ["CMD", "wget", "--spider", "-q", "localhost:8123/ping"]
      interval: 30s
      timeout: 5s
      retries: 3
    volumes:
      - ./shared/clickhouse:/var/lib/clickhouse
    ports:
      - 8143:8123
      - 9000:9000
    env_file:
      - basicrum_clickhouse_server.env
    ulimits:
      nproc: 65535
      nofile:
        soft: 262144
        hard: 262144
  front_basicrum_go:
    labels:
      # SSL endpoint
      - "traefik.http.routers.route-front_basicrum_go.entryPoints=port443"
      - "traefik.http.routers.route-front_basicrum_go.rule=host(`LETS_ENCRYPT_DOMAIN`) && (PathPrefix(`/beacon`) || PathPrefix(`/health`))"
      - "traefik.http.routers.route-front_basicrum_go.tls=true"
      - "traefik.http.routers.route-front_basicrum_go.tls.certResolver=le-ssl"
      - "traefik.http.routers.route-front_basicrum_go.service=route-front_basicrum_go"
      - "traefik.http.services.route-front_basicrum_go.loadBalancer.server.port=8087"
    image: basicrum/front_basicrum_go:FRONT_BASICRUM_GO_VERSION
    healthcheck:
      test: wget --no-verbose --tries=1 --spider http://localhost:8087/health || exit 1
      interval: 30s
      retries: 3
      start_period: 3s
      timeout: 10s
    env_file:
      - basicrum_clickhouse_server.env
      - front_basicrum_go.env
    depends_on:
      basicrum_clickhouse_server:
        condition: service_healthy
  basicrum_dashboard:
    labels:
      # SSL endpoint
      - "traefik.http.routers.route-https.entryPoints=port443"
      - "traefik.http.routers.route-https.rule=host(`LETS_ENCRYPT_DOMAIN`) && PathPrefix(`/grafana`)"
      - "traefik.http.routers.route-https.tls=true"
      - "traefik.http.routers.route-https.tls.certResolver=le-ssl"
      - "traefik.http.routers.route-https.service=route-https"
      - "traefik.http.services.route-https.loadBalancer.server.port=3000"
    image: basicrum/dashboard:DASHBOARD_VERSION
    env_file:
      - basicrum_clickhouse_server.env
      - basicrum_dashboard.env
    depends_on:
      basicrum_clickhouse_server:
        condition: service_healthy
    # volumes:
    # Persist user created stuff
    #- ./shared/grafana:/var/lib/grafana
    # Mount provisioning related stuff
    #- ./provisioning/grafana/datasources:/etc/grafana/provisioning/datasources
    # - ./provisioning/grafana/grafana.ini:/etc/grafana/grafana.ini
    #- ./provisioning/grafana/dashboards:/etc/grafana/provisioning/dashboards
  traefik:
    image: traefik:v3.0
    ports:
      - "443:443"
      # expose port below only if you need access to the Traefik API
      #- "8080:8080"
    command:
      #- "--log.level=DEBUG"
      #- "--api=true"
      - "--providers.docker=true"
      - "--entryPoints.port443.address=:443"
      - "--certificatesResolvers.le-ssl.acme.tlsChallenge=true"
      - "--certificatesResolvers.le-ssl.acme.email=LETS_ENCRYPT_EMAIL"
      - "--certificatesResolvers.le-ssl.acme.storage=/letsencrypt/acme.json"
    volumes:
      - ./shared/traefik:/letsencrypt/
      - /var/run/docker.sock:/var/run/docker.sock

EOF
)

  DASHBOARD_ENV=$(cat <<- 'EOF'
CLICKHOUSE_CONNECTION_URL=http://basicrum_clickhouse_server:8123

EOF
)

  FRONT_BASICRUM_GO_ENV=$(cat <<- 'EOF'
BRUM_SERVER_HOST=localhost
BRUM_SERVER_PORT=8087
BRUM_DATABASE_HOST=basicrum_clickhouse_server
BRUM_DATABASE_PORT=9000
BRUM_DATABASE_TABLE_PREFIX=
BRUM_PERSISTANCE_DATABASE_STRATEGY=all_in_one_db
BRUM_PERSISTANCE_TABLE_STRATEGY=all_in_one_table
BRUM_BACKUP_ENABLED=false
BRUM_BACKUP_DIRECTORY=/home/basicrum_archive
BRUM_BACKUP_INTERVAL_SECONDS=5

EOF
)

  SYSTEM_D_CONFIG=$(cat <<- 'EOF'
[Unit]
Description=%i service with docker compose
PartOf=docker.service
After=docker.service

[Service]
Type=oneshot
RemainAfterExit=true
WorkingDirectory=/etc/docker/compose/%i
ExecStart=/usr/bin/docker compose up -d --remove-orphans
ExecStop=/usr/bin/docker compose down

[Install]
WantedBy=multi-user.target

EOF
)

  DIR="$PWD"
  FRONT_BASICRUM_GO_VERSION=0.0.5
  DASHBOARD_VERSION=0.0.5

  install_docker_compose() {
    apt update -y
    apt install -y ca-certificates curl gnupg lsb-release wget sed
    mkdir -p /etc/apt/demokeyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --yes --dearmor -o /etc/apt/demokeyrings/demodocker.gpg
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/demokeyrings/demodocker.gpg] https://download.docker.com/linux/ubuntu \
      $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list
    apt update -y 
    apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
  }

  create_docker_compose_file() {
    FILE=${DIR}/docker-compose.yaml
    rm -rf $FILE
    echo "$DOCKER_COMPOSE_YAML" > $FILE

    sed -i "s#\FRONT_BASICRUM_GO_VERSION#$FRONT_BASICRUM_GO_VERSION#g" ${FILE}
    sed -i "s#\DASHBOARD_VERSION#$DASHBOARD_VERSION#g" ${FILE}
  }

  create_shared_directories() {
    mkdir -p ${DIR}/shared/clickhouse
    mkdir -p ${DIR}/shared/grafana
  }

  create_env_files() {
    db_name_default=default
    db_user_default=default
    db_pass_default=$(echo $RANDOM | md5sum | head -c 20)
    admin_user_default=gfadmin
    admin_pass_default=$(echo $RANDOM | md5sum | head -c 20)

    read -p "Enter clickhouse database name[${db_name_default}]: " db_name
    db_name=${db_name:-${db_name_default}}

    read -p "Enter clickhouse username[${db_user_default}]: " db_user
    db_user=${db_user:-${db_user_default}}

    read -p "Enter clickhouse password[${db_pass_default}]: " db_pass
    db_pass=${db_pass:-${db_pass_default}}

    read -p "Enter dashboard admin username[${admin_user_default}]: " admin_user
    admin_user=${admin_user:-${admin_user_default}}

    read -p "Enter dashboard admin password[${admin_pass_default}]: " admin_pass
    admin_pass=${admin_pass:-${admin_pass_default}}

    read -p "Enter ssl let's encrypt domain: " ssl_lets_encrypt_domain
    read -p "Enter ssl let's encrypt email: " ssl_lets_encrypt_email

    FILE=${DIR}/basicrum_clickhouse_server.env
    rm -rf $FILE
    echo -e "CLICKHOUSE_DB=${db_name}" >> $FILE
    echo -e "CLICKHOUSE_USER=${db_user}" >> $FILE
    echo -e "CLICKHOUSE_PASSWORD=${db_pass}" >> $FILE

    FILE=${DIR}/basicrum_dashboard.env
    rm -rf $FILE
    echo "$DASHBOARD_ENV" > $FILE
    echo -e "GF_SECURITY_ADMIN_USER=${admin_user}" >> $FILE
    echo -e "GF_SECURITY_ADMIN_PASSWORD=${admin_pass}" >> $FILE
    echo -e "GF_SERVER_ROOT_URL=https://${ssl_lets_encrypt_domain}/grafana/" >> $FILE
    echo -e "GF_SERVER_DOMAIN=${ssl_lets_encrypt_domain}" >> $FILE
    echo -e "GF_USERS_ALLOW_SIGN_UP=false" >> $FILE
    echo -e "GF_SERVER_SERVE_FROM_SUB_PATH=true" >> $FILE
    echo -e "CLICKHOUSE_USER=${db_user}" >> $FILE
    echo -e "CLICKHOUSE_PASSWORD=${db_pass}" >> $FILE

    FILE=${DIR}/front_basicrum_go.env
    rm -rf $FILE
    echo "$FRONT_BASICRUM_GO_ENV" > $FILE
    echo -e "BRUM_DATABASE_NAME=${db_name}" >> $FILE
    echo -e "BRUM_DATABASE_USERNAME=${db_user}" >> $FILE
    echo -e "BRUM_DATABASE_PASSWORD=${db_pass}" >> $FILE

    FILE=${DIR}/docker-compose.yaml
    sed -i "s#\LETS_ENCRYPT_DOMAIN#${ssl_lets_encrypt_domain}#g" ${FILE}
    sed -i "s#\LETS_ENCRYPT_EMAIL#${ssl_lets_encrypt_email}#g" ${FILE}
  }

  create_docker_compose_systemd() {
    FILE=/etc/systemd/system/docker-compose@.service
    rm -rf $FILE
    echo $SYSTEM_D_CONFIG > $FILE
  }

  install_basicrum() {
    mkdir -p $DIR
    create_docker_compose_file
    create_shared_directories
    create_env_files
  }

  install_systemd_basicrum() {
    systemctl enable docker-compose@basicrum
    systemctl daemon-reload
    systemctl start docker-compose@basicrum
  }

  install() {
    install_docker_compose
    # create_docker_compose_systemd
    install_basicrum
    # install_systemd_basicrum
  }

  start() {
    docker compose up -d
  }

  install
  start
}