<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/**
 * As there is no proper way of including this file just when the notification is
 * loaded we need to include it on all pages. Make sure to only include important
 * language items (That are directly needed by the notification system) in this file.
 */
$lang = array_merge($lang, array(
	'MENTION_MENTION_NOTIFICATION'	=> 'You were mentioned by %1$s<br />in “%2$s”',
	'NOTIFICATION_TYPE_MENTION'     => 'Someone mentioned me',
	'MENTION_GROUP_NAME'			=> '(Group. Will notify {CNT} users in total)', // Do not translate/change {CNT}
	'MENTION_NO_MATCH_FOUND'		=> 'No Match Found!',
));
