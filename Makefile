.PHONY: help
.DEFAULT_GOAL := help
SHELL=bash

UID := $(shell id -u)

dc_path=./docker-compose.yaml
clickhouse_container=basicrum_clickhouse_server
grafana_container=basicrum_grafana

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

up: ## Starts the environment
	env UID=${UID} docker-compose -f ${dc_path} build
	env UID=${UID} docker-compose -f ${dc_path} up -d

down: ## Stops the environment
	env UID=${UID}  docker-compose -f ${dc_path} down

logs: ## prints the logs
	docker-compose -f ${dc_path} logs

restart: down up # Restart the environment

rebuild: ## Rebuilds the environment from scratch
	@/bin/echo -n "All the volumes will be deleted. You will loose data in DB. Are you sure? [y/N]: " && read answer && \
	[[ $${answer:-N} = y ]] && make destroy

destroy: ## Destroys thel environment
	docker-compose -f ${dc_path} down --volumes --remove-orphans
	docker-compose -f ${dc_path} rm -vsf

jump_clickhouse: ## Jump to the ClickHouse container
	docker-compose -f ${dc_path} exec ${clickhouse_container} bash

jump_grafana: ## Jump to the Grafana container
	docker-compose -f ${dc_path} exec ${grafana_container} bash

prepare_grafana_plugins: ## Installs the Grafana plugins
#TODO: Add check if the directory already exists and if the plugin version is the same
	tar -xf ./artefacts/grafana/plugins/ae3e-plotly-panel-0.5.0.tar.gz --directory ./shared/grafana/plugins/

prepare_clickhouse_datasource_plugins: ## Installs the ClickHouse data source plugins
#TODO: Add check if the directory already exists and if the plugin version is the same
	tar -xf ./artefacts/grafana/plugins/vertamedia-clickhouse-datasource-2.5.1.tar.gz --directory ./shared/grafana/plugins/

grafana_init: prepare_grafana_plugins prepare_clickhouse_datasource_plugins

