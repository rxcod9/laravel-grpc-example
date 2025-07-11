generate:
	cd publisher-app \
	&& docker run --rm -v "$$PWD:/workspace" -w /workspace namely/protoc-all \
  		-f protos/messages.proto -l php -o app/Grpc

	cd consumer-app \
	&& docker run --rm -v "$$PWD:/workspace" -w /workspace namely/protoc-all \
		-f protos/messages.proto -l php -o app/Grpc

