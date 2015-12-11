<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$testUrl = $this->app->jbrouter->admin(array('task' => 'performanceStep'));
$reportUrl = $this->app->jbrouter->admin(array('task' => 'performanceReport'));
$jbperform = $this->app->jbperform;

?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">
        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_INFO_PERFORMANCE'); ?></h2>

        <p><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_DESC'); ?></p>

        <p><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_WEBSITE_1'); ?></p>

        <p><em><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_WEBSITE_2'); ?></em></p>

        <a class="uk-button uk-button-primary jsStart"
           href="<?php echo $testUrl; ?>"><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_START'); ?></a>

        <a class="uk-button uk-button-success jsReport"
           href="<?php echo $reportUrl; ?>"><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_SHARE'); ?></a>

        <p>&nbsp;</p>

        <table class="uk-table uk-table-hover uk-table-striped table-performance">
            <thead>
            <tr>
                <th class="uk-width-6-10 uk-text-bold uk-text-center"><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_TESTNAME'); ?></th>
                <th class="uk-width-2-10 uk-text-bold uk-text-center"><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_CURRENT'); ?></th>
                <th class="uk-width-2-10 uk-text-bold uk-text-center"><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_STANDARD'); ?></th>
            </tr>
            </thead>

            <tbody>

            <?php foreach ($this->testList as $group => $tests) : ?>
                <tr>
                    <td colspan="4">
                        <div class="col-group"><?php echo JText::_('JBZOO_BENCHMARK_' . $group); ?></div>
                        <?php echo JText::_('JBZOO_BENCHMARK_' . $group . '_DESC'); ?>
                    </td>
                </tr>
                <?php foreach ($tests as $test) :

                    $isAlert = '';
                    if ($test['value'] != '-') {
                        $isAlert = $jbperform->isAlert($test['value'], $test['key']);
                        if ($isAlert !== -1) {
                            $isAlert = ($isAlert) ? 'uk-error' : 'uk-success';
                        }
                    }

                    ?>
                    <tr class="jstest jstest-<?php echo $test['key']; ?> <?php echo $isAlert; ?>"
                        data-testname="<?php echo $test['key']; ?>">
                        <td>
                            <strong class="testname"><?php echo JText::_('JBZOO_BENCHMARK_' . $test['key']); ?></strong><br>
                            <?php echo JText::_('JBZOO_BENCHMARK_' . $test['key'] . '_DESC'); ?>
                        </td>

                        <td class="uk-text-center jsValue">
                            <?php echo $jbperform->toFormat($test['value'], $test['key']); ?>
                        </td>

                        <td class="uk-text-center jsStandard">
                            <?php echo $jbperform->toFormat($test['standard'], $test['key']); ?>

                            <?php if ($test['postfix']) : ?>
                                <?php echo JText::_('JBZOO_BENCHMARK_POSTFIX_' . $test['postfix']); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>

            </tbody>
        </table>

        <h3><em><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_TITLE'); ?></em></h3>

        <ul>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_1'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_2'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_3'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_4'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_5'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_6'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_7'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_8'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_9'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_INFO_PERFORMANCE_10'); ?></li>
        </ul>

        <?php echo $this->partial('footer'); ?>

    </div>
</div>

<script type="text/javascript">
    jQuery(function ($) {

        var requestUrl = "<?php echo $testUrl;?>";

        var img = '<img src="<?php echo JUri::root();?>media/zoo/applications/jbuniversal/assets/img/misc/loader.gif" />';

        var execTest = function (i) {

            var date = new Date(),
            //$obj = $('.jstest[data-testname=total_score]:eq(' + i + ')'),
                $obj = $('.jstest:eq(' + i + ')'),
                testName = $obj.data('testname'),
                $value = $('.jsValue', $obj),
                $standard = $('.jsStandard', $obj);

            $value.html(img);

            $.ajax({
                'dataType': 'json',
                'type'    : 'POST',
                'cache'   : false,
                'headers' : {
                    "cache-control": "no-cache"
                },
                'url'     : requestUrl,
                'data'    : {
                    'nocache' : date.getMilliseconds(),
                    'testname': testName
                },
                'success' : function (data) {

                    if (typeof (data.value) != "undefined") {
                        $value.text(data.value);
                        $standard.text(data.standard);

                        if (data.alert == -1) {
                            $obj.removeClass('uk-error uk-success');
                        } else {
                            $obj.toggleClass('uk-error', data.alert);
                            $obj.toggleClass('uk-success', !data.alert);
                        }
                    } else {
                        $value.text('FAIL!');
                        alert(JBZoo.stripTags(data));
                        $obj.toggleClass('uk-error', true);
                    }

                    i++;
                    var $nextTest = $('.jstest:eq(' + i + ')');
                    if ($nextTest.length != 0) {
                        // no hardcore!
                        setTimeout(function () {
                            execTest(i);
                        }, 500);
                    }
                },
                'error'   : function (data) {
                    alert(JBZoo.stripTags(data.responseText));
                    $value.text('FATAL ERROR!');
                }
            });
        }

        $('.jsStart').click(function () {
            $('.jstest').removeClass('error success');
            execTest(0);
            return false;
        });

        $('.jsReport').click(function () {

        });

    });
</script>
