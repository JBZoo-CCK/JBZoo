#
# JBZoo Application
#
# This file is part of the JBZoo CCK package.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @package    Application
# @license    GPL-2.0
# @copyright  Copyright (C) JBZoo.com, All rights reserved.
# @link       https://github.com/JBZoo/JBZoo
#

.PHONY: build build-installer build-update clean reset test dev validate prod

dev:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Update project \033[0m"
	@composer update --optimize-autoloader --no-interaction --no-progress
	@echo ""

prod:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Prepare project for production version \033[0m"
	@composer update --optimize-autoloader --no-interaction --no-progress --no-dev
	@echo ""

validate:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Composer validate \033[0m"
	@composer validate --no-interaction
	@echo ""

test:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Run unit-tests \033[0m"
	@php ./vendor/phpunit/phpunit/phpunit
	@echo ""

build:
	@rm -rf ./build
	@make build-installer
	@make build-update
	@ls -ld ./build/*

build-installer:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Build installer \033[0m"
	@mkdir -p ./build/files
	@cp -R ./src/packages         ./build/files/packages
	@cp    ./src/file.script.php  ./build/files/file.script.php
	@cp    ./src/pkg_jbzoo.xml    ./build/files/pkg_jbzoo.xml
	@cp    ./README.md        ./build/files/README.md
	@cp    ./LICENSE.md       ./build/files/LICENSE.md
	@cd ./build/files; zip -r9q jbzoo_clean_install.zip *
	@mv ./build/files/jbzoo_clean_install.zip ./build/jbzoo_clean_install.zip
	@rm -rf ./build/files
	@echo "./build/files/jbzoo_clean_install.zip is ready"
	@echo ""

build-update:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Build update package \033[0m"
	@mkdir -p ./build/files
	@cp -R ./src/packages         ./build/files/packages
	@cp    ./src/file.script.php  ./build/files/file.script.php
	@cp    ./src/pkg_jbzoo.xml    ./build/files/pkg_jbzoo.xml
	@cp    ./README.md        ./build/files/README.md
	@cp    ./LICENSE.md       ./build/files/LICENSE.md
	@find ./build/files -name 'positions.config' -delete
	@find ./build/files -name 'positions.xml' -delete
	@find ./build/files -name 'metadata.xml' -delete
	@rm   -rf ./build/files/packages/jbuniversal/jbuniversal/templates
	@cd ./build/files; zip -r9q jbzoo_update.zip *
	@mv ./build/files/jbzoo_update.zip ./build/jbzoo_update.zip
	@echo "./build/files/jbzoo_update.zip is ready"
	@rm -rf ./build/files
	@echo ""

reset:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Hard reset \033[0m"
	@git reset --hard

clean:
	@echo "\033[0;33m>>> >>> >>> >>> >>> >>> >>> >>> \033[0;30;46m Cleanup project \033[0m"
	@rm -rf ./vendor/
	@rm -f ./composer.lock
