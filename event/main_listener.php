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
use phpbb\auth\auth;
use phpbb\controller\helper;
use phpbb\db\driver\driver;
use phpbb\db\driver\driver_interface;
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
     * @var string
     */
    private $regex = "#\[mention\](.*?)\[/mention\]#si";

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
     * @var array
     */
    private $mention_data;

    /**
     * @var auth
     */
    private $auth;

    /**
     * Constructor
     *
     * @param helper $helper Controller helper object
     * @param template $template Template object
     * @param driver_interface $db
     * @param manager $notification_manager
     * @param user $user
     * @param auth $auth
     */
	public function __construct(helper $helper, template $template, driver_interface $db, manager $notification_manager, user $user, auth $auth)
	{
		$this->helper = $helper;
		$this->template = $template;
        $this->db = $db;
        $this->notification_manager = $notification_manager;
        $this->user = $user;
        $this->auth = $auth;
    }

    static public function getSubscribedEvents()
    {
        return [
            'core.submit_post_end'          => 'submit_post',
            'core.modify_submit_post_data'  => 'modify_submit_post',
            'core.permissions'              => 'add_permission',
            'core.user_setup'			    => 'load_language_on_setup',
        ];
    }

    public function add_permission($event)
    {
        $permissions = $event['permissions'];
        $permissions['u_can_mention'] = array('lang' => 'ACL_U_CAN_MENTION', 'cat' => 'misc');
        $event['permissions'] = $permissions;
    }

    /**
     * Load common language files during user setup
     *
     * @param object $event The event object
     * @return null
     * @access public
     */
    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'paul999/mention',
            'lang_set' => 'common',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }

    /**
     * @param array $event
     */
    public function modify_submit_post($event)
    {
        $handle = ['post', 'reply'];

        if (!in_array($event['mode'], $handle) || !$this->auth->acl_get('u_can_mention')) {
            return;
        }

        $matches = [];
        if (preg_match_all($this->regex, $event['data']['message'], $matches) === 0) {
            return;
        }
        $this->mention_data = [];

        $sql = 'SELECT user_id, username 
                FROM ' . USERS_TABLE . '
                WHERE ' . $this->db->sql_in_set('username_clean', $matches[1]) . ' OR ' .
                          $this->db->sql_in_set('user_id', $matches[1]);
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result)) {
            $this->mention_data[] = (int)$row['user_id'];
        }
        $this->db->sql_freeresult($result);

        $event['data']['message'] = preg_replace($this->regex, '', $event['data']['message']);
    }
    function submit_post($event)
    {
        if (sizeof($this->mention_data))
        {
            $this->notification_manager->add_notifications('paul999.mention.notification.type.mention', [
                'user_ids'		    => $this->mention_data,
                'notification_id'   => $event['data']['post_id'],
                'username'          => $this->user->data['username'],
                'poster_id'         => $this->user->data['user_id'],
                'post_id'           => $event['data']['post_id'],
            ],
            [
                'user_ids'		    => $this->mention_data,
            ]);
        }
        return;
    }
}
