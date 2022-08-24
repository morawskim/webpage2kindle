.PHONY: build-images
build-images:
	docker build -t morawskim/webpage2kindle symfony-app
	docker build -t morawskim/webpage2kindle-node node-readability

.PHONY: push-images
push-images:
	docker push morawskim/webpage2kindle
	docker push morawskim/webpage2kindle-node
