<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Updates.
 *
 * @package HostCMS 6\Update
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Update_Module extends Core_Module
{
	/**
	 * Module version
	 * @var string
	 */
	public $version = '6.5';

	/**
	 * Module date
	 * @var date
	 */
	public $date = '2015-06-05';

	/**
	 * Module name
	 * @var string
	 */
	protected $_moduleName = 'update';
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->menu = array(
			array(
				'sorting' => 150,
				'block' => 3,
				'ico' => 'fa fa-refresh',
				'name' => Core::_('Update.menu'),
				'href' => "/admin/update/index.php",
				'onclick' => "$.adminLoad({path: '/admin/update/index.php'}); return false"
			)
		);
	}
}