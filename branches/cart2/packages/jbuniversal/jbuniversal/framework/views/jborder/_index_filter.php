<ul class="filter order-filter">
    <li class="filter-right">
        <input type="text" name="filter[search]" value="<?php echo $filter->get('search'); ?>" class="rounded"
               style="width:280px;" placeholder="<?php echo JText::_('JBZOO_ADMIN_ORDER_SEARCH_FIELD'); ?>">
        <button onclick="this.form.submit();"><?php echo Jtext::_('JBZOO_ADMIN_SEARCH'); ?></button>
    </li>

    <?php if ($this->app->joomla->version->isCompatible('3.0')) : ?>
        <li class="filter-right">
            <?php echo str_replace(array('input-mini', 'size="1"'), '', $this->pagination->getLimitBox()); ?>
        </li>
    <?php endif ?>

    <li class="filter-right">
        <?php echo $this->app->jbhtml->select($this->statusList, 'filter[status]', 'class="inputbox auto-submit"', $filter->get('status')); ?>
    </li>

    <li class="filter-right">
        <?php echo $this->app->jbhtml->select($this->userList, 'filter[created_by]', 'class="inputbox auto-submit"', $filter->get('created_by')); ?>
    </li>

    <li class="filter-right filter-total">
        <input type="text" value="<?php echo $filter->get('total_from'); ?>" name="filter[total_from]" class="rounded"
               placeholder="<?php echo Jtext::_('JBZOO_ADMIN_ORDER_TOTAL_FROM'); ?>">
        <input type="text" value="<?php echo $filter->get('total_to'); ?>" name="filter[total_to]" class="rounded"
               placeholder="<?php echo Jtext::_('JBZOO_ADMIN_ORDER_TOTAL_TO'); ?>">
    </li>

    <li class="filter-right">
        <span class="zoo-calendar">
            <?php echo $this->app->html->_('zoo.calendar', $filter->get('created_from'), 'filter[created_from]',
                uniqid('calendar-'), 'placeholder="' . JText::_('JBZOO_ADMIN_ORDER_CREATED_FROM') . '"', true); ?>
        </span>

        <span class="zoo-calendar">
            <?php echo $this->app->html->_('zoo.calendar', $filter->get('created_to'), 'filter[created_to]',
                uniqid('calendar-'), 'placeholder="' . JText::_('JBZOO_ADMIN_ORDER_CREATED_TO') . '"', true); ?>
        </span>
    </li>
</ul>