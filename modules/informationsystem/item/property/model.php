<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Information systems.
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Item_Property_Model extends Core_Entity{
	/**
	 * Disable markDeleted()
	 * @var mixed
	 */	protected $_marksDeleted = NULL;
	/**
	 * Belongs to relations
	 * @var array
	 */	protected $_belongsTo = array(		'informationsystem' => array(),		'property' => array(),	);}