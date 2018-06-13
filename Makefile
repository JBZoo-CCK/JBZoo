#
# Item8 | Application
#
# This file is part of the Item8 Service Package.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @package      Application
# @license      Proprietary
# @copyright    Copyright (C) Item8, All rights reserved.
# @link         https://item8.io
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