flare:
	curl -v 'http://127.0.0.1:8087/beacon/catcher' \
		-H 'authority: beacon.basicrum.com' \
		-H 'accept: */*' \
		-H 'accept-language: en-US,en;q=0.9' \
		-H 'cache-control: no-cache' \
		-H 'content-type: application/x-www-form-urlencoded' \
		-H 'origin: https://calendar.perfplanet.com' \
		-H 'pragma: no-cache' \
		-H 'referer: https://calendar.perfplanet.com/' \
		-H 'sec-fetch-dest: empty' \
		-H 'sec-fetch-mode: no-cors' \
		-H 'sec-fetch-site: cross-site' \
		-H 'user-agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1' \
		--data-raw 'mob.etype=4g&mob.dl=10&mob.rtt=50&c.e=l544045p&c.tti.m=lt&pt.lcp=1234&rt.start=navigation&rt.bmr=1981%2C226%2C221%2C6&rt.tstart=1656779946253&rt.bstart=1656779948466&rt.blstart=1656779948233&rt.end=1656779948292&t_resp=941&t_page=1098&t_done=2039&t_other=boomerang%7C25%2Cboomr_fb%7C2213%2Cboomr_ld%7C1980%2Cboomr_lat%7C233&rt.tt=2039&rt.obo=0&pt.fp=1234&pt.fcp=1234&nt_nav_st=1656779946253&nt_red_st=1656779946262&nt_red_end=1656779946977&nt_fet_st=1656779946977&nt_dns_st=1656779946977&nt_dns_end=1656779946977&nt_con_st=1656779946977&nt_con_end=1656779946977&nt_req_st=1656779946981&nt_res_st=1656779947194&nt_res_end=1656779947207&nt_domloading=1656779947229&nt_domint=1656779948234&nt_domcontloaded_st=1656779948234&nt_domcontloaded_end=1656779948243&nt_domcomp=1656779948292&nt_load_st=1656779948292&nt_load_end=1656779948292&nt_ssl_st=1656779946977&nt_enc_size=12680&nt_dec_size=47273&nt_trn_size=12980&nt_protocol=h2&nt_first_paint=1656779947487&nt_red_cnt=1&nt_nav_type=0&restiming=%7B%22https%3A%2F%2Fcalendar.perfplanet.com%2F%22%3A%7B%22wp-includes%2F%22%3A%7B%22js%2F%22%3A%7B%22wp-embed.min.js%3Fver%3D5.8.4%22%3A%223ri%2Cre%2Cq1%2C4s*1l9%2C_%2Cid*24%22%2C%22wp-emoji-release.min.js%3Fver%3D5.8.4%22%3A%223ws%2Ch7%2Cgv%2Cw*13uu%2C8c%2Ca67*23%22%7D%2C%22css%2Fdist%2Fblock-library%2Fstyle.min.css%3Fver%3D5.8.4%22%3A%222re%2C17%2C12%2Cy*18gp%2C_%2C1hph*44%22%7D%2C%22photos%2F%22%3A%7B%22alexrussell-70tr.jpg%22%3A%22*01y%2C1y%2Ca7%2C103%2C5u%2C5u%7C1rf%2C8e%2C8c%2C5n*17oz%2C_%22%2C%22yoav2016-70tr.jpg%22%3A%22*02d%2C1y%2Cij%2C103%2C2e%2C1y%7C1rf%2Cb1%2Cav%2C5s*13e7%2C_%22%2C%22paulcalvano-70tr.jpg%22%3A%22*01y%2C1y%2Cra%2C103%2C5u%2C5u%7C1rf%2Cbe%2Cb8%2C5t*15el%2C_%22%2C%22nic-70tr.jpg%22%3A%22*01y%2C1y%2Cyt%2C103%7C1rf%2Cgd%2Ccm%2C5t*14kr%2C_%22%2C%22annie-70tr.jpg%22%3A%22*01y%2C1y%2C17y%2C103%2C5u%2C5u%7C1rf%2Cii%2Ccy%2C5w*18r7%2C_%22%2C%22jana-70tr.jpg%22%3A%22*01y%2C1y%2C1ga%2C103%2C5u%2C5u%7C1rf%2Cko%2Ccy%2C5w*195r%2C_%22%2C%22andrea2021-70tr.jpg%22%3A%22*01y%2C1y%2C1nt%2C103%2C5u%2C5u%7C1rf%2Ckr%2Ccy%2C5y*13wr%2C_%22%2C%22richcecko-70tr.jpg%22%3A%22*011%2C1y%2C1w5%2C103%2C2x%2C5u%7C1rg%2Cms%2Ccx%2C5x*1492%2C_%22%2C%22alex-70tr.jpg%22%3A%22*02k%2C1y%2C24h%2C103%2C2l%2C1y%7C1rg%2Cms%2Ccx%2C5x*12p1%2C_%22%2C%22meiert-70tr.jpg%22%3A%22*01y%2C1y%2C2df%2C103%2C5u%2C5u%7C1rg%2Cmt%2Cm7%2C5x*12uo%2C_%22%2C%22artem2021-70tr.jpg%22%3A%22*01y%2C1y%2C2lr%2C103%2C5u%2C5u%7C1rg%2Cmv%2Cms%2C5x*17ae%2C_%22%2C%22Tim-Vereecke-70tr.jpg%22%3A%22*02j%2C1y%2C2ta%2C103%2C2k%2C1y%7C1rg%2Cmv%2Cm7%2C5x*113v%2C_%22%2C%22peter2015-70tr.jpg%22%3A%22*01x%2C1y%2C327%2C103%2C1x%2C1y%7C1rg%2Cmv%2Cmt%2C5x*11k0%2C_%22%2C%22robin2021-70tr.jpg%22%3A%22*01z%2C1y%2C3b0%2C103%2C8w%2C8r%7C1rg%2Cn4%2Cmj%2C5x*1d9m%2C_%22%2C%22amiya-70tr.jpg%22%3A%22*025%2C1y%2C3jd%2C103%2C4b%2C3w%7C1rg%2Cn6%2Cmj%2C5x*122y%2C_%22%2C%22kanmi-70tr.jpg%22%3A%22*01y%2C1y%2C3sz%2C103%2C79%2C78%7C1rh%2Cn5%2Cmi%2C5w*16kz%2C_%22%2C%22brunosabot-70tr.jpg%22%3A%22*01y%2C1y%2C42l%2C103%2C8w%2C8w%7C1rh%2Cng%2Cmi%2C5x*16ch%2C_%22%2C%22stoyan2015-70tr.jpg%22%3A%22*01s%2C1y%2C4c7%2C103%2Cc6%2Cdh%7C1rh%2Cnv%2Cmi%2C5x*1g01%2C_%22%2C%22hongbo-70tr.jpg%22%3A%22*01z%2C1y%2C4kj%2C103%2C5w%2C5u%7C1rh%2Co6%2Cmi%2C5x*14kx%2C_%22%2C%22amit-70tr.jpg%22%3A%22*02c%2C1y%2C53r%2C103%2C74%2C5u%7C1rh%2Cq3%2Cmi%2C5x*15j9%2C_%22%2C%22robertb-70tr.jpg%22%3A%22*027%2C1y%2C5mz%2C103%2C4g%2C3w%7C1rh%2Cqa%2Cmi%2C5x*14a9%2C_%22%2C%22leon2021-70tr.jpg%22%3A%22*022%2C1y%2C5vk%2C103%2C67%2C5u%7C1rh%2Cqa%2Cmi%2C5x*14df%2C_%22%2C%22krasimir-70tr.jpg%22%3A%22*01y%2C1y%2C6t8%2C103%2C5u%2C5u%7C1ri%2Cr9%2Cmh%2C5w*14z8%2C_%22%2C%22leonb-70tr.jpg%22%3A%22*01y%2C1y%2C72u%2C103%2C50%2C50%7C1ri%2Cr9%2Cns%2C5w*12xl%2C_%22%2C%22barry-70tr.jpg%22%3A%22*026%2C1y%2C7bz%2C103%2C4e%2C3w%7C1ri%2Crc%2Cns%2C5w*13dm%2C_%22%2C%22tanner-70tr.jpg%22%3A%22*01y%2C1y%2C7sw%2C103%2C5u%2C5u%7C1ri%2Crd%2Co4%2C5w*180h%2C_%22%2C%22erwin-70tr.jpg%22%3A%22*01y%2C1y%2C82i%2C103%2C5u%2C5u%7C1ri%2Crd%2Cq1%2C5w*16m5%2C_%22%2C%22matt-70tr.jpg%22%3A%22*025%2C1y%2C8au%2C103%2C7y%2C78%7C1ri%2Cre%2Cq1%2C5x*194e%2C_%22%7D%2C%22favicon.ico%22%3A%2201kq%2C5o%2C5n%2C1*1%2C8c%22%2C%222021%2F%22%3A%226%2Cqi%2Cq6%2Ck8%2Ck4%2Ck4%2Ck4%2Ck4%2Ck4%2Ck4%2C9*19s8%2C8c%2Cqox%22%2C%22wp-content%2Fthemes%2Fwpc2%2F%22%3A%7B%22style.css%22%3A%222re%2C16%2C12%2Cy*1182%2C_%2C29p*44%22%2C%22wpclogo.png%22%3A%22*050%2C50%2Ci%2Cbj%2Clv%2Clv%7C1re%2Cgd%2Cd6%2C4w*1ujn%2C_%22%7D%2C%22js%2Fbasicrum%2Fboomerang-1.737.60.cutting-edge.min.js%22%3A%2221j1%2C6a%2C65%2C6*1plb%2C8c%2C1ma1*25*42%22%7D%7D&u=https://example1.com&v=1.737.60&sv=14&sm=p&rt.si=f7504dae-b2e1-464b-a28e-1311ab4de139-reejl6&rt.ss=1656779946253&rt.sl=1&vis.st=visible&ua.plt=Linux%20x86_64&ua.vnd=Google%20Inc.&pid=ifvwnpyy&n=1&c.l=22hq%2Cxb3%2Cyka%7C52wz%2Ca0%7C22x0%2Cxaw%2Cyh4&c.t.fps=07*d*615*y*62&c.t.mouse=0*9*0.n4.00.9m..6k.&c.t.mousepct=2*a*004000001710&c.t.orn=0*f*01&c.tti.vr=1990&c.tti=2128&c.f=59&c.f.d=5027&c.f.m=1&c.f.l=1&c.f.s=l54405vm&c.m.p=67&c.m.n=1414&dom.res=36&dom.doms=1&mem.total=16164031&mem.limit=2172649472&mem.used=11839111&mem.lsln=0&mem.ssln=0&mem.lssz=2&mem.sssz=2&scr.xy=414x896&scr.bpp=24%2F24&scr.orn=0%2Fportrait-primary&scr.dpx=2&cpu.cnc=8&scr.mtp=1&dom.ln=515&dom.sz=45903&dom.ck=158&dom.img=35&dom.img.uniq=29&dom.script=7&dom.script.ext=3&dom.iframe=0&dom.link=9&dom.link.css=2&sb=1' \
		--compressed