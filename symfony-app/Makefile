.PHONY: phpstan
phpstan:
	docker run --rm -v $(PWD):/project -w /project jakzal/phpqa:alpine phpstan analyse --no-interaction

.PHONY: behat
behat:
	./vendor/bin/behat

.PHONY: consumer
consumer:
	./bin/console rabbitmq:multiple-consumer -m1 pipeline
