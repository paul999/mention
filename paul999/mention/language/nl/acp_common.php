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
	'MENTION_LENGTH'                => 'Minimale lengte voor eenvoudige vermeldingen',
	'MENTION_LENGTH_EXPLAIN'        => 'De minimale tekstlengte voordat de keuzelijst voor eenvoudige vermeldingen wordt weergegeven. 
	Op grotere forums kun je deze waarde verhogen.',
	'MENTION_COLOR'                 => 'Kleur voor eenvoudige vermeldingen',
	'MENTION_COLOR_EXPLAIN'         => 'Deze kleur wordt in berichten gebruikt om aan te geven welke gebruiker wordt vermeld. Alleen hexadecimale waarden zijn toegestaan.',
	'MENTION_COLOR_INVALID'         => 'De geselecteerde vermeldingskleur (%s) is ongeldig. Selecteer een geldige HEX-kleur, zonder #',
	'MENTION_MAX_RESULTS'			=> 'Maximaal aantal resultaten voor eenvoudige vermeldingen',
	'MENTION_MAX_RESULTS_EXPLAIN'	=> 'Het maximale aantal gebruikers dat in de keuzelijst wordt weergegeven. Op grotere forums kun je deze waarde verlagen',
	'MENTION_LARGE_GROUPS'			=> 'Groepsgrootte voor grote groepsvermeldingen',
	'MENTION_LARGE_GROUPS_EXPLAIN'	=> 'Als de groep meer leden heeft dan het opgegeven aantal, is de permissie “Kan grote groepen vermelden” vereist.'
));