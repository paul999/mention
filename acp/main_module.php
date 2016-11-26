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
 * phpBB mentions ACP module.
 */
class main_module
{
	public $u_action;
    public $tpl_name;
    public $page_title;

    public function main($id, $mode)
	{
        global $phpbb_container, $language;

		$this->tpl_name = 'mention_body';
		$this->page_title = $language->lang('ACP_MENTION_TITLE');

        $phpbb_container->get('paul999.mention.admin_controller')->page();
	}
}
