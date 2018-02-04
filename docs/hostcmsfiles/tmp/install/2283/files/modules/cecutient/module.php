<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Cecutient Module.
 *
 * @package HostCMS
 * @subpackage Cecutient
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Cecutient_Module extends Core_Module
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
	public $date = '2016-05-23';

	/**
	 * Module name
	 * @var string
	 */
	protected $_moduleName = 'cecutient';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		Core_Page::instance()->css('/hostcmsfiles/cecutient/cecutient.css');
	}
}