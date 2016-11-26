<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paul999\mention\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_MENTION_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_MENTION_TITLE',
				array(
					'module_basename'	=> '\paul999\mention\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
