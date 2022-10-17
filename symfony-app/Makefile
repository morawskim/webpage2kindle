
.PHONY: worker-new-job
worker-new-job:
	php -dapc.enable_cli=1 /app/bin/console rabbitmq:consume -m1 new_job

.PHONY: worker-fetch-page-content
worker-fetch-page-content:
	php -dapc.enable_cli=1 /app/bin/console rabbitmq:consume -m1 fetch_page_content
