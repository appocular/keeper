
.PHONEY: docs

test: test-spec test-api

test-spec:
	phpdbg -qrr ./vendor/bin/phpspec run

test-api:
	dredd

docs: docs/Keeper\ API.html

docs/Keeper\ API.html: docs/Keeper\ API.apib
	docker run -ti --rm -v $(PWD)/docs:/docs humangeo/aglio --theme-template triple -i Keeper\ API.apib -o Keeper\ API.html

clean:
	rm -rf docs/Keeper\ API.html
