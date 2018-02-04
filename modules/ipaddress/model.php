<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * IP addresses.
 *
 * @package HostCMS 6\Ipaddress
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Ipaddress_Model extends Core_Entity
	/**
	 * Column consist item's name
	 * @var string
	 */
	protected $_nameColumn = 'ip';

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	/**
	 * Get ipaddress by ip
	 * @param string $ip ip
	 * @return Ip|NULL
	 */
		$aIp = $this->findAll();
		return isset($aIp[0])
			? $aIp[0]
			: NULL;
	}
	/**
	 * Change access mode
	 * @return self
	 */
	
	 * Change statistic mode
	 * @return self
	 */

	/**
	 * Check if there another ip with this address is
	 * @return self
	 */
	protected function _checkDuplicate()
	{
		$oIpaddressDublicate = Core_Entity::factory('Ipaddress')->getByIp($this->ip);

		if (!is_null($oIpaddressDublicate) && $oIpaddressDublicate->id != $this->id)
		{
			$this->id = $oIpaddressDublicate->id;
		}

		return $this;
	}

	/**
	 * Update object data into database
	 * @return Core_ORM
	 */
	public function update()
	{
		$this->_checkDuplicate();
		return parent::update();
	}

	/**
	 * Save object.
	 *
	 * @return Core_Entity
	 */
	public function save()
	{
		$this->_checkDuplicate();
		return parent::save();
	}