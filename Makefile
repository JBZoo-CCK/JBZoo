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

#### Project Update & Building (complex commands) ######################################################################
update:
	@echo "$(C_AR)>>> >>> >>> >>> $(C_T) Update project (DEV) $(CE)"
	@make off
	@make clean-cache
	@make clean-build
	@make update-composer
	@make update-bower
	@make on
