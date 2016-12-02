<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paul999\mention\event;

/**
 * @ignore
 */
use phpbb\controller\helper;
use phpbb\db\driver\driver;
use phpbb\notification\manager;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * phpBB mentions Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/**
     * @var helper
     */
	protected $helper;

	/**
     * @var template
     */
	protected $template;

    /**
     * @var driver
     */
    private $db;

    /**
     * @var manager
     */
    private $notification_manager;

    /**
     * @var user
     */
    private $user;

    /**
     * Constructor
     *
     * @param helper $helper Controller helper object
     * @param template $template Template object
     * @param driver $db
     * @param manager $notification_manager
     * @param user $user
     */
	public function __construct(helper $helper, template $template, driver $db, manager $notification_manager, user $user)
	{
		$this->helper = $helper;
		$this->template = $template;
        $this->db = $db;
        $this->notification_manager = $notification_manager;
        $this->user = $user;
    }

    static public function getSubscribedEvents()
    {
        return [
            'core.submit_post_end'  => 'modify_submit_post',
        ];
    }

    /**
     * @param array $event
     */
    public function modify_submit_post($event) : void
    {
        $handle = ['post', 'reply'];

        if (!in_array($event['mode'], $handle))
        {
            return;
        }

        $matches = [];
        $regex = "#\[mention\](.*)\[/mention\]#si";
        if (preg_match_all($regex, $event['data']['message'], $matches) === 0)
        {
            return;
        }
        $mention_data = [];
        foreach($matches as $mention)
        {
            $sql = 'SELECT user_id, username FROM ' . USERS_TABLE . ' WHERE username_clean = \'' . $this->db->sql_escape($mention) . '\'';
            $result = $this->db->sql_query($sql);
            $row = $this->db->sql_fetchrow($result);

            $this->db->sql_freeresult($result);

            if ($row)
            {
                $mention_data[] = $row['user_id'];
            }
        }
        $this->notification_manager->add_notifications('paul999.mention.notification.type.mention', array(
            'user_ids'		    => $mention_data,
            'notification_id'   => $event['data']['post_id'],
            'username'          => $this->user->data['username'],
        ));


        $event['data']['message'] = preg_replace($regex, '', $event['data']['message']);
        return;
    }
}
