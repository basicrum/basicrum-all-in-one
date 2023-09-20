#!/bin/bash
{
  set -o pipefail  # trace ERR through pipes
  set -o errtrace  # trace ERR through 'time command' and other functions
  set -o nounset   ## set -u : exit the script if you try to use an uninitialised variable
  set -o errexit   ## set -e : exit the script if any statement returns a non-true return value

  DIR=/etc/docker/compose/basicrum

  install_docker_compose() {
    apt update -y
    apt install -y ca-certificates curl gnupg lsb-release wget
    mkdir -p /etc/apt/demokeyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --yes --dearmor -o /etc/apt/demokeyrings/demodocker.gpg
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/demokeyrings/demodocker.gpg] https://download.docker.com/linux/ubuntu \
      $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list
    apt update -y 
    apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
  }

  create_docker_compose_file() {
    FILE_BASE64=dmVyc2lvbjogIjMuNyIKCnNlcnZpY2VzOgogIGJhc2ljcnVtX2NsaWNraG91c2Vfc2VydmVyOgogICAgaW1hZ2U6IGNsaWNraG91c2UvY2xpY2tob3VzZS1zZXJ2ZXI6MjMuNi4yLjE4LWFscGluZQogICAgaGVhbHRoY2hlY2s6CiAgICAgIHRlc3Q6IFsiQ01EIiwgIndnZXQiLCAiLS1zcGlkZXIiLCAiLXEiLCAibG9jYWxob3N0OjgxMjMvcGluZyJdCiAgICAgIGludGVydmFsOiAzMHMKICAgICAgdGltZW91dDogNXMKICAgICAgcmV0cmllczogMwogICAgdm9sdW1lczoKICAgICAgLSAuL3NoYXJlZC9jbGlja2hvdXNlOi92YXIvbGliL2NsaWNraG91c2UKICAgIHBvcnRzOgogICAgICAtIDgxNDM6ODEyMwogICAgICAtIDkwMDA6OTAwMAogICAgZW52X2ZpbGU6CiAgICAgIC0gYmFzaWNydW1fY2xpY2tob3VzZV9zZXJ2ZXIuZW52CiAgICB1bGltaXRzOgogICAgICBucHJvYzogNjU1MzUKICAgICAgbm9maWxlOgogICAgICAgIHNvZnQ6IDI2MjE0NAogICAgICAgIGhhcmQ6IDI2MjE0NAogIGZyb250X2Jhc2ljcnVtX2dvOgogICAgaW1hZ2U6IGJhc2ljcnVtL2Zyb250X2Jhc2ljcnVtX2dvOjAuMC4yCiAgICBoZWFsdGhjaGVjazoKICAgICAgdGVzdDogd2dldCAtLW5vLXZlcmJvc2UgLS10cmllcz0xIC0tc3BpZGVyIGh0dHA6Ly9sb2NhbGhvc3Q6ODA4Ny9oZWFsdGggfHwgZXhpdCAxCiAgICAgIGludGVydmFsOiAzMHMKICAgICAgcmV0cmllczogMwogICAgICBzdGFydF9wZXJpb2Q6IDNzCiAgICAgIHRpbWVvdXQ6IDEwcwogICAgcG9ydHM6CiAgICAgIC0gODA4Nzo4MDg3CiAgICAgIC0gNDQzOjQ0MwogICAgZW52X2ZpbGU6CiAgICAgIC0gYmFzaWNydW1fY2xpY2tob3VzZV9zZXJ2ZXIuZW52CiAgICAgIC0gZnJvbnRfYmFzaWNydW1fZ28uZW52CiAgICBkZXBlbmRzX29uOgogICAgICBiYXNpY3J1bV9jbGlja2hvdXNlX3NlcnZlcjoKICAgICAgICBjb25kaXRpb246IHNlcnZpY2VfaGVhbHRoeQogIGJhc2ljcnVtX2Rhc2hib2FyZDoKICAgIGltYWdlOiBiYXNpY3J1bS9kYXNoYm9hcmQ6MC4wLjMKICAgICMgV2UgbmVlZCB0aGlzIGluIG9yZGVyIHRvIG1ha2UgdGhlIGdyYWZhbmEgY29udGFpbmVyIGhhcHB5IHdoZW4gd2UgaGF2ZSB2b2x1bWVzLgogICAgIyBJZiB3ZSBkb24ndCBzZXQgdGhlIHVzZXIgdGhlbiB3ZSBnZXQ6CiAgICAjIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiAgICAjIGJhc2ljcnVtX2dyYWZhbmFfMSAgICAgICAgICAgIHwgR0ZfUEFUSFNfREFUQT0nL3Zhci9saWIvZ3JhZmFuYScgaXMgbm90IHdyaXRhYmxlLgogICAgIyBiYXNpY3J1bV9ncmFmYW5hXzEgICAgICAgICAgICB8IFlvdSBtYXkgaGF2ZSBpc3N1ZXMgd2l0aCBmaWxlIHBlcm1pc3Npb25zLCBtb3JlIGluZm9ybWF0aW9uIGhlcmU6IGh0dHA6Ly9kb2NzLmdyYWZhbmEub3JnL2luc3RhbGxhdGlvbi9kb2NrZXIvI21pZ3JhdGUtdG8tdjUxLW9yLWxhdGVyCiAgICAjIGJhc2ljcnVtX2dyYWZhbmFfMSAgICAgICAgICAgIHwgbWtkaXI6IGNhbm5vdCBjcmVhdGUgZGlyZWN0b3J5ICcvdmFyL2xpYi9ncmFmYW5hL3BsdWdpbnMnOiBQZXJtaXNzaW9uIGRlbmllZAogICAgIyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQogICAgIwogICAgIyAkVUlEIGlzIHBhc3NlZCBieSB0aGUgaG9zdDoKICAgICMKICAgICMgMS58IFVJRCA6PSAkKHNoZWxsIGlkIC11KQogICAgIyAyLnwgZW52IFVJRD0ke1VJRH0gZG9ja2VyLWNvbXBvc2UgLWYgJHtkY19wYXRofSB1cCAtZAogICAgIwogICAgIyB1c2VyOiAiJFVJRCIKICAgIHBvcnRzOgogICAgICAtIDM1MDA6MzAwMAogICAgZW52X2ZpbGU6CiAgICAgIC0gYmFzaWNydW1fY2xpY2tob3VzZV9zZXJ2ZXIuZW52CiAgICAgIC0gYmFzaWNydW1fZGFzaGJvYXJkLmVudgogICAgZGVwZW5kc19vbjoKICAgICAgYmFzaWNydW1fY2xpY2tob3VzZV9zZXJ2ZXI6CiAgICAgICAgY29uZGl0aW9uOiBzZXJ2aWNlX2hlYWx0aHkKICAgICMgdm9sdW1lczoKICAgICMgUGVyc2lzdCB1c2VyIGNyZWF0ZWQgc3R1ZmYKICAgICMtIC4vc2hhcmVkL2dyYWZhbmE6L3Zhci9saWIvZ3JhZmFuYQogICAgIyBNb3VudCBwcm92aXNpb25pbmcgcmVsYXRlZCBzdHVmZgogICAgIy0gLi9wcm92aXNpb25pbmcvZ3JhZmFuYS9kYXRhc291cmNlczovZXRjL2dyYWZhbmEvcHJvdmlzaW9uaW5nL2RhdGFzb3VyY2VzCiAgICAjIC0gLi9wcm92aXNpb25pbmcvZ3JhZmFuYS9ncmFmYW5hLmluaTovZXRjL2dyYWZhbmEvZ3JhZmFuYS5pbmkKICAgICMtIC4vcHJvdmlzaW9uaW5nL2dyYWZhbmEvZGFzaGJvYXJkczovZXRjL2dyYWZhbmEvcHJvdmlzaW9uaW5nL2Rhc2hib2FyZHMK
    FILE=${DIR}/docker-compose.yaml
    rm -rf $FILE
    echo $FILE_BASE64 | base64 --decode > $FILE
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
    ssl_default=false
    ssl_type_default=LETS_ENCRYPT
    ssl_cert_file=
    ssl_key_key=

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

    read -p "Do you want SSL? [${ssl_default}] (true/false): " ssl
    ssl=${ssl:-${ssl_default}}

    if [ $ssl == 'true' ]
    then
      read -p "Enter ssl type [${ssl_type_default}] (LETS_ENCRYPT/FILE): " ssl_type
      ssl_type=${ssl_type:-${ssl_type_default}}

      if [ $ssl_type == 'LETS_ENCRYPT' ]
      then
        read -p "Enter ssl let's encrypt domain: " ssl_lets_encrypt_domain
      fi

      if [ $ssl_type == 'FILE' ]
      then
        read -p "Enter ssl cert file: " ssl_cert_file
        read -p "Enter ssl key file: " ssl_key_key
      fi

    fi

    FILE=${DIR}/basicrum_clickhouse_server.env
    rm -rf $FILE
    echo -e "CLICKHOUSE_DB=${db_name}" >> $FILE
    echo -e "CLICKHOUSE_USER=${db_user}" >> $FILE
    echo -e "CLICKHOUSE_PASSWORD=${db_pass}" >> $FILE

    FILE_BASE64=Q0xJQ0tIT1VTRV9DT05ORUNUSU9OX1VSTD1odHRwOi8vYmFzaWNydW1fY2xpY2tob3VzZV9zZXJ2ZXI6ODEyMwpDTElDS0hPVVNFX1VTRVI9JHtDTElDS0hPVVNFX1VTRVJ9CkNMSUNLSE9VU0VfUEFTU1dPUkQ9JHtDTElDS0hPVVNFX1BBU1NXT1JEfQo=
    FILE=${DIR}/basicrum_dashboard.env
    rm -rf $FILE
    echo $FILE_BASE64 | base64 --decode > $FILE
    echo -e "GF_SECURITY_ADMIN_USER=${admin_user}" >> $FILE
    echo -e "GF_SECURITY_ADMIN_PASSWORD=${admin_pass}" >> $FILE

    FILE_BASE64=QlJVTV9TRVJWRVJfSE9TVD1sb2NhbGhvc3QKQlJVTV9TRVJWRVJfUE9SVD04MDg3CkJSVU1fREFUQUJBU0VfSE9TVD1iYXNpY3J1bV9jbGlja2hvdXNlX3NlcnZlcgpCUlVNX0RBVEFCQVNFX1BPUlQ9OTAwMApCUlVNX0RBVEFCQVNFX05BTUU9JHtDTElDS0hPVVNFX0RCfQpCUlVNX0RBVEFCQVNFX1VTRVJOQU1FPSR7Q0xJQ0tIT1VTRV9VU0VSfQpCUlVNX0RBVEFCQVNFX1BBU1NXT1JEPSR7Q0xJQ0tIT1VTRV9QQVNTV09SRH0KQlJVTV9EQVRBQkFTRV9UQUJMRV9QUkVGSVg9CkJSVU1fUEVSU0lTVEFOQ0VfREFUQUJBU0VfU1RSQVRFR1k9YWxsX2luX29uZV9kYgpCUlVNX1BFUlNJU1RBTkNFX1RBQkxFX1NUUkFURUdZPWFsbF9pbl9vbmVfdGFibGUKQlJVTV9CQUNLVVBfRU5BQkxFRD1mYWxzZQpCUlVNX0JBQ0tVUF9ESVJFQ1RPUlk9L2hvbWUvYmFzaWNydW1fYXJjaGl2ZQpCUlVNX0JBQ0tVUF9JTlRFUlZBTF9TRUNPTkRTPTUK
    FILE=${DIR}/front_basicrum_go.env
    rm -rf $FILE
    echo $FILE_BASE64 | base64 --decode > $FILE
    echo -e "BRUM_SERVER_SSL=${ssl}" >> $FILE
    echo -e "BRUM_SERVER_SSL_TYPE=${ssl_type}" >> $FILE
    echo -e "BRUM_SERVER_SSL_LETS_ENCRYPT_DOMAIN=${ssl_lets_encrypt_domain}" >> $FILE
    echo -e "BRUM_SERVER_SSL_CERT_FILE=${ssl_cert_file}" >> $FILE
    echo -e "BRUM_SERVER_SSL_KEY_FILE=${ssl_key_key}" >> $FILE
  }

  create_docker_compose_systemd() {
    FILE_BASE64=W1VuaXRdCkRlc2NyaXB0aW9uPSVpIHNlcnZpY2Ugd2l0aCBkb2NrZXIgY29tcG9zZQpQYXJ0T2Y9ZG9ja2VyLnNlcnZpY2UKQWZ0ZXI9ZG9ja2VyLnNlcnZpY2UKCltTZXJ2aWNlXQpUeXBlPW9uZXNob3QKUmVtYWluQWZ0ZXJFeGl0PXRydWUKV29ya2luZ0RpcmVjdG9yeT0vZXRjL2RvY2tlci9jb21wb3NlLyVpCkV4ZWNTdGFydD0vdXNyL2Jpbi9kb2NrZXIgY29tcG9zZSB1cCAtZCAtLXJlbW92ZS1vcnBoYW5zCkV4ZWNTdG9wPS91c3IvYmluL2RvY2tlciBjb21wb3NlIGRvd24KCltJbnN0YWxsXQpXYW50ZWRCeT1tdWx0aS11c2VyLnRhcmdldA==
    FILE=/etc/systemd/system/docker-compose@.service
    rm -rf $FILE
    echo $FILE_BASE64 | base64 --decode > $FILE
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
    create_docker_compose_systemd
    install_basicrum
    install_systemd_basicrum
  }

  install
}