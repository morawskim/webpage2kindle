MAKEFILE_DIR := $(dir $(abspath $(firstword $(MAKEFILE_LIST))))

.PHONY: build-images
build-images:
	rm -rf caddy/public
	cp -r symfony-app/public caddy/public
	docker build -t morawskim/webpage2kindle symfony-app
	docker build -t morawskim/webpage2kindle-node node-readability
	docker build -t morawskim/webpage2kindle-caddy caddy

.PHONY: push-images
push-images:
	docker push morawskim/webpage2kindle
	docker push morawskim/webpage2kindle-node
	docker push morawskim/webpage2kindle-caddy

.PHONY: bump-firefox-extension-version
bump-firefox-extension-version:
	@test -n "$(JUNIE_OPENROUTER_API_KEY)" || { \
    		echo "ERROR: JUNIE_OPENROUTER_API_KEY is not set"; \
    		exit 1; \
    	}
	@docker run --rm -it -e JUNIE_OPENROUTER_API_KEY=$(JUNIE_OPENROUTER_API_KEY) -v$(MAKEFILE_DIR):/app -w/app registry.jetbrains.team/p/matterhorn/public/junie-gitlab-wrapper:latest junie --model gpt-codex "Bump the Firefox extension version"
