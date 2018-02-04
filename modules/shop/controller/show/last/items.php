<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Shop_Controller_Show_Last_Items - контроллер последних просмотренных товаров магазина
 * @version 2.0b
 *
 * @requires HostCMS 6.0.0+ 
 *
 * @author Eugene Strigo aka James V. Kotov, (©) 2011-2012.
 * @copyright 2010-2012
 * @access public
 */
 
class Shop_Controller_Show_Last_Items extends Core_Controller
{

	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		
		'limit', // макс. количество выводимых товаров 
		
		'is_random', // сортировка: TRUE - в случайном порядке; FALSE - в порядке, обратном посещениям
		
		'allow_show_current_item', // если находимся на странице товара: TRUE - разрешает показывать его среди последних просмотренных; FALSE - запрещает. 
		
		'show_parent_items_instead_modifications', // TRUE - показывать родительские товары вместо модификаций; FALSE - сами модификации.
		
		'exclude_incart_items', // TRUE - запрещает показывать товары, которые уже есть в корзине; FALSE - разрешает. 
		
		'exclude_out_of_rest_items', // TRUE - запрещает показывать товары, которых нет в наличии; FALSE - разрешает.
		
		'cache' // TRUE - разрешает использовать кеширование; FALSE - запрещает.
		
		);

	protected $_aShop_Last_Items = array(); // массив последних просмотренных элементов

	protected $_oShop_Controller_Show = null; // контроллер отображения магазина

	protected $_cookie_name = null; // название куки
	
	/**
	 * The singleton instances.
	 * @var mixed
	 */
	static public $instance = NULL;

	/**
	 * Constructor.
	 */
	public function __construct(Shop_Model $oShop)
	{
		parent::__construct($oShop->clearEntities());

		$this->_oShop_Controller_Show = new Shop_Controller_Show($oShop);

		// задаем базовые настройки контроллера отображения
		// они могут потом переопределятся из клиентской части.
		$this->_oShop_Controller_Show
			->group(false) // - вывод будет происходить из всех групп
			->groupsMode('none') // - информация о группах в XML не передается
			->offset(0) // начинаем показ с 0 элемента
			->cache(false) // отключаем кеширование
			->tags(false) // не выводим в XML теги
			->comments(false) // не выводим в XML комментарии
			->associatedItems(false) // не выводим в XML связанные товары
			->modifications(false) // не выводим в XML модификации
			->specialprices(false) // не выводим в XML спец.цены
			->itemsProperties(false) // не выводим в XML свойства товаров
			->groupsProperties(false) // не выводим в XML свойства групп
			->itemsForbiddenTags(array('text')); // не выводим полный текст товаров

		$this->_cookie_name = 'SHOP_LAST_ITEMS';

		$this->limit = 5;
		$this->is_random = false;
		$this->allow_show_current_item = false;
		$this->show_parent_items_instead_modifications = true;
		$this->exclude_incart_items = true;
		$this->exclude_out_of_rest_items = true;
		$this->cache = true;

		$this->loadLastItems();
	}


	/**
	 * Register an existing instance as a singleton.
	 * @return object
	 */
	static public function instance(Shop_Model $oShop)
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self($oShop);
		} else {
			self::$instance->setEntity($oShop);
		}

		return self::$instance;
	}

	/**
	 * Shop_Controller_Show_Last_Items::loadLastItems() - заполняет массив последних просмотренных товаров значениями из куки
	 * 
	 * @return void
	 */
	private function loadLastItems()
	{
		// загрузим массив просмотренных элементов из куки
        $this->_aShop_Last_Items = Core_Type_Conversion::toArray(@unserialize(Core_Array::get($_COOKIE, $this->_cookie_name, null)));
        $this->_aShop_Last_Items = array_unique($this->_aShop_Last_Items);
	}

	/**
	 * Shop_Controller_Show_Last_Items::saveLastItems() - сохраняет массив последних просмотренных товаров в куку
	 * 
	 * @return void
	 */
	private function saveLastItems()
	{
		// сохраним куку
		setcookie($this->_cookie_name, serialize($this->_aShop_Last_Items), time() + 31536000, '/');
	}

	/**
	 * deleteArrayItemByValue() - удаляет из массива элемент с переданным значением
	 * 
	 * @param mixed $array
	 * @param mixed $item_value
	 * @return void
	 */
	private function deleteArrayItemByValue(&$array = array(), $item_value = null)
	{
		if (sizeof($array) && !is_null($item_value))
		{
			$key = array_search($item_value, $array);

			// если такой товар в массиве посещений есть
			if ($key !== false)
			{
				// то исключим его из массива
				unset($array[$key]);
			}
		}
	}

	/**
	 * Shop_Controller_Show_Last_Items::xsl() - назначает XSL-шаблон для отображения
	 * 
	 * @param mixed $oXsl
	 * @return
	 */
	public function xsl(Xsl_Model $oXsl)
	{
		$this->_oShop_Controller_Show->xsl($oXsl);
		return $this;
	}

	/**
	 * Shop_Controller_Show_Last_Items::Shop_Controller_Show() - возвращает контроллер отображения магазина для задания дополнительных настроек
	 * 
	 * @return
	 */
	public function Shop_Controller_Show()
	{
		return $this->_oShop_Controller_Show;
	}

	/**
	 * Shop_Controller_Show_Last_Items::collectItem() - сохраняет в куку переданный товар магазина
	 * 
	 * @param mixed $oShop_Item
	 * @return void
	 */
	public function collectItem(Shop_Item_Model $oShop_Item)
	{
		// если в массиве уже есть id текущего товара
		if (in_array($oShop_Item->id, $this->_aShop_Last_Items))
		{

			// то вычислим его индекс
			$key = array_search($oShop_Item->id, $this->_aShop_Last_Items);

			// и удалим этот элемент массива,

			if ($key !== false)
			{
				unset($this->_aShop_Last_Items[$key]);
			}
		}
		// запишем id текущего товара в конец массива
		array_push($this->_aShop_Last_Items, $oShop_Item->id);

		$this->saveLastItems();
	}

	/**
	 * Shop_Controller_Show_Last_Items::showLastItems() - отображает последние просмотренные товары, в соответствии с заданными настройками
	 * 
	 * @return void
	 */
	public function showLastItems()
	{
		$oShop = $this->getEntity();

		// исключим из показа последних товаров те, что уже есть в корзине - начало
		if ($this->exclude_incart_items)
		{
			// получим содержимое корзины
			$oShop_Cart_Controller = Shop_Cart_Controller::instance();
			$aCart = $oShop_Cart_Controller->getAll($oShop);

			// запустим цикл по товарам лежащим в корзине
			foreach ($aCart as $oShop_Cart)
			{
				$oShop_Item = Core_Entity::factory('Shop_Item', $oShop_Cart->shop_item_id);

				$this->deleteArrayItemByValue($this->_aShop_Last_Items, 
					$oShop_Item->modification_id && $this->show_parent_items_instead_modifications ? 
						$oShop_Item->modification_id : $oShop_Item->id);
			}
		}
		// исключим из показа последних товаров те, что уже есть в корзине - конец

		// исключение текущего элемент, если мы находимся на странице товара - начало
		if (!$this->allow_show_current_item)
		{
			$oShop_Controller_Show = new Shop_Controller_Show($oShop);
			$oShop_Controller_Show->parseUrl();

			if ($oShop_Controller_Show->item)
			{
				$oShop_Item = Core_Entity::factory('Shop_Item', $oShop_Controller_Show->item);

				$this->deleteArrayItemByValue($this->_aShop_Last_Items, 
					$oShop_Item->modification_id && $this->show_parent_items_instead_modifications ?
						$oShop_Item->modification_id : $oShop_Item->id);
			}
		}
		// исключение текущего элемента, если мы находимся на странице товара - конец

		// замена модификаций на родительские товары - начало
		if ($this->show_parent_items_instead_modifications)
		{
			// реверсируем массив, чтобы сохранить правильный порядок товаров при замене
			// при случайной сортировке порядок просмотра товаров, по сути, не имеет значения
			if (!$this->is_random)
			{
				$this->_aShop_Last_Items = array_reverse($this->_aShop_Last_Items, true);
			}

			foreach ($this->_aShop_Last_Items as $key => $item_id)
			{
				$oShop_Item = Core_Entity::factory('Shop_Item', $item_id);

				// если это модификация
				if ($oShop_Item->modification_id)
				{
					// ищем, есть ли уже в массиве родительский товар
					$parent_item_key = array_search($oShop_Item->modification_id, $this->_aShop_Last_Items);

					if ($parent_item_key === false)
					{
						// если еще нет, то заменяем текущий элемент на родительский
						$this->_aShop_Last_Items[$key] = $oShop_Item->modification_id;

					} else
					{
						// а если уже есть, то удаляем текущий элемент
						unset($this->_aShop_Last_Items[$key]);
					}
				}
			}

			// реверсируем массив, чтобы восстановить порядок просмотра товаров
			// при случайной сортировке порядок просмотра товаров, по сути, не имеет значения
			if (!$this->is_random)
			{
				$this->_aShop_Last_Items = array_reverse($this->_aShop_Last_Items, true);
			}
		}
		// замена модификаций на родительские товары - конец

		// исключение товаров, которых нет в наличии - начало
		if ($this->exclude_out_of_rest_items)
		{
			foreach ($this->_aShop_Last_Items as $item_id)
			{
				$oShop_Warehouse_Item = Core_Entity::factory('Shop_Warehouse_Item')->getByShopItemId($item_id, $this->cache);

				if (!$oShop_Warehouse_Item || !($oShop_Warehouse_Item->count > 0))
				{
					$this->deleteArrayItemByValue($this->_aShop_Last_Items, $item_id);
				}
			}
		}
		// исключение товаров, которых нет в наличии - конец

		// и если после всего этого еще осталось что показывать :)
		if (sizeof($this->_aShop_Last_Items))
		{
			// если задан режим случайного показа
			if ($this->is_random)
			{
				// то включим случайную сортировку
				$this->_oShop_Controller_Show
					->shopItems()
						->queryBuilder()
							->clearOrderBy()
							->orderBy('RAND()');
			}
			// если задан показ в обратном хронологическом порядке
			else
			{
				// развернем массив историй посещения товаров задом наперед,
				// и обрежем его, оставив только то количество элементов, которое необходимо показать
				$this->_aShop_Last_Items = array_slice(array_reverse($this->_aShop_Last_Items), 0, $this->limit);

				// сформируем фрагмент XML, для описания порядка следования элементов в массиве истории посещения
				$visit_order_xml = Core::factory('Core_Xml_Entity')->name('visit_order');

				foreach ($this->_aShop_Last_Items as $item_id)
				{
					$visit_order_xml->addEntity(Core::factory('Core_Xml_Entity')->name('last_item')->value($item_id));
				}

				$this->_oShop_Controller_Show->addEntity($visit_order_xml);
			}

			// создадим фильтр по основному свойству id товара.
			// с помощью этого фильтра мы укажем контроллеру
			// какие товары мы хотим вывести
			// начало фильтра
			$this->_oShop_Controller_Show
				->shopItems()
					->queryBuilder()
						->where('shop_items.id', 'IN', $this->_aShop_Last_Items)
						->setAnd();
			// конец фильтра

			$this->_oShop_Controller_Show			
				->cache($this->cache)
				->limit($this->limit)
				->show();
		}
	}
}
