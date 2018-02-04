<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Benchmark_Model
 *
 * @package HostCMS
 * @subpackage Benchmark
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Benchmark_Model extends Core_Entity
{
	/**
	 * Model name
	 * @var mixed
	 */
	protected $_modelName = 'benchmark';

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'site' => array()
	);

	/**
	 * MySQL etalon write
	 * @var mixed
	 */
	public $etalon_mysql_write = 10000;

	/**
	 * MySQL etalon read
	 * @var mixed
	 */
	public $etalon_mysql_read = 10000;

	/**
	 * MySQL etalon update
	 * @var mixed
	 */
	public $etalon_mysql_update = 10000;

	/**
	 * File system etalon, ops
	 * @var mixed
	 */
	public $etalon_filesystem = 15000;

	/**
	 * CPU math etalon
	 * @var mixed
	 */
	public $etalon_cpu_math = 1000000;

	/**
	 * CPU string etalon
	 * @var mixed
	 */
	public $etalon_cpu_string = 1500000;

	/**
	 * Network etalon, Mbps
	 * @var mixed
	 */
	public $etalon_network = 10;

	/**
	 * Mail etalon, sec
	 * @var mixed
	 */
	public $etalon_mail = 0.0500;

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$this->_preloadValues['datetime'] = Core_Date::timestamp2sql(time());
		}
	}

	/**
	 * Get coefficient for benchmark
	 * @return self
	 */
	public function getCoefficient($value, $max)
	{
		$iCoefficient = intval($value * 100 / $max);

		return $iCoefficient < 100
			? $iCoefficient
			: 100;
	}

	/**
	 * Get coefficient for mail
	 * @return self
	 */
	public function getMailCoefficient()
	{
		$iMin = $this->etalon_mail;

		if ($this->mail < $this->etalon_mail)
		{
			return 100;
		}
		elseif ($this->mail > $this->etalon_mail + 1)
		{
			return 0;
		}

		return 100 - ($this->mail - $this->etalon_mail) * 100;
	}

	public function getBenchmark()
	{
		return ceil((
			$this->getCoefficient($this->mysql_write, $this->etalon_mysql_write) +
			$this->getCoefficient($this->mysql_read, $this->etalon_mysql_read) +
			$this->getCoefficient($this->mysql_update, $this->etalon_mysql_update) +
			$this->getCoefficient($this->filesystem, $this->etalon_filesystem) +
			$this->getCoefficient($this->cpu_math, $this->etalon_cpu_math) +
			$this->getCoefficient($this->cpu_string, $this->etalon_cpu_string) +
			$this->getCoefficient($this->network, $this->etalon_network) +
			$this->getMailCoefficient())
			/ 8);
	}
}