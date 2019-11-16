
.PHONEY: phpcs
phpcs:
	./vendor/bin/phpcs

.PHONEY: test
test: clean-coverage test-spec test-unit phpcs test-api

.PHONEY: test-spec
test-spec:
	phpdbg -qrr ./vendor/bin/phpspec run

.PHONEY: test-unit
test-unit:
	phpdbg -qrr ./vendor/bin/phpunit --coverage-php=coverage/unit.cov

.PHONEY: test-api
test-api:
	env SHARED_TOKEN=MySharedToken REPORT_COVERAGE=true dredd

.PHONEY: coverage-clover
coverage-clover:
	./vendor/bin/phpcov merge --clover=clover.xml coverage/

.PHONEY: coverage-html
coverage-html:
	./vendor/bin/phpcov merge --html=coverage/html coverage/

.PHONEY: coverage-text
coverage-text:
	./vendor/bin/phpcov merge --text coverage/

.PHONEY: clean-coverage
clean-coverage:
	rm -rf coverage/* clover.xml

.PHONEY: docs
docs: docs/Keeper\ API.html

docs/Keeper\ API.html: docs/Keeper\ API.apib
	docker run -ti --rm -v $(PWD)/docs:/docs humangeo/aglio --theme-template triple -i Keeper\ API.apib -o Keeper\ API.html

.PHONEY: clean
clean: clean-coverage
	rm -rf docs/Keeper\ API.html

watch-test:
	while true; do \
	  find . \( -name .git -o -name vendor \) -prune -o -name '#*' -o -name '*.php' -a -print | entr -cd make test-spec test-unit test-api; \
	done
