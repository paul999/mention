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

	public function main($id, $mode)
	{
		global $config, $request, $template, $user;

		$user->add_lang_ext('paul999/mention', 'common');
		$this->tpl_name = 'acp_demo_body';
		$this->page_title = $user->lang('ACP_DEMO_TITLE');
		add_form_key('acme/demo');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('acme/demo'))
			{
				trigger_error('FORM_INVALID');
			}

			$config->set('acme_demo_goodbye', $request->variable('acme_demo_goodbye', 0));

			trigger_error($user->lang('ACP_DEMO_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'ACME_DEMO_GOODBYE'		=> $config['acme_demo_goodbye'],
		));
	}
}
