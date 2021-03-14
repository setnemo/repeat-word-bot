SHELL ?= /bin/bash
ARGS = $(filter-out $@,$(MAKECMDGOALS))

# Regular Colors
BLACK=\033[0;30m        # Black
RED=\033[0;31m          # Red
GREEN=\033[0;32m        # Green
YELLOW=\033[0;33m       # Yellow
BLUE=\033[0;34m         # Blue
PURPLE=\033[0;35m       # Purple
CYAN=\033[0;36m         # Cyan
WHITE=\033[0;37m        # White

# Bold
BBLACK=\033[1;30m       # Black
BRED=\033[1;31m         # Red
BGREEN=\033[1;32m       # Green
BYELLOW=\033[1;33m      # Yellow
BBLUE=\033[1;34m        # Blue
BPURPLE=\033[1;35m      # Purple
BCYAN=\033[1;36m        # Cyan
BWHITE=\033[1;37m       # White

# Underline
UBLACK=\033[4;30m       # Black
URED=\033[4;31m         # Red
UGREEN=\033[4;32m       # Green
UYELLOW=\033[4;33m      # Yellow
UBLUE=\033[4;34m        # Blue
UPURPLE=\033[4;35m      # Purple
UCYAN=\033[4;36m        # Cyan
UWHITE=\033[4;37m       # White

.SILENT: ;
.ONESHELL: ;
.NOTPARALLEL: ;
.EXPORT_ALL_VARIABLES: ;
Makefile: ;

.DEFAULT_GOAL := help

.PHONY: build
build:
	docker-compose build ${ARGS}

.PHONY: up
up:
	docker-compose up -d --remove-orphans

.PHONY: stop
stop:
	docker-compose stop ${ARGS}

.PHONY: install
install:
	docker-compose exec repeat composer install

.PHONY: logs
logs:
	tail -f ./logs/app/*.log

.PHONY: bash
bash:
	docker-compose exec repeat sh

.PHONY: bot-stop
bot-stop:
	docker-compose exec worker supervisorctl stop all

.PHONY: bot-start
bot-start:
	docker-compose exec worker supervisorctl start all

.PHONY: bot-sup
bot-sup:
	docker-compose exec worker supervisorctl ${ARGS}

.PHONY: status
status:
	docker-compose exec worker supervisorctl status

.PHONY: psr
psr:
	php vendor/bin/phpcbf --standard=psr12 app -n tests -n

.PHONY: psalm
psalm:
	docker-compose exec repeat php vendor/bin/psalm

.PHONY: orm-clear
orm-clear:
	docker-compose exec repeat php vendor/bin/doctrine orm:clear-cache:metadata
	docker-compose exec repeat php vendor/bin/doctrine orm:clear-cache:query
	docker-compose exec repeat php vendor/bin/doctrine orm:clear-cache:result

.PHONY: orm-validate
orm-validate:
	docker-compose exec repeat php vendor/bin/doctrine orm:validate-schema

.PHONY: orm-update
orm-update:
	docker-compose exec repeat vendor/bin/doctrine orm:schema-tool:update --dump-sql

.PHONY: orm-proxies
orm-proxies:
	docker-compose exec worker php vendor/bin/doctrine orm:generate-proxies

.PHONY: test-up
test-up:
	docker-compose -f docker-compose.github.actions.yml up -d

.PHONY: test-build-up
test-build-up:
	docker-compose -f docker-compose.github.actions.yml up -d --build --remove-orphans

.PHONY: test-stop
test-stop:
	docker-compose -f docker-compose.github.actions.yml stop

.PHONY: test-ps
test-ps:
	docker-compose -f docker-compose.github.actions.yml ps

.PHONY: test-all
test-all:
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:metadata
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:query
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:result
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/codecept build
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/codecept run

.PHONY: test-all-coverage
test-all-coverage:
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:metadata
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:query
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:result
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/codecept build
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/codecept run --coverage-xml --xml

.PHONY: test-run
test-run:
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:metadata
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:query
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/doctrine orm:clear-cache:result
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/codecept build
	docker-compose -f docker-compose.github.actions.yml exec repeatt vendor/bin/codecept run ${ARGS}

.PHONY: network
network:
	docker network create telegram-bots-network

.PHONY: default
default: help

.PHONY: help
help: .title
	printf '\n'
	printf "${BGREEN}Available targets:${NC}\n"
	printf '\n'
	printf "${BLUE}make help${NC}:          ${YELLOW}Show this help and exit${NC}\n"
	printf "${BLUE}make build${NC}:         ${YELLOW}Build or rebuild services${NC}\n"
	printf "${BLUE}make up${NC}:            ${YELLOW}Create and start application in detached mode (in the background)${NC}\n"
	printf "${BLUE}make stop${NC}:          ${YELLOW}Stop container {name}${NC}\n"
	printf "${BLUE}make install${NC}:       ${YELLOW}Install dependency from composer.lock${NC}\n"
	printf "${BLUE}make logs${NC}:          ${YELLOW}Show app logs${NC}\n"
	printf "${BLUE}make bash${NC}:          ${YELLOW}Run bash in container${NC}\n"
	printf "${BLUE}make bot-stop${NC}:      ${YELLOW}Stop worker supervisorctl${NC}\n"
	printf "${BLUE}make bot-start${NC}:     ${YELLOW}Run all worker jobs${NC}\n"
	printf "${BLUE}make bot-sup${NC}:       ${YELLOW}Run worker supervisorctl with {args}${NC}\n"
	printf "${BLUE}make status${NC}:        ${YELLOW}Show worker supervisorctl status${NC}\n"
	printf "${BLUE}make psr${NC}:           ${YELLOW}Fix all files according PSR-12 Code Style${NC}\n"
	printf "${BLUE}make psalm${NC}:         ${YELLOW}Run Psalm${NC}\n"
	printf "${BLUE}make orm-clear${NC}:     ${YELLOW}Clear ORM cache${NC}\n"
	printf "${BLUE}make orm-validate${NC}:  ${YELLOW}Validate ORM schema${NC}\n"
	printf "${BLUE}make orm-update${NC}:    ${YELLOW}Get migration SQL updates to console${NC}\n"
	printf "${BLUE}make orm-proxies${NC}:   ${YELLOW}Regenerate proxies entity${NC}\n"
	printf "${BLUE}make network${NC}:       ${YELLOW}Create external docker network${NC}\n"
	printf "${BLUE}make test-up${NC}:       ${YELLOW}Up containers for tests${NC}\n"
	printf "${BLUE}make test-build-up${NC}: ${YELLOW}Force up with rebuild containers for tests${NC}\n"
	printf "${BLUE}make test-stop${NC}:     ${YELLOW}Stop containers for tests${NC}\n"
	printf "${BLUE}make test-ps${NC}:       ${YELLOW}Show containers for tests${NC}\n"
	printf "${BLUE}make test-all${NC}:      ${YELLOW}Run all tests${NC}\n"
	printf "${BLUE}make test-run${NC}:      ${YELLOW}Run test file with path in args${NC}\n"
	printf '\n'

%:
	@: