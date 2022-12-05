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
