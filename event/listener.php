<?php
/**
*
* @package Faulty logins
* @copyright (c) 2014 John Peskens (http://ForumHulp.com)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\faultylogins\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $user;
	protected $log;

	/**
	* Constructor
	*/
	public function __construct(\phpbb\user $user, \phpbb\log\log $log)
	{
		$this->user = $user;
		$this->log = $log;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.login_box_failed' => 'log_login'
		);
	}

	public function log_login($event)
	{
		$eventvars = $event['result'];
		if (in_array($eventvars['status'], range(10, 15)))
		{
			$this->log->add('admin', $eventvars['user_row']['user_id'], $this->user->ip, 'LOG_FAULTY_LOGINS', time(),
				array(($eventvars['user_row']['user_id'] == 1) ? 'Guest': $eventvars['user_row']['username'], $event['password'], $this->user->lang[$eventvars['error_msg']]));
		}
	}
}
