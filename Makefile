
.PHONEY: test test-spec test-api coverage-clover coverage-html coverage-text clean-coverage docs clean

test: clean-coverage test-spec test-api

test-spec:
	phpdbg -qrr ./vendor/bin/phpspec run

test-api:
	env REPORT_COVERAGE=true dredd

coverage-clover:
	./vendor/bin/phpcov merge --clover=clover.xml coverage/

coverage-html:
	./vendor/bin/phpcov merge --html=coverage/html coverage/

coverage-text:
	./vendor/bin/phpcov merge --text coverage/

clean-coverage:
	rm -rf coverage/* clover.xml

docs: docs/Keeper\ API.html

docs/Keeper\ API.html: docs/Keeper\ API.apib
	docker run -ti --rm -v $(PWD)/docs:/docs humangeo/aglio --theme-template triple -i Keeper\ API.apib -o Keeper\ API.html

clean: clean-coverage
	rm -rf docs/Keeper\ API.html
