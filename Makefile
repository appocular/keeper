
.PHONEY: test
test: clean-coverage test-spec test-api

.PHONEY: test-spec
test-spec:
	phpdbg -qrr ./vendor/bin/phpspec run

.PHONEY: test-unit
test-unit:
	./vendor/bin/phpunit --coverage-php=coverage/unit.cov

.PHONEY: test-api
test-api:
	env REPORT_COVERAGE=true dredd

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
