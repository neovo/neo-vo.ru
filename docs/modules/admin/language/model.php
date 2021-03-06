<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin_Language_Model
 *
 * @package HostCMS
 * @subpackage Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Language_Model extends Core_Entity
{
	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'active' => 1,
		'sorting' => 0
	);

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'admin_word_value' => array()
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'admin_languages.sorting' => 'ASC'
	 );

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;
		}
	}

	/**
	 * Get current admin language
	 * @return Admin_Language|NULL
	 */
	public function getCurrent()
	{
		$sCurrentLng = Core_I18n::instance()->getLng();

		$oAdmin_Language = $this->getByShortname($sCurrentLng);

		if ($oAdmin_Language)
		{
			return $oAdmin_Language;
		}

		// Первый язык в списке
		$this->queryBuilder()->clear();
		$aAdmin_Language = $this->findAll();

		return count($aAdmin_Language)
			? $aAdmin_Language[0]
			: NULL;
	}

	/**
	 * Get admin language by short name
	 * @param string $shortname short name
	 * @return Admin_Language|NULL
	 */
	public function getByShortname($shortname)
	{
		$this->queryBuilder()
			->clear()
			->where('shortname', '=', $shortname)
			->where('active', '=', 1)
			->limit(1);

		$aAdmin_Language = $this->findAll();

		if (count($aAdmin_Language) > 0)
		{
			return $aAdmin_Language[0];
		}

		return NULL;
	}

	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Core_Entity
	 */
	public function delete($primaryKey = NULL)
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}

		$this->id = $primaryKey;

		$admin_word_values = $this->admin_word_values->findAll();

		foreach($admin_word_values AS $oAdmin_Word_Value)
		{
			$oAdmin_Word_Value->delete();
		}

		return parent::delete($primaryKey);
	}
}