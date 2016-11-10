<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paul999\mention\acp;

/**
 * phpBB mentions ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\paul999\mention\acp\main_module',
			'title'		=> 'ACP_DEMO_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_DEMO',
					'auth'	=> 'ext_paul999/mention && acl_a_board',
					'cat'	=> array('ACP_DEMO_TITLE')
				),
			),
		);
	}
}
