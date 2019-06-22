<?php
/**
*
* @package User Ranks Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\userranks\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\controller\helper;
use phpbb\auth\auth;
use david63\userranks\core\functions;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template\template */
	protected $template;

	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \david63\userranks\core\functions */
	protected $functions;

	/**
	* Constructor for listener
	*
	* @param \phpbb\config\config				$config				Config object
	* @param \phpbb\template\template			$template			Template object
	* @param \phpbb\controller\helper			$controller_helper	Controller helper object
	* @param \phpbb\auth\auth 					$auth				Auth object
	* @param \david63\userranks\core\functions	$functions			Functions for the extension
	*
	* @access public
	*/
	public function __construct(config $config, template $template, helper $controller_helper, auth $auth, functions $functions)
	{
		$this->config				= $config;
		$this->template				= $template;
		$this->controller_helper	= $controller_helper;
		$this->auth					= $auth;
		$this->functions			= $functions;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'	=> 'load_language_on_setup',
			'core.page_header'	=> 'page_header',
		);
	}

	/**
	* Load common user ranks language files during user setup
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function load_language_on_setup($event)
	{
		$lang_set_ext	= $event['lang_set_ext'];
		$lang_set_ext[]	= array(
			'ext_name' => $this->functions->get_ext_namespace(),
			'lang_set' => 'user_ranks_common',
		);

		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* Add the required template variables
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function page_header($event)
	{
		$this->template->assign_vars(array(
			'S_USER_RANKS_LINK_ENABLED'			=> $this->config ['userranks_header_link'],
			'S_USER_RANKS_QUICK_LINK_ENABLED'	=> $this->config ['userranks_quick_link'],

			'U_USER_RANKS' 						=> $this->controller_helper->route('david63_userranks_main_controller', array('name' => 'ranks')),
			'U_USER_RANKS_MEMBERS'				=> $this->config['userranks_members'] || ($this->config['userranks_members_admin'] && $this->auth->acl_get('a_')),
		));
	}

}
