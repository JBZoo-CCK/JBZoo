<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementNotificationSendEmail
 */
class JBCartElementNotificationSendEmail extends JBCartElementNotification
{
    /**
     * @var JMail
     */
    protected $_mailer;

    const RECIPIENT_USER_PROFILE = 'user';
    const RECIPIENT_USER_ORDER = 'order';

    /**
     * Class constructor
     *
     * @param App $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_mailer  = JMail::getInstance();
        $this->renderer = $this->app->jbrenderer->create('email');
    }

    /**
     * Launch notification
     * @return string
     */
    public function notify()
    {
        $html   = $this->getHTML();
        $attach = $this->renderer->getAttach();

        $this
            ->setHead()
            ->setBody($html)
            ->isHtml(true)
            ->setSender()
            ->addAttachment($attach)
            ->addImageItems();

        $this->send();

        return $html;
    }

    /**
     * Send notification message to each recipient
     */
    public function send()
    {
        $this
            /** Send message to administrators */
            ->sendToAdmins()
            /** Send message to order owner - user */
            ->sendToUser()
            /** Send message to advance email's - user */
            ->sendToAdvance();
    }

    /**
     * Get data from all elements
     *
     * @return string
     */
    public function getHTML()
    {
        $layout = $this->config->get('layout_email', 'order');
        $html   = '';

        $html .= $this->renderer->render($layout, array(
            'subject' => $this->getSubject()
        ));

        $html = $this->replace($html);

        return $html;
    }

    /**
     * Sets message type to HTML
     *
     * @param  bool $isHtml
     *
     * @return $this
     */
    public function isHtml($isHtml = true)
    {
        $this->_mailer->isHtml($isHtml);

        return $this;
    }

    /**
     * Set email subject
     *
     * @param  null $subject
     *
     * @return $this
     */
    public function setHead($subject = null)
    {
        $subject = !empty($subject) ? $subject : $this->config->get('subject');
        $this->_mailer->setSubject($subject);

        return $this;
    }

    /**
     * Add file attachments to the email
     *
     * @param   mixed $data string|array of
     *
     * @return $this
     */
    public function addAttachment($data)
    {
        if (!empty($data) && $this->getOrder()->id) {
            $this->_mailer->addAttachment($data['files'], $data['names']);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addImageItems()
    {
        $order = $this->getOrder();

        if ($order->id && $items = $order->getItems(false)) {

            foreach ($items as $key => $params) {

                if ($path = $params->get('image')) {

                    $path = JPATH_ROOT . DS . $path;

                    $file = $this->clean(basename($path));
                    $name = $this->clean($params->get('name'));

                    $cid = $this->clean($key) . '-' . $file;
                    $cid = JString::str_ireplace(' ', '', $cid);

                    $this->addImage($path, $cid, $name);
                }
            }
        }

        return $this;
    }

    /**
     * Add image width CID to attach. Use for Order Items.
     *
     * @param string $path Path to image
     * @param string $cid Unique
     * @param string $name Name of image
     *
     * @return $this
     */
    public function addImage($path, $cid, $name = '')
    {
        $this->_mailer->AddEmbeddedImage($path, $cid, $name);

        return $this;
    }

    /**
     * Set the email body
     *
     * @param  string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->_mailer->setBody($body);

        return $this;
    }

    /**
     * Set email sender
     * @return $this
     */
    public function setSender()
    {
        $from     = $this->config->get('from');
        $fromName = $this->config->get('fromname');

        $this->_mailer->setSender(array($from, $fromName));

        return $this;
    }

    /**
     * Add recipients to the email
     *
     * @param  string|array $recipient - email address
     * @param  string|array $name - name of recipient
     *
     * @return $this
     */
    public function addRecipient($recipient, $name = '')
    {
        $this->_mailer->addRecipient($recipient, $name);

        return $this;
    }

    /**
     * Send notification email to administrators
     *
     * @return $this
     */
    public function sendToAdmins()
    {
        $to = $this->app->data->create($this->config->get('recipient'));

        if ($adminRecipients = $to->get('admin', array())) {

            foreach ($adminRecipients as $id) {

                $users = JAccess::getUsersByGroup($id);
                if (!empty($users)) {

                    foreach ($users as $userId) {

                        $user = JFactory::getUser($userId);

                        $this->addRecipient($user->get('email'), $user->get('name'));
                        $this->_send();
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Send notification email to user
     *
     * @return $this
     */
    public function sendToUser()
    {
        $to = $this->app->data->create($this->config->get('recipient'));

        //send notification to user
        if ($userRecipients = $to->get('user', array())) {
            foreach ($userRecipients as $type) {

                //send to email from user profile
                if ($type == self::RECIPIENT_USER_PROFILE) {

                    $user = JFactory::getUser();
                    $this->addRecipient($user->get('email'), $user->get('name'));

                    $this->_send();

                    //send to email from order field email
                } else if ($type == self::RECIPIENT_USER_ORDER) {

                }

            }
        }

        return $this;
    }

    /**
     * Send notification email to advance email's
     *
     * @return $this
     */
    public function sendToAdvance()
    {
        $to = $this->app->data->create($this->config->get('recipient'));

        //advanced email's
        if ($advRecipients = $to->get('advanced', '')) {

            if (strpos($advRecipients, ',') === false) {
                $this->addRecipient($advRecipients);
                $this->_send();

            } else {
                $advRecipients = explode(',', $advRecipients);
                foreach ($advRecipients as $recipient) {
                    $recipient = JString::trim($recipient);
                    $this->addRecipient($recipient);

                    $this->_send();
                }
            }
        }

        return $this;
    }

    /**
     * Send notification and clear recipient
     *
     * return $this
     */
    protected function _send()
    {
        $this->_mailer->Send();
        $this->_clear();

        return $this;
    }

    /**
     * Clear all recipients
     *
     * @return $this
     */
    protected function _clear()
    {
        $this->_mailer->ClearAllRecipients();

        return $this;
    }

}
