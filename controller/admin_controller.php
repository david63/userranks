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
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbb\log\log;
use phpbb\language\language;
use david63\userranks\core\functions;

/**
* Admin controller
*/
class admin_controller implements admin_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \david63\userranks\core\functions */
	protected $functions;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor for admin controller
	*
	* @param \phpbb\config\config				$config		Config object
	* @param \phpbb\request\request				$request	Request object
	* @param \phpbb\template\template			$template	Template object
	* @param \phpbb\user						$user		User object
	* @param \phpbb\log\log						$log		Log object
	* @param \phpbb\language\language			$language	Language object
	* @param \david63\userranks\core\functions	$functions	Functions for the extension
	*
	* @return \david63\userranks\controller\admin_controller
	* @access public
	*/
	public function __construct(config $config, request $request, template $template, user $user, log $log, language $language, functions $functions)
	{
		$this->config		= $config;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->log			= $log;
		$this->language		= $language;
		$this->functions	= $functions;
	}

	/**
	* Display the options a user can configure for this extension
	*
	* @return null
	* @access public
	*/
	public function display_options()
	{
		// Add the language files
		$this->language->add_lang('acp_userranks', $this->functions->get_ext_namespace());
		$this->language->add_lang('acp_common', $this->functions->get_ext_namespace());

		// Create a form key for preventing CSRF attacks
		$form_key = 'userranks_manage';
		add_form_key($form_key);

		$back = false;

		// Is the form being submitted
		if ($this->request->is_set_post('submit'))
		{
			// Is the submitted form is valid
			if (!check_form_key($form_key))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// If no errors, process the form data
			// Set the options the user configured
			$this->set_options();

			// Add option settings change action to the admin log
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'USER_RANKS_LOG');

			// Option settings have been updated and logged
			// Confirm this to the user and provide link back to previous page
			trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		// Template vars for header panel
		$version_data	= $this->functions->version_check();

		$this->template->assign_vars(array(
			'DOWNLOAD'			=> (array_key_exists('download', $version_data)) ? '<a class="download" href =' . $version_data['download'] . '>' . $this->language->lang('NEW_VERSION_LINK') . '</a>' : '',

			'HEAD_TITLE'		=> $this->language->lang('USER_RANKS'),
			'HEAD_DESCRIPTION'	=> $this->language->lang('USER_RANKS_EXPLAIN'),

			'NAMESPACE'			=> $this->functions->get_ext_namespace('twig'),

			'S_BACK'			=> $back,
			'S_VERSION_CHECK'	=> (array_key_exists('current', $version_data)) ? $version_data['current'] : false,

			'VERSION_NUMBER'	=> $this->functions->get_meta('version'),
		));

		// Set output vars for display in the template
		$this->template->assign_vars(array(
			'USER_RANKS_BOTS'			=> isset($this->config['userranks_ignore_bots']) ? $this->config['userranks_ignore_bots'] : '',
			'USER_RANKS_HEADER'			=> isset($this->config['userranks_header_link']) ? $this->config['userranks_header_link'] : '',
			'USER_RANKS_MEMBERS' 		=> isset($this->config['userranks_members']) ? $this->config['userranks_members'] : '',
			'USER_RANKS_MEMBERS_ADMIN'	=> isset($this->config['userranks_members_admin']) ? $this->config['userranks_members_admin'] : '',
			'USER_RANKS_QUICK_LINK'		=> isset($this->config['userranks_quick_link']) ? $this->config['userranks_quick_link'] : '',
			'USER_RANKS_SPECIAL' 		=> isset($this->config['userranks_special']) ? $this->config['userranks_special'] : '',
			'USER_RANKS_SPECIAL_ADMIN'	=> isset($this->config['userranks_special_admin']) ? $this->config['userranks_special_admin'] : '',
			'U_ACTION' 					=> $this->u_action,
		));
	}

	/**
	* Set the options a user can configure
	*
	* @return null
	* @access protected
	*/
	protected function set_options()
	{
		$this->config->set('userranks_header_link', $this->request->variable('userranks_header_link', 0));
		$this->config->set('userranks_ignore_bots', $this->request->variable('userranks_ignore_bots', 0));
		$this->config->set('userranks_members', $this->request->variable('userranks_members', 0));
		$this->config->set('userranks_members_admin', $this->request->variable('userranks_members_admin', 0));
		$this->config->set('userranks_quick_link', $this->request->variable('userranks_quick_link', 0));
		$this->config->set('userranks_special', $this->request->variable('userranks_special', 0));
		$this->config->set('userranks_special_admin', $this->request->variable('userranks_special_admin', 0));
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return null
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}

}
