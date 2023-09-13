.PHONY: behat
behat:
	./vendor/bin/behat

.PHONY: consumer
consumer:
	./bin/console rabbitmq:multiple-consumer -m1 pipeline
