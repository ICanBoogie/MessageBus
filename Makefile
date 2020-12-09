# customization

PACKAGE_NAME = icanboogie/message-bus
PACKAGE_VERSION = 0.8
PHPCS_FILENAME = build/phpcs

# do not edit the following lines

usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer install

update:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer update

test: vendor
	@$(PHPUNIT)

test-coverage: vendor
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html build/coverage

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

clean:
	@rm -fR build
	@rm -fR vendor
	@rm -f composer.lock

.PHONY: all autoload doc clean test test-coverage test-coveralls test-dependencies update
