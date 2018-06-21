#------------------------------------------------------------------------------
# Composer
#------------------------------------------------------------------------------

COMPOSER_IMAGE_NAME=composer

composer = docker run -t -i --rm \
                -v ${HOST_SOURCE_PATH}:/var/www/app \
                -v ~/.cache/composer:/tmp/composer \
                -v $(SSH_AUTH_SOCK):/ssh-agent \
                -e SSH_AUTH_SOCK=/ssh-agent \
                -e COMPOSER_CACHE_DIR=/tmp/composer \
                -e COMPOSER=/var/www/app/composer.json \
                -w /var/www/app \
                -u composer \
                ${COMPOSER_IMAGE_NAME} $1 $2

# Spread cli arguments
ifneq (,$(filter $(firstword $(MAKECMDGOALS)),composer))
    CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(CLI_ARGS):;@:)
endif

# Add ignore platform reqs for composer install & update
COMPOSER_ARGS=
ifeq (composer, $(firstword $(MAKECMDGOALS)))
    ifneq (,$(filter install update,$(CLI_ARGS)))
        COMPOSER_ARGS=--ignore-platform-reqs
    endif
endif

#------------------------------------------------------------------------------

build-composer:
	docker build --build-arg USER_ID=$(USER_ID) -t ${COMPOSER_IMAGE_NAME} ./docker/images/composer

composer-init:
	mkdir -p ~/.cache/composer

composer: composer-init check-env ## Run composer
	$(call composer, $(CLI_ARGS), $(COMPOSER_ARGS))

composer-install: composer-init check-env
	$(call composer, install, --ignore-platform-reqs)

composer-update: composer-init check-env
	$(call composer, update, --ignore-platform-reqs)

composer-dumpautoload: composer-init check-env
	$(call composer, dumpautoload)

#------------------------------------------------------------------------------

clean-composer:
	-rm -rf vendor

#------------------------------------------------------------------------------

.PHONY: composer-init composer composer-install composer-update composer-dumpautoload clean-composer build-composer
