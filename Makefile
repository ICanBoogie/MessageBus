# customization

PACKAGE_NAME = icanboogie/message-bus
PHPUNIT = vendor/bin/phpunit
PHPCS_FILENAME = build/phpcs

# do not edit the following lines

.PHONY: usage
usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer install

.PHONY: update
update:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer update

test-dependencies: vendor

.PHONY: test
test: test-dependencies
	@$(PHPUNIT)

.PHONY: test-coverage
test-coverage: vendor
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html build/coverage

.PHONY: test-coveralls
test-coveralls: test-dependencies
	@mkdir -p build/logs
	@$(PHPUNIT) --coverage-clover build/logs/clover.xml

.PHONY: test-container
test-container:
	@-docker-compose -f ./docker-compose.yml run --rm app bash
	@docker-compose -f ./docker-compose.yml down -v

$(PHPCS_FILENAME):
	curl -L -o $@ https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
	chmod +x $@

lint: vendor $(PHPCS_FILENAME)
	$(PHPCS_FILENAME)
	vendor/bin/phpstan analyse

doc: vendor
	@mkdir -p build/docs
	@apigen generate \
	--source lib \
	--destination build/docs/ \
	--title "$(PACKAGE_NAME) v$(PACKAGE_VERSION)" \
	--template-theme "bootstrap"

.PHONY: clean
clean:
	@rm -fR build
	@rm -fR vendor
	@rm -f composer.lock
