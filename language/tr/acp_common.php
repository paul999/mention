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
	'TRANSLATION_INFO'	=> '<br />Tercüme: <a href="https://obelde.com/">O Belde</a> <a href="https://forum.obelde.com/">Forum</a>',
	'MENTION_LENGTH'                => 'Minimum bahsetme uzunluğu',
	'MENTION_LENGTH_EXPLAIN'        => 'Minimum harf girildiğinde tavsiye listesi açılır.
	Çok fazla üyeniz varsa bu değeri arttırmanızı tavsiye ederiz.',
	'MENTION_COLOR'                 => 'Bahsetme rengi',
	'MENTION_COLOR_EXPLAIN'         => 'Bu renk sadece bahsedildiğini göstermek için kullanılır. Sadece Hex renk kodları kullanılabilir.',
	'MENTION_COLOR_INVALID'         => 'Seçilen renk (%s) geçersizdir. Lütfen renk kodunun başına # işaretini koymayın.',
));
