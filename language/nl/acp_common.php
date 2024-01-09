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

$lang = array_merge($lang, array(
	'MENTION_LENGTH'                => 'Vermelding systeem minimale lengte',
	'MENTION_LENGTH_EXPLAIN'        => 'The minimum text length before the simple mention dropdown is shown. 
	On larger boards you might want to increase this value.',
	'MENTION_COLOR'                 => 'Vermelding systeem kleur',
	'MENTION_COLOR_EXPLAIN'         => 'Deze kleur wordt in het bericht gebruikt om te bepalen welke gebruiker wordt genoemd. Alleen hexadecimale waarden kunnen worden gebruikt.',
	'MENTION_COLOR_INVALID'         => 'De geselecteerde vermelding kleur (%s) is ongeldig. Selecteer een geldige HEX kleur, zonder #',
	'MENTION_MAX_RESULTS'			=> 'Eenvoudige vermelding maximale resultaten',
	'MENTION_MAX_RESULTS_EXPLAIN'	=> 'Het maximale aantal gebruikers wordt weergegeven in de vervolgkeuzelijst. Op grotere forums wil je deze waarde misschien verlagen',
	'MENTION_LARGE_GROUPS'			=> 'Vermeld grote groepsgrootte',
	'MENTION_LARGE_GROUPS_EXPLAIN'	=> 'Als de groep meer leden heeft dan het opgegeven aantal, is de permissie “Mag grote groepen vermelden” vereist.'
));
