<?php
/**
*
* @package User Ranks Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\userranks\migrations;

class version_1_0_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('config.add', array('userranks_enable', '1')),
			array('config.add', array('userranks_header_link', '1')),
			array('config.add', array('userranks_ignore_bots', '0')),
			array('config.add', array('userranks_members', '0')),
			array('config.add', array('userranks_members_admin', '1')),
			array('config.add', array('userranks_special', '0')),
			array('config.add', array('userranks_special_admin', '1')),
			array('config.add', array('userranks_version', '1.0.0')),

			// Add the ACP module
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'USER_RANKS')),

			array('module.add', array(
				'acp', 'USER_RANKS', array(
					'module_basename'	=> '\david63\userranks\acp\userranks_module',
					'modes'				=> array('main'),
				),
			)),
		);
	}
}
