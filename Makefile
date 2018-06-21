###############################################################################
# ONYX Main Makefile
###############################################################################

HOST_SOURCE_PATH=$(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))

USER_ID=$(shell id -u)
GROUP_ID=$(shell id -g)
HOST_IP=$(shell ip addr show docker0 | grep -w inet | sed 's%.*inet \([^/]*\).*%\1%')
XDEBUG_CONFIG?=$(shell ${XDEBUG_CONFIG:-""})

export USER_ID
export GROUP_ID

#------------------------------------------------------------------------------

-include vendor/onyx/core/wizards.mk
include makefiles/composer.mk
include makefiles/karma.mk
include makefiles/phpunit.mk
include makefiles/qa.mk
include makefiles/whalephant.mk

#------------------------------------------------------------------------------

.DEFAULT_GOAL := help

# Spread cli arguments
ifneq (,$(filter $(firstword $(MAKECMDGOALS)),switch))
    CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    $(eval $(CLI_ARGS):;@:)
endif

init: config install-dependencies gitignore


install-dependencies: composer-install

gitignore:
	sed '/^composer.lock$$/d' -i .gitignore

help:
	@echo "========================================"
	@echo "Google Auth"
	@echo "========================================"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo "========================================"

#------------------------------------------------------------------------------

clean: clean-composer clean-console clean-karma clean-phpunit clean-qa clean-webpack clean-whalephant
	-rm -rf var

#------------------------------------------------------------------------------

.PHONY: init check-env install-dependencies gitignore help clean
