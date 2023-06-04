# https://www.gnu.org/software/make/manual/make.html

PHP = php

##
## PROJECT
## -------

tests: ## start tests
	$(PHP) -dxdebug.mode=develop ./tests_run.php

tests-coverage: ## start tests
	$(PHP) -dxdebug.mode=coverage,develop ./tests_run.php

.PHONY: tests

#
# HELP
# ----

help:
	@cat $(MAKEFILE_LIST) | grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-24s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/' && printf "\n"

.PHONY: help

.DEFAULT_GOAL := help

-include Makefile.override.mk
