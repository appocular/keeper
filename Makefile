
.PHONEY: docs

test:
	./vendor/bin/phpunit
	phpdbg -qrr ./vendor/bin/phpspec run
	dredd

docs: docs.html

docs.html: docs/Keeper\ API.apib
	docker run -ti --rm -v $(PWD):/docs humangeo/aglio --theme-template triple -i docs/Keeper\ API.apib -o docs/index.html

