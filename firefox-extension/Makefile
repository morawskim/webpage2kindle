.PHONY: build chromium
build:
	test -n "$(SYMFONY_ENDPOINT_URL)"  # $$SYMFONY_ENDPOINT_URL eg https://example.com/web-extension
	npm run build
chromium:
	 npx web-ext run --source-dir ./dist/ --target chromium --arg='--proxy-bypass-list=<-loopback>' --arg='--proxy-server=localhost:8080'
