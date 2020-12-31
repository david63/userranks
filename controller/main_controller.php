<?php
/**
*
* @package User Ranks Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\userranks\controller;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\db\driver\driver_interface;
use phpbb\controller\helper;
use phpbb\path_helper;
use phpbb\cache\service;
use phpbb\auth\auth;
use phpbb\language\language;

/**
* Main controller
*/
class main_controller implements main_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var string phpBB tables */
	protected $tables;

	/**
	* Constructor for main controller
	*
	* @param \phpbb\config\config		$config				Config object
	* @param \phpbb\template\template	$template			Template object
	* @param \phpbb_db_driver			$db					Database object
	* @param \phpbb\controller\helper	controller_helper	Controller helper object
	* @param \phpbb\path_helper			$path_helper		phpBB helper
	* @param \phpbb\cache\service		$cache				Cache object
	* @param \phpbb\auth\auth 			$auth				Auth object
	* @param \phpbb\language\language	$language			Language object
	* @param array						$tables				phpBB db tables
	*
	* @access public
	*/
	public function __construct(config $config, template $template, driver_interface $db, helper $helper, path_helper $path_helper, service $cache, auth $auth, language $language, $tables)
	{
		$this->config		= $config;
		$this->template		= $template;
		$this->db			= $db;
		$this->helper		= $helper;
		$this->path_helper	= $path_helper;
		$this->cache		= $cache;
		$this->auth			= $auth;
		$this->language		= $language;
		$this->tables		= $tables;
	}

	/**
	* Display the user ranks page
	*
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	* @access public
	*/
	public function display($name)
	{
		// Get the rank details
		$sql = 'SELECT *
			FROM ' . $this->tables['ranks'] . '
			ORDER BY rank_special DESC, rank_min ASC, rank_title ASC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if (($this->config['userranks_special'] || ($this->config['userranks_special_admin'] && $this->auth->acl_get('a_'))) || (!$this->config['userranks_special'] && !$row['rank_special']))
			{
				$rank_row = array(
					'MIN_POSTS'			=> $row['rank_min'],

					'RANK_IMAGE'		=> $this->path_helper->get_web_root_path() . $this->config['ranks_path'] . '/' . $row['rank_image'],
					'RANK_TITLE'		=> $row['rank_title'],

					'S_RANK_IMAGE'		=> ($row['rank_image']) ? true : false,
					'S_SPECIAL_RANK'	=> ($row['rank_special']) ? true : false,
				);

				$this->template->assign_block_vars('ranks', $rank_row);

				// Are we displaying members?
				if ($this->config['userranks_members'] || ($this->config['userranks_members_admin'] && $this->auth->acl_get('a_')))
				{
					$rank_users = $this->get_user_rank_data($row['rank_id']);

					if (!empty($rank_users) > 0)
					{
						foreach ($rank_users as $row_rank)
						{
							$this->template->assign_block_vars('ranks.rank_member', array(
								'MEMBERS' => get_username_string('full', $row_rank['user_id'], $row_rank['username'], $row_rank['user_colour']),
							));
						}
					}
					else
					{
						$this->template->assign_block_vars('ranks.rank_member', array(
							'MEMBERS' => $this->language->lang('NO_MEMBERS'),
						));
					}
				}
			}
		}
		$this->db->sql_freeresult($result);

		// Assign breadcrumb template vars for the user ranks page
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->language->lang('USER_RANKS'),
			'U_VIEW_FORUM'	=> $this->helper->route('david63_userranks_main_controller', array('name' => 'ranks')),
		));

		// Send all data to the template file
		return $this->helper->render('user_ranks.html', $name);
	}

	/**
	* Obtain an array of users in a rank.
	*
	* @return array
	*/
	protected function get_user_rank_data($rank_id)
	{
		$rank_data = $rank_users = [];

		if (($rank_data = $this->cache->get('_rank_data')) === false)
		{
			$ranks = $this->cache->obtain_ranks();
			$where = $this->config['userranks_ignore_bots'] ? 'WHERE user_type <> ' . USER_IGNORE . '' : '';

			$sql = 'SELECT user_id, user_colour, username, user_rank, user_posts
				FROM ' . $this->tables['users'] . "
					$where
				ORDER BY username_clean ASC";
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if (!empty($row['user_rank']))
				{
					$rank_data[$row['user_id']] = $row;
				}
				else if ($row['user_posts'] !== false)
				{
					if (!empty($ranks['normal']))
					{
						foreach ($ranks['normal'] as $rank)
						{
							if ($row['user_posts'] >= $rank['rank_min'])
							{
								$row['user_rank'] 			= $rank['rank_id'];
								$rank_data[$row['user_id']] = $row;
								break;
							}
						}
					}
				}
			}
			$this->db->sql_freeresult($result);

			// Cache this data to save processing
			$this->cache->put('_rank_data', $rank_data, $this->config['load_online_time']);
		}

		foreach ($rank_data as $user_rank)
		{
			if ($user_rank['user_rank'] == $rank_id)
			{
				$rank_users[$user_rank['user_id']] = $user_rank;
			}
		}
		return $rank_users;
	}
}
