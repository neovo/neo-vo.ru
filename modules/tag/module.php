<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Tags.
 *
 * @package HostCMS 6\Tag
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Tag_Module extends Core_Module
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
	protected $_moduleName = 'tag';
	
	/**
	 * Constructor.
	 */
		parent::__construct();
		
				'ico' => 'fa fa-tags',