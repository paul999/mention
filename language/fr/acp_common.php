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
	'MENTION_LENGTH'                => 'Longueur minimum des mentions',
	'MENTION_LENGTH_EXPLAIN'        => 'Longueur minimum avant d’afficher les suggestions de mention. Il est conseillé d’augmenter ce nombre sur les forums avec des nombreux utilisateurs.',
	'MENTION_COLOR'                 => 'Couleur des mentions',
	'MENTION_COLOR_EXPLAIN'         => 'Cette couleur est utiliser dans le post pour désigner l’utilisateur mentionné. Valeurs hexadécimales uniquement.',
	'MENTION_COLOR_INVALID'         => 'La couleur choisi (%s) est invalide. Merci de choisir une couleur en valeur HEX, sans le #',
));
