<?php
use Joomla\String\StringHelper;
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementNotificationSendemail
 */
class JBCartElementNotificationSendemail extends JBCartElementNotification
{

    const FIELD_USERMAIL = 'usermail';
    const FIELD_SITEMAIL = 'sitemail';

    /**
     * @var JMail
     */
    protected $_mailer;

    /**
     * @var EmailRenderer
     */
    protected $_renderer;

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_mailer   = JFactory::getMailer();
        $this->_renderer = $this->app->jbrenderer->create('email');
    }

    /**
     * Launch notification
     * @return string
     */
    public function notify()
    {
        if ($recipients = $this->_getRecipients()) {

            $body = StringHelper::trim($this->renderBody());
            if (empty($body)) {
                return null;
            }

            $sender = $this->_getMailSender();

            $this->_mailer->setSubject($this->_getMailSubject());
            $this->_mailer->setFrom($sender[0], $sender[1]);
            $this->_mailer->isHtml($this->_isHtml());
            $this->_mailer->setBody($body);

            foreach ($recipients as $recEmail => $recName) {

                // send message
                $this->_mailer->addRecipient($recEmail, $recName);
                $this->_mailer->send();
                $this->_mailer->ClearAllRecipients();

                if ($this->_isSleep()) { // simple antispam
                    sleep(1);
                }
            }
        }
    }

    /**
     * Get data from all elements
     * @return string
     */
    public function renderBody()
    {
        $emailBody = $this->_renderer->render(
            $this->config->get('layout_email', 'default'),
            array(
                'order'  => $this->getOrder(),
                'mailer' => $this->_mailer
            )
        );

        if (!$this->_isHtml()) {
            $emailBody = StringHelper::trim($emailBody);

            // clean up text (experimental)
            //$emailBody = strip_tags($emailBody, '<br><br/>');
            //$emailBody = preg_replace('#<br[/\s]*>#ius', PHP_EOL, $emailBody);
            //$emailBody = str_replace(array("\n", "\r", "\r\n", "\n\r", PHP_EOL), PHP_EOL, $emailBody);
        }

        return $emailBody;
    }

    /**
     * @return int
     */
    protected function _isHtml()
    {
        return (int)$this->config->get('ishtml', 1);
    }

    /**
     * @return int
     */
    protected function _isSleep()
    {
        return (int)$this->config->get('issleep', 0);
    }

    /**
     * Get mail subject from config
     * @return string
     */
    protected function _getMailSubject()
    {
        $subject = $this->config->get('subject');
        $subject = $this->_macros->renderText($subject, $this->getOrder());
        $subject = StringHelper::trim($subject);

        if (empty($subject)) {
            $subject = $this->getName();
        }

        return $subject;
    }

    /**
     * @return array
     */
    protected function _getRecipients()
    {
        $recipients = $this->app->data->create($this->config->get('recipients'));

        $tmpResult = array();

        // get by groups
        if ($groups = $recipients->get('groups')) {
            foreach ($groups as $groupId) {
                if ($users = JAccess::getUsersByGroup($groupId)) {
                    foreach ($users as $userId) {
                        $juser       = JFactory::getUser($userId);
                        $tmpResult[] = array($juser->email, $juser->name);
                    }
                }
            }
        }

        // get by orderform
        $orderform = (array)$recipients->get('orderform', array());
        foreach ($orderform as $field) {

            if ($field == self::FIELD_SITEMAIL) {
                $config      = JFactory::getConfig();
                $tmpResult[] = array($config->get('mailfrom'), $config->get('sitename'));

            } else if ($field == self::FIELD_USERMAIL) {
                $juser       = JFactory::getUser();
                $tmpResult[] = array($juser->email, $juser->name);

            } else if ($element = $this->getOrder()->getFieldElement($field)) {
                $value       = $element->data()->get('value');
                $tmpResult[] = array($value, $value);
            }

        }

        // get custom fields
        $custom = explode(',', $recipients->get('custom'));
        foreach ($custom as $email) {
            $tmpResult[] = array($email, $email);
        }

        // check and clear all recipients
        $result = array();
        foreach ($tmpResult as $recipient) {

            list($email, $name) = $recipient;

            if ($email = $this->app->jbvars->email($email)) {
                if (!isset($result[$email])) {
                    $result[$email] = StringHelper::trim($name);
                }
            }

        }

        return $result;
    }

    /**
     * Get email sender
     * @return array
     */
    protected function _getMailSender()
    {
        $jconfig = JFactory::getConfig();
        $jbvars  = $this->app->jbvars;

        $joomlaSite = $jconfig->get('sitename');
        $joomlaMail = $jbvars->email($jconfig->get('mailfrom'));

        $fromEmail = $jbvars->email($this->config->get('fromemail', $joomlaMail));
        $fromEmail = (!empty($fromEmail)) ? $fromEmail : $joomlaMail;

        $fromName = StringHelper::trim($this->config->get('fromname', $joomlaSite));
        $fromName = (!empty($fromName)) ? $fromName : StringHelper::trim($joomlaSite);

        return array($fromEmail, $fromName);
    }

}
