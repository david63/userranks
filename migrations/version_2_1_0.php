<?php
/**
*
* @package User Ranks Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\userranks\migrations;

use phpbb\db\migration\migration;

class version_2_1_0 extends migration
{
	static public function depends_on()
	{
		return array('\david63\userranks\migrations\version_1_0_0');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('userranks_quick_link', '1')),

			array('config.remove', array('userranks_enable')),
			array('config.remove', array('userranks_version')),
		);
	}
}
