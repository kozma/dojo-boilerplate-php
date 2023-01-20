SHELL=/bin/bash
.PHONY: help build ssh test

help: ## Show this help
	@echo "Targets:"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/\(.*\):.*##[ \t]*/    \1 ## /' | sort | column -t -s '##'

build: ## Build the image
	docker-compose build && \
	docker-compose run docker-php sh -c "composer install"

ssh: ## Start a shell in the container
	docker-compose run docker-php sh

up: ## Start a container that keeps running so PHPStorm can connect to it to quickly run tests
	docker-compose run -d docker-php sh -c "while true; do sleep 1; done"

down: ## Stop container
	docker-compose down

test: ## Run tests
	docker-compose run docker-php sh -c "./vendor/bin/phpunit --colors=always"