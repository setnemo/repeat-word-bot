SHELL ?= /bin/bash
ARGS = $(filter-out $@,$(MAKECMDGOALS))

.SILENT: ;
.ONESHELL: ;
.NOTPARALLEL: ;
.EXPORT_ALL_VARIABLES: ;
Makefile: ;

.PHONY: build
build:
	docker-compose build ${ARGS}

.PHONY: up
up: network
	docker-compose up -d

.PHONY: install
install: up
	docker-compose exec worker composer install

.PHONY: logs
logs:
	tail -f ./logs/app/*.log

.PHONY: test
test: up
	docker-compose exec worker vendor/bin/phpunit tests

.PHONY: bash
bash: up
	docker-compose exec worker sh

.PHONY: bot-stop
bot-stop: up
	docker-compose exec worker supervisorctl stop all

.PHONY: bot-start
bot-start: up
	docker-compose exec worker supervisorctl start all

.PHONY: bot-sup
bot-sup: up
	docker-compose exec worker supervisorctl ${ARGS}

.PHONY: status
status: up
	docker-compose exec worker supervisorctl status

.PHONY: stop
stop:
	docker-compose stop ${ARGS}

.PHONY: psr
psr:
	 php vendor/bin/phpcbf --standard=psr12 app -n tests -n

.PHONY: psalm
psalm:
	 php vendor/bin/psalm

.PHONY: network
network:
	docker network create telegram-bots-network 2> /dev/null | true

.PHONY: help
help: .title
	echo ''
	echo 'Available targets:'
	echo '  help:             Show this help and exit'
	echo '  up:               Create and start application in detached mode (in the background)'
	echo '  psr:              Fix all files according PSR-12 Code Style'
	echo '  stop:             Stop container {name}'
	echo '  install:          Install dependency from composer.lock'
	echo '  network:          Create external docker network'
	echo '  build:            Build or rebuild services'
	echo '  bot-stop:         Stop worker supervisorctl'
	echo '  bot-start:        Run all worker jobs'
	echo '  bot-sup:          Run worker supervisorctl with {args}'
	echo '  bot-status:       Show worker supervisorctl status'
	echo '  worker:           Run worker bash'
	echo ''

%:
	@: