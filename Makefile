build:
	# You can use this CLI tool to check if image build successfully or not
	devcontainer build

attach:
	docker exec -ti phpfs bash

repl:
	# https://www.php.net/manual/en/features.commandline.interactive.php
	# to exit type 'exit'
	docker exec -ti phpfs php -a -d xdebug.mode=off

pwd:
	# make pwd pwd=admin
	docker exec -ti phpfs php -r "print(password_hash('$(pwd)',  null));"