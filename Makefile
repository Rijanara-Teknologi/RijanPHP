.PHONY: test test-unit test-feature coverage

test:
	vendor/bin/phpunit

test-unit:
	vendor/bin/phpunit --testsuite Unit

test-feature:
	vendor/bin/phpunit --testsuite Feature

coverage:
	vendor/bin/phpunit --coverage-html tests/coverage

watch:
	find tests -name "*.php" -o -name "*.xml" | entr -c vendor/bin/phpunit

install:
	composer install --dev
