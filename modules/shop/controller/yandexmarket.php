<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Экспорт в Yandex.Market для магазина.
 *
 * Доступные методы:
 *
 * - itemsProperties(TRUE|FALSE|array()) выводить значения дополнительных свойств товаров, по умолчанию TRUE.
 * - modifications(TRUE|FALSE) экспортировать модификации, по умолчанию TRUE.
 * - type('offer'|'vendor.model'|'book'|'audiobook'|'artist.title'|'tour'|'event-ticket') тип товара, по умолчанию 'offer'
 * - onStep(100) количество товаров, выбираемых запросом за 1 шаг, по умолчанию 100
 *
 * <code>
 * $Shop_Controller_YandexMarket = new Shop_Controller_YandexMarket(
 * 	Core_Entity::factory('Shop', 1)
 * );
 *
 * $Shop_Controller_YandexMarket->show();
 * </code>
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Controller_YandexMarket extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'itemsProperties',
		'modifications',
		'type',
		'onStep'
	);

	/**
	 * Shop's items object
	 * @var Shop_Item_Model
	 */
	protected $_Shop_Items = NULL;

	/**
	 * Shop's groups object
	 * @var Shop_Group_Model
	 */
	protected $_Shop_Groups = NULL;

	/**
	 * Array of siteuser's groups allowed for current siteuser
	 * @var array
	 */
	protected $_aSiteuserGroups = array();

	/**
	 * List's offer tags
	 * @var array
	 */
	public $aOfferTags = array(
		'adult' => 'adult',
		'cpa' => 'cpa',
		'age-year' => 'age-year',
		'age-month' => 'age-month',
		'barcode' => 'barcode',
	);

	/**
	 * List's vendor tags
	 * @var array
	 */
	public $aVendorTags = array(
		'typePrefix' => 'typePrefix',
		'model' => 'model',
		'adult' => 'adult',
		'cpa' => 'cpa',
		'rec' => 'rec',
		'expiry' => 'expiry',
		'weight' => 'weight',
		'dimensions' => 'dimensions',
		'age-year' => 'age-year',
		'age-month' => 'age-month',
	);

	/**
	 * List's book tags
	 * @var array
	 */
	public $aBookTags = array(
		//http://help.yandex.ru/partnermarket/offers.xml#book
		'author' => 'author',
		'publisher' => 'publisher',
		'series' => 'series',
		'year' => 'year',
		'ISBN' => 'ISBN',
		'volume' => 'volume',
		'part' => 'part',
		'language' => 'language',
		'binding' => 'binding',
		'page_extent' => 'page_extent',
		'table_of_contents' => 'table_of_contents',
		'age-year' => 'age-year',
		'age-month' => 'age-month',
	);

	/**
	 * List's audiobook tags
	 * @var array
	 */
	public $aAudiobookTags = array(
		//http://help.yandex.ru/partnermarket/offers.xml#audiobook
		'author' => 'author',
		'publisher' => 'publisher',
		'series' => 'series',
		'year' => 'year',
		'ISBN' => 'ISBN',
		'volume' => 'volume',
		'part' => 'part',
		'language' => 'language',
		'table_of_contents' => 'table_of_contents',
		'performed_by' => 'performed_by',
		'performance_type' => 'performance_type',
		'storage' => 'storage',
		'format' => 'format', //Время звучания задается в формате mm.ss (минуты.секунды).
		'recording_length' => 'recording_length',
		'age-year' => 'age-year',
		'age-month' => 'age-month',
	);

	/**
	 * List's artist.title tags
	 * @var array
	 */
	public $aArtistTitleTags = array(
		'artist' => 'artist',
		'title' => 'title',
		'year' => 'year',
		'media' => 'media',
		'starring' => 'starring',
		'director' => 'director',
		'originalName' => 'originalName',
		'country' => 'country',
		'adult' => 'adult',
		'age-year' => 'age-year',
		'age-month' => 'age-month',
		'barcode' => 'barcode',
	);

	/**
	 * List's tour tags
	 * @var array
	 */
	public $aTourTags = array(
		'worldRegion' => 'worldRegion',
		'country' => 'country',
		'region' => 'region',
		'days' => 'days',
		'dataTour' => 'dataTour', //Даты заездов. Предпочтительный формат: YYYY-MM-DD hh:mm:ss.
		'hotel_stars' => 'hotel_stars',
		'room' => 'room',
		'meal' => 'meal',
		'included' => 'included',
		'transport' => 'transport',
		'price_min' => 'price_min',
		'price_max' => 'price_max',
		'options' => 'options',
		'age-year' => 'age-year',
		'age-month' => 'age-month',
	);

	/**
	 * List's event ticket tags
	 * @var array
	 */
	public $aEventTicketTags = array(
		'place' => 'place',
		'hall' => 'hall',
		'hall_part' => 'hall_part',
		'date' => 'date', //Дата и время сеанса. Предпочтительный формат: YYYY-MM-DD hh:mm:ss.
		'is_premiere' => 'is_premiere',
		'is_kids' => 'is_kids',
		'age-year' => 'age-year',
		'age-month' => 'age-month',
	);

	/**
	 * Shop_Item_Controller
	 * @var Shop_Item_Controller
	 */
	protected $_Shop_Item_Controller = NULL;
	
	/**
	 * Constructor.
	 * @param Shop_Model $oShop shop
	 */
	public function __construct(Shop_Model $oShop)
	{
		parent::__construct($oShop->clearEntities());

		$this->_Shop_Items = $oShop->Shop_Items;

		$siteuser_id = 0;

		$this->_aSiteuserGroups = array(0, -1);
		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$siteuser_id = $oSiteuser->id;

				$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll(FALSE);
				foreach ($aSiteuser_Groups as $oSiteuser_Group)
				{
					$this->_aSiteuserGroups[] = $oSiteuser_Group->id;
				}
			}
		}

		switch ($oShop->items_sorting_direction)
		{
			case 1:
				$items_sorting_direction = 'DESC';
			break;
			case 0:
			default:
				$items_sorting_direction = 'ASC';
		}

		// Определяем поле сортировки информационных элементов
		switch ($oShop->items_sorting_field)
		{
			case 1:
				$this->_Shop_Items
					->queryBuilder()
					->orderBy('shop_items.name', $items_sorting_direction);
				break;
			case 2:
				$this->_Shop_Items
					->queryBuilder()
					->orderBy('shop_items.sorting', $items_sorting_direction)
					->orderBy('shop_items.name', $items_sorting_direction);
				break;
			case 0:
			default:
				$this->_Shop_Items
					->queryBuilder()
					->orderBy('shop_items.datetime', $items_sorting_direction);
		}

		$dateTime = Core_Date::timestamp2sql(time());
		$this->_Shop_Items
			->queryBuilder()
			->select('shop_items.*')
			->leftJoin('shop_groups', 'shop_groups.id', '=', 'shop_items.shop_group_id'/*,
				array(
						array('AND' => array('shop_groups.active', '=', 1)),
						array('OR' => array('shop_items.shop_group_id', '=', 0))
					)*/
			)

			// Активность группы или группа корневая
			->open()
			->where('shop_groups.active', '=', 1)
			->setOr()
			->where('shop_groups.id', 'IS', NULL)
			->close()

			->where('shop_items.shortcut_id', '=', 0)
			->where('shop_items.active', '=', 1)
			->where('shop_items.siteuser_id', 'IN', $this->_aSiteuserGroups)
			->open()
			->where('shop_items.start_datetime', '<', $dateTime)
			->setOr()
			->where('shop_items.start_datetime', '=', '0000-00-00 00:00:00')
			->close()
			->setAnd()
			->open()
			->where('shop_items.end_datetime', '>', $dateTime)
			->setOr()
			->where('shop_items.end_datetime', '=', '0000-00-00 00:00:00')
			->close()
			->where('shop_items.yandex_market', '=', 1)
			->where('shop_items.price', '>', 0)
			->where('shop_items.modification_id', '=', 0);

		$this->_Shop_Groups = $oShop->Shop_Groups;
		$this->_Shop_Groups
			->queryBuilder()
			->where('shop_groups.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
			->where('shop_groups.active', '=', 1)
			->orderBy('shop_groups.parent_id');

		$this->itemsProperties = $this->modifications = TRUE;

		$this->type = 'offer';
		$this->onStep = 100;
		
		$this->_Shop_Item_Controller = new Shop_Item_Controller();
	}

	/**
	 * Get items set
	 * @return Shop_Item_Model
	 */
	public function shopItems()
	{
		return $this->_Shop_Items;
	}

	/**
	 * Get groups set
	 * @return Shop_Item_Model
	 */
	public function shopGroups()
	{
		return $this->_Shop_Groups;
	}

	/**
	 * Show currencies
	 * @return self
	 */
	protected function _currencies()
	{
		echo '<currencies>'. "\n";
		$aShop_Currencies = Core_Entity::factory('Shop_Currency')->findAll(FALSE);

		$aCurrenciesCodes = array(
			'RUR',
			'RUB',
			'USD',
			'BYR',
			'KZT',
			'EUR',
			'UAH',
		);

		foreach ($aShop_Currencies as $oShop_Currency)
		{
			if (trim($oShop_Currency->code) != ''
			&& in_array($oShop_Currency->code, $aCurrenciesCodes))
			{
				echo '<currency id="' . Core_Str::xml($oShop_Currency->code) .
					'" rate="' . Core_Str::xml($oShop_Currency->exchange_rate) .'"'. "/>\n";
			}
		}
		echo '</currencies>'. "\n";

		return $this;
	}

	/**
	 * Show categories
	 * @return self
	 */
	protected function _categories()
	{
		$aShop_Groups = $this->_Shop_Groups->findAll(FALSE);

		echo "<categories>\n";

		// Название магазина
		$oShop = $this->getEntity();

		echo '<category id="0">' . Core_Str::xml(!empty($oShop->yandex_market_name) ? $oShop->yandex_market_name : $oShop->Site->name) . "</category>\n";

		foreach ($aShop_Groups as $oShop_Group)
		{
			$group_parent_id = $oShop_Group->parent_id == '' || $oShop_Group->parent_id == 0 ? '' : ' parentId="' . $oShop_Group->parent_id . '"';

			echo '<category id="' . $oShop_Group->id . '"' . $group_parent_id . '>' . Core_Str::xml($oShop_Group->name) . "</category>\n";
		}
		echo "</categories>\n";

		unset($aShop_Groups);

		return $this;
	}

	/**
	 * Property_Model for <market_category>
	 * @var mixed
	 */
	protected $_MarketCategory = NULL;
	
	/**
	 * Show offers
	 * @return self
	 * @hostcms-event Shop_Controller_YandexMarket.onBeforeOffer
	 * @hostcms-event Shop_Controller_YandexMarket.onAfterOffer
	 */
	protected function _offers()
	{
		echo "<offers>\n";

		$offset = 0;
		$limit = $this->onStep;

		$oShop = $this->getEntity();
		$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $oShop->id);
		
		$this->_MarketCategory = $oShop_Item_Property_List->Properties->getByTag_name('market_category');
		
		do {
			$oShop_Items = $this->_Shop_Items;
			$oShop_Items->queryBuilder()
				->offset($offset)
				->limit($limit);

			$aShop_Items = $oShop_Items->findAll(FALSE);

			foreach ($aShop_Items as $oShop_Item)
			{
				$this->_showOffer($oShop_Item);

				if ($this->modifications)
				{
					$aModifications = $oShop_Item->Modifications->findAll(FALSE);

					foreach ($aModifications as $oModification)
					{
						$this->_showOffer($oModification);
					}
				}
			}

			Core_File::flush();
			$offset += $limit;
		}
		while (count($aShop_Items));

		echo '</offers>'. "\n";

		return $this;
	}

	protected function _showOffer($oShop_Item)
	{
		$oShop = $this->getEntity();

		/* Устанавливаем атрибуты тега <offer>*/
		$tag_bid = $oShop_Item->yandex_market_bid
			? ' bid="' . Core_Str::xml($oShop_Item->yandex_market_bid) . '"'
			: '';

		$tag_cbid = $oShop_Item->yandex_market_cid
			? ' cbid="' . Core_Str::xml($oShop_Item->yandex_market_cid) . '"'
			: '';

		$oShop_Warehouse_Item = $oShop_Item->Shop_Warehouse_Items->getByShopItemId($oShop_Item->id, FALSE);
		$available = !is_null($oShop_Warehouse_Item) && $oShop_Warehouse_Item->count > 0 ? 'true' : 'false';

		$sType = $this->type != 'offer'
			? ' type="' . Core_Str::xml($this->type) . '"'
			: '';

		echo '<offer id="' . $oShop_Item->id . '"'. $tag_bid . $tag_cbid . $sType . " available=\"{$available}\">\n";

		Core_Event::notify(get_class($this) . '.onBeforeOffer', $this, array($oShop_Item));

		/* URL */
		echo '<url>' . Core_Str::xml($this->_shopPath . $oShop_Item->getPath()) . '</url>'. "\n";

		/* Определяем цену со скидкой */
		$aPrices = $this->_Shop_Item_Controller->calculatePrice($oShop_Item->price, $oShop_Item);
		
		/* Цена */
		echo '<price>' . $aPrices['price_discount'] . '</price>'. "\n";		
		
		if ($aPrices['price'] > $aPrices['price_discount'])
		{
			/* Старая цена */
			echo '<oldprice>' . $aPrices['price'] . '</oldprice>'. "\n";
		}

		/* CURRENCY */
		// Обязательно поле в модели:
		// (url?,buyurl?,price,wprice?,currencyId,xCategory?,categoryId+ ...
		echo '<currencyId>'. Core_Str::xml($oShop_Item->Shop_Currency->code) . '</currencyId>'. "\n";

		/* Идентификатор категории */
		// Основной товар
		if ($oShop_Item->modification_id == 0)
		{
			$categoryId = $oShop_Item->shop_group_id;
		}
		else // Модификация, берем ID родительской группы
		{
			$categoryId = $oShop_Item->Modification->Shop_Group->id
				? $oShop_Item->Modification->Shop_Group->id
				: 0;
		}
		echo '<categoryId>' . $categoryId . '</categoryId>'. "\n";

		if (!is_null($this->_MarketCategory))
		{
			$aProperty_Value_Market_Category = $this->_MarketCategory->getValues($oShop_Item->id);
			if (isset($aProperty_Value_Market_Category[0]))
			{
				echo '<market_category>' .
					Core_Str::xml($aProperty_Value_Market_Category[0]->velue) .
				'</market_category>'. "\n";
			}
		}
		
		/* PICTURE */
		if ($oShop_Item->image_large != '')
		{
			echo '<picture>' . 'http://' . Core_Str::xml($this->_siteAlias->name . $oShop_Item->getLargeFileHref()) . '</picture>'. "\n";
		}

		// (name, vendor?, vendorCode?)
		if (mb_strlen($oShop_Item->name) > 0)
		{
			/* NAME */
			echo '<name>' . Core_Str::xml($oShop_Item->name) . '</name>'. "\n";

			if ($oShop_Item->Shop_Producer->id)
			{
				echo '<vendor>' . Core_Str::xml($oShop_Item->Shop_Producer->name) . '</vendor>'. "\n";
			}

			if ($oShop_Item->vendorcode != '')
			{
				echo '<vendorCode>' . Core_Str::xml($oShop_Item->vendorcode) . '</vendorCode>'. "\n";
			}
		}

		/* DESCRIPTION */
		if (!empty($oShop_Item->description))
		{
			echo '<description>' . Core_Str::xml(html_entity_decode(strip_tags($oShop_Item->description), ENT_COMPAT, 'UTF-8')) . '</description>'. "\n";
		}

		/* sales_notes */
		$sales_notes = mb_strlen($oShop_Item->yandex_market_sales_notes) > 0
			? $oShop_Item->yandex_market_sales_notes
			: $oShop->yandex_market_sales_notes_default;

		echo '<sales_notes>' . Core_Str::xml(html_entity_decode(strip_tags($sales_notes), ENT_COMPAT, 'UTF-8')) . '</sales_notes>'. "\n";

		if ($oShop_Item->manufacturer_warranty)
		{
			echo '<manufacturer_warranty>true</manufacturer_warranty>'. "\n";
		}

		if (trim($oShop_Item->country_of_origin) != '')
		{
			echo '<country_of_origin>' . Core_Str::xml(html_entity_decode(strip_tags($oShop_Item->country_of_origin), ENT_COMPAT, 'UTF-8')) . '</country_of_origin>'. "\n";
		}

		// Элемент предназначен для обозначения товара, который можно скачать. Если указано значение параметра true, товарное предложение показывается во всех регионах независимо от регионов доставки, указанных магазином на странице Параметры размещения.
		if ($oShop_Item->type == 1)
		{
			echo '<downloadable>true</downloadable>'. "\n";
		}

		$this->itemsProperties && $this->_addPropertyValue($oShop_Item);

		Core_Event::notify(get_class($this) . '.onAfterOffer', $this, array($oShop_Item));

		echo '</offer>'. "\n";
	}

	/**
	 * Print Shop_Item properties
	 * @param Shop_Item_Model $oShop_Item
	 * @return self
	 */
	protected function _addPropertyValue(Shop_Item_Model $oShop_Item)
	{
		// Доп. св-ва выводятся в <param>
		// <param name="Максимальный формат">А4</param>
		//$aProperty_Values = $oShop_Item->getPropertyValues(FALSE);

		if (is_array($this->itemsProperties))
		{
			$aProperty_Values = Property_Controller_Value::getPropertiesValues($this->itemsProperties, $oShop_Item->id, FALSE);
		}
		else
		{
			$aProperty_Values = $oShop_Item->getPropertyValues(FALSE);
		}

		foreach ($aProperty_Values as $oProperty_Value)
		{
			$oProperty = $oProperty_Value->Property;

			switch ($oProperty->type)
			{
				case 0: // Int
				case 1: // String
				case 4: // Textarea
				case 6: // Wysiwyg
				case 8: // Date
				case 9: // Datetime
					$value = $oProperty_Value->value;
				break;

				case 3: // List
					$value = NULL;

					$oList_Item = $oProperty->List->List_Items->getById(
						$oProperty_Value->value, FALSE
					);

					!is_null($oList_Item) && $value = $oList_Item->value;
				break;

				case 7: // Checkbox
					$value = $oProperty_Value->value == 1 ? 'есть' : NULL;
				break;

				case 2: // File
				case 5: // ИС
				case 10: // Hidden field
				default:
					$value = NULL;
				break;
			}

			if (!is_null($value))
			{
				$sTagName = 'param';

				$unit = $oProperty->type == 0 && $oProperty->Shop_Item_Property->Shop_Measure->id
					? ' unit="' . Core_Str::xml($oProperty->Shop_Item_Property->Shop_Measure->name) . '"'
					: '';

				$sAttr = ' name="' . Core_Str::xml($oProperty->name) . '"' . $unit;

				if ($this->type != 'offer')
				{
					switch ($this->type)
					{
						case 'vendor.model':
							$aTmpArray = $this->aVendorTags;
						break;
						case 'book':
							$aTmpArray = $this->aBookTags;
						break;
						case 'audiobook':
							$aTmpArray = $this->aAudiobookTags;
						break;
						case 'artist.title':
							$aTmpArray = $this->aArtistTitleTags;
						break;
						case 'tour':
							$aTmpArray = $this->aTourTags;
						break;
						case 'event-ticket':
							$aTmpArray = $this->aEventTicketTags;
						break;
						default:
							throw new Core_Exception("Wrong type '%type'",
								array('%type' => $this->type)
							);
					}

					if (isset($aTmpArray[$oProperty->tag_name]))
					{
						$sTagName = $aTmpArray[$oProperty->tag_name];
						$sAttr = '';
					}
				}

				echo '<' . $sTagName . $sAttr . '>' . Core_Str::xml(html_entity_decode(strip_tags($value), ENT_COMPAT, 'UTF-8')) . '</' . $sTagName . '>'. "\n";
			}
		}

		return $this;
	}

	/**
	 * Current site alias
	 * @var string
	 */
	protected $_siteAlias = NULL;

	/**
	 * Shop URL
	 * @var string
	 */
	protected $_shopPath = NULL;

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Shop_Controller_YandexMarket.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		$oShop = $this->getEntity();
		$oSite = $oShop->Site;

		Core_Page::instance()->response
			->header('Content-Type', "text/xml; charset={$oSite->coding}")
			->sendHeaders();

		echo '<?xml version="1.0" encoding="' . $oSite->coding . '"?>' . "\n";
		echo '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\n";
		echo '<yml_catalog date="' . date("Y-m-d H:i") . '">' . "\n";
		echo "<shop>\n";

		// Название магазина
		$shop_name = trim(
			!empty($oShop->yandex_market_name)
				? $oShop->yandex_market_name
				: $oSite->name
		);

		echo "<name>" . Core_Str::xml(mb_substr($shop_name, 0, 20)) . "</name>\n";

		// Название компании.
		echo "<company>" . Core_Str::xml($oShop->Shop_Company->name) . "</company>\n";

		$this->_siteAlias = $oSite->getCurrentAlias();
		$this->_shopPath = 'http://' . $this->_siteAlias->name . $oShop->Structure->getPath();

		echo "<url>" . Core_Str::xml($this->_shopPath) . "</url>\n";
		echo "<platform>HostCMS</platform>\n";
		echo "<version>" . Core_Str::xml(CURRENT_VERSION) . "</version>\n";

		/* Валюты */
		$this->_currencies();

		/* Категории */
		$this->_categories();

		Core_File::flush();

		/* Товары */
		$this->_offers();

		echo "</shop>\n";
		echo '</yml_catalog>';

		Core_File::flush();
	}
}