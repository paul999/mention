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
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver;
use phpbb\db\driver\driver_interface;
use phpbb\notification\manager;
use phpbb\template\template;
use phpbb\user;
use phpbb\viewonline_helper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * phpBB mentions Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/**
	 * @var string
	 */
	private $regex = '#\[mention\]<\/s>(.*?)<e>\[\/mention\]#';

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
	 * @var config
	 */
	private $config;
	/**
	 * @var viewonline_helper
	 */
	private $viewonline_helper;

	/**
	 * Constructor
	 *
	 * @param helper $helper Controller helper object
	 * @param template $template Template object
	 * @param driver_interface $db
	 * @param manager $notification_manager
	 * @param user $user
	 * @param auth $auth
	 * @param config $config
	 * @param viewonline_helper $viewonline_helper
	 */
	public function __construct(helper $helper, template $template, driver_interface $db, manager $notification_manager, user $user, auth $auth, config $config, viewonline_helper $viewonline_helper)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->db = $db;
		$this->notification_manager = $notification_manager;
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->viewonline_helper = $viewonline_helper;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.submit_post_end'                  => 'submit_post',
			'core.modify_submit_post_data'          => 'modify_submit_post',
			'core.permissions'                      => 'add_permission',
			'core.user_setup'			            => 'load_language_on_setup',
			'core.modify_posting_auth'              => 'posting',
			'core.viewtopic_modify_page_title'      => 'viewtopic',
			'core.text_formatter_s9e_parse_before'  => 'permissions',
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
	public function viewtopic($event) {
		$s_quick_reply = false;
		if ($this->user->data['is_registered'] && $this->config['allow_quick_reply'] && ($event['topic_data']['forum_flags'] & FORUM_FLAG_QUICK_REPLY) && $this->auth->acl_get('f_reply', $event['forum_id']))
		{
			// Quick reply enabled forum
			$s_quick_reply = (($event['topic_data']['forum_status'] == ITEM_UNLOCKED && $event['topic_data']['topic_status'] == ITEM_UNLOCKED) || $this->auth->acl_get('m_edit', $event['forum_id'])) ? true : false;
		}
		if ($s_quick_reply)
		{
			$this->template->assign_vars([
				'UA_AJAX_MENTION_URL'    => $this->helper->route('paul999_mention_controller'),
			]);
		}
	}

	/**
	 * @param array $event
	 */
	public function posting($event) {
		if ($this->auth->acl_get('u_can_mention'))
		{
			$this->template->assign_vars([
			   'UA_AJAX_MENTION_URL'    => $this->helper->route('paul999_mention_controller'),
			]);
		}
	}

	/**
	 * @param array $event
	 */
	public function permissions($event)
	{
		$disable = false;
		if (!$this->auth->acl_get('u_can_mention'))
		{
			$disable = true;
		}

		$on_page = $this->viewonline_helper->get_user_page($this->user['session_page']);

		if ($on_page[1] !== 'posting')
		{
			// Only enable mention BBCode on posting page.
			$disable = true;
		}

		if ($disable)
		{
			$event['parser']->disable_bbcode('mention');
		}
	}

	/**
	 * @param array $event
	 */
	public function modify_submit_post($event)
	{
		$handle = ['post', 'reply', 'quote'];

		if (!in_array($event['mode'], $handle) || !$this->auth->acl_get('u_can_mention')) {
			return;
		}

		$matches = [];
		$message = $event['data']['message'];
		if (preg_match_all($this->regex, $message, $matches, PREG_OFFSET_CAPTURE) === 0)
		{
			return;
		}
		$this->mention_data = [];
		$mentions = [];
		$data = [];

		for ($i = 0; $i < sizeof($matches[1]); $i++)
		{
			$data[] = utf8_clean_string($matches[1][$i][0]);
		}

		$sql = 'SELECT user_id, username, user_permissions, user_type
				FROM ' . USERS_TABLE . '
				WHERE ' . $this->db->sql_in_set('username_clean', $data);
		$result = $this->db->sql_query($sql);

		$data = [];

		while ($row = $this->db->sql_fetchrow($result))
		{
			if (!in_array($row['user_id'], $mentions))
			{
				$mentions[] = (int)$row['user_id'];
				$data[] = $row;
			}
		}
		$this->db->sql_freeresult($result);

		if (sizeof($data))
		{
			foreach ($data as $index => $row)
			{
				if ($this->user->data['user_id'] == $row['user_id'])
				{
					continue; // Do not send notification to current user.
				}
				$auth = new auth();
				$auth->acl($row);
				if ($auth->acl_get('f_read', $event['data']['forum_id']))
				{
					// Only do the mention when the user is able to read the forum
					$this->mention_data[] = (int)$row['user_id'];
				}
			}
		}
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
                'topic_id'          => $event['data']['topic_id'],
			],
			[
				'user_ids'		    => $this->mention_data,
			]);
		}
		return;
	}
}
