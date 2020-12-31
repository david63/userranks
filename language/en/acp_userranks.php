<?php
/**
*
* @package User Ranks Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'USER_RANKS_BOTS'					=> 'Ignore bots & anonymous',
	'USER_RANKS_BOTS_EXPLAIN'			=> 'Do not include Bots & anonymous in the list of rank members.',

	'USER_RANKS_EXPLAIN'				=> 'Here you can manage the User Rank list display options.',

	'USER_RANKS_HEADER'					=> 'Display user ranks link in the header',

	'USER_RANKS_MEMBERS'				=> 'Show members',
	'USER_RANKS_MEMBERS_ADMIN'			=> 'Show members to Admins',
	'USER_RANKS_MEMBERS_ADMIN_EXPLAIN'	=> 'Only show the members in each rank to Admins.',
	'USER_RANKS_MEMBERS_EXPLAIN'		=> 'Show the members who are in each rank on the User Ranks list to all logged in users.',

	'USER_RANKS_OPTIONS'				=> 'User rank options',

	'USER_RANKS_QUICK_LINK'				=> 'Display user ranks link in the quick links menu',

	'USER_RANKS_SPECIAL'				=> 'Show special ranks',
	'USER_RANKS_SPECIAL_ADMIN'			=> 'Show special ranks to admins',
	'USER_RANKS_SPECIAL_ADMIN_EXPLAIN'	=> 'Only show special ranks to Admins.',
	'USER_RANKS_SPECIAL_EXPLAIN'		=> 'Show the special ranks on the User Ranks list to all logged in users.',
));
