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
    const RECIPIENT_USER_ORDER   = 'order';

    /**
     * Class constructor
     *
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_mailer = JMail::getInstance();

        $this->registerCallback('preview');
    }

    /**
     * Launch notification
     * @return string
     */
    public function notify()
    {
        $renderer = $this->app->jbrenderer->create('email');
        $layout   = $this->config->get('layout_email', 'default');
        $html     = '';

        $html .= $renderer->render($layout, array(
            'subject' => $this->getSubject()
        ));

        $attach = $renderer->getAttach();
        $html   = $this->replace($html);

        $this
            ->setHead()
            ->setBody($html)
            ->isHtml(true)
            ->_setRecipients()
            ->setSender()
            ->addAttachment($attach)
            ->addImageItems();

        $this->_mailer->Send();

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
        if ($order->id && $items = $order->getItems()) {

            foreach ($items as $item) {

                if ($path = $item->get('image')) {
                    $path = JPATH_ROOT . DS . $path;
                    $file = $this->clean(basename($path));
                    $name = $this->clean($item->get('name'));
                    $cid  = $name . '-' . $file;
                    $cid  = JString::str_ireplace(' ', '', $cid);

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
     * @param string $cid  Unique
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
     * @param  string|array $name      - name of recipient
     *
     * @return $this
     */
    public function addRecipient($recipient, $name = '')
    {
        $this->_mailer->addRecipient($recipient, $name);

        return $this;
    }

    /**
     *
     */
    public function preview()
    {
        die('dasdasdasd');
        $html = $this->notify();

        $this->app->jbajax->send(array(
                'html' => $html
            )
        );
    }

    /**
     * Bind recipients into JMail from element config
     * @return $this
     */
    protected function _setRecipients()
    {
        $to     = $this->app->data->create($this->config->get('recipient'));
        $config = JFactory::getConfig();
        $name   = $config->get('sitename');

        //send notification to administrator's
        if ($adminRecipients = $to->get('admin', array())) {
            foreach ($adminRecipients as $id) {

                $users = JAccess::getUsersByGroup($id);
                if (!empty($users)) {
                    foreach ($users as $usrId) {
                        $user = JFactory::getUser($usrId);
                        $this->addRecipient($user->get('email'), $user->get('name', $name));
                    }
                }
            }
        }

        //send notification to user
        if ($userRecipients = $to->get('user', array())) {
            foreach ($userRecipients as $type) {

                //send to email from user profile
                if ($type == self::RECIPIENT_USER_PROFILE) {
                    $user = JFactory::getUser();
                    $this->addRecipient($user->get('email'), $user->get('name', $name));

                    //send to email from order field email
                } else if ($type == self::RECIPIENT_USER_ORDER) {

                }
            }
        }

        //advanced email's
        if ($advRecipients = $to->get('advanced', '')) {

            if (strpos($advRecipients, ',') === false) {
                $this->addRecipient($advRecipients);

            } else {
                $advRecipients = explode(',', $advRecipients);
                foreach ($advRecipients as $recipient) {
                    $recipient = JString::trim($recipient);
                    $this->addRecipient($recipient, $name);
                }
            }
        }

        return $this;
    }

}
