<?php

$oShop = Core_Entity::factory('Shop', Core_Array::get(Core_Page::instance()->libParams, 'shopId'));

class My_Shop_Controller_Show extends Shop_Controller_Show
{
	protected function _groupCondition()
	{
		/*$this->_Shop_Items
		->queryBuilder()
		->where('shop_items.shop_group_id', '=', intval($this->group));
		*/

		if ($this->group)
		{
			$this
			->shopItems()
			->queryBuilder()
			->join('shop_groups', 'shop_groups.id', '=', 'shop_items.shop_group_id', array(
				array('AND' => array('(')),
				array('' => array('shop_groups.parent_id', '=', $this->group)),
				array('OR' => array('shop_groups.id', '=', $this->group)),
				array('' => array(')'))
				)
			);
		}
		else
		{
			
			/*$this
					->shopItems()
					->queryBuilder()
					->where('shop_items.shop_group_id', '=', 0);*/
			
			// Отключаем выбор модификаций
			//!$this->_selectModifications && $this->forbidSelectModifications();
			$get_keys = array_keys($_GET);
			$is_filter = false;
			foreach($get_keys as $k) {
				if(mb_stripos($k, 'property_') !== false) {
					$is_filter = true;
				}
			}
			$this->addEntity( 
				Core::factory('Core_Xml_Entity')->name('is_filter')->value($is_filter)
			);
			
			$this->shopItems()
					->queryBuilder()
					->open();

			if ($this->group)
			{
					// если ID группы не 0, т.е. не корневая группа
					// получаем подгруппы
					$aSubGroupsID = $this->fillShopGroup($oShop->id, $this->group); // добавляем текущую группу в массив
					$aSubGroupsID[] = $this->group;

					$this->shopItems()
							->queryBuilder()
							// получаем все товары из подгрупп
							->where('shop_items.shop_group_id', 'IN', $aSubGroupsID);
			}
			else
			{
					$this->shopItems()
							->queryBuilder()
							->where('shop_items.modification_id', '=', 0);

					/*$this->shopItems()
							->queryBuilder()
							->where('shop_items.shop_group_id', '=', 0)
							;*/
			}

			$shop_group_id = !$this->parentItem
					? intval($this->group)
					: 0;

			// Вывод модификаций на одном уровне в списке товаров
			if (!$this->item && $this->modificationsList)
			{
					$oCore_QueryBuilder_Select_Modifications = Core_QueryBuilder::select('shop_items.id')
							->from('shop_items')
							->where('shop_items.shop_id', '=', $oShop->id)
							->where('shop_items.deleted', '=', 0)
							->where('shop_items.active', '=', 1);

					if ($this->group)
					{
							$oCore_QueryBuilder_Select_Modifications
									->where('shop_items.shop_group_id', 'IN', $aSubGroupsID); // получаем все товары из подгрупп
					}

					// Стандартные ограничения для товаров
					$this->_applyItemConditionsQueryBuilder($oCore_QueryBuilder_Select_Modifications);

					Core_Event::notify(get_class($this) . '.onBeforeSelectModifications', $this, array($oCore_QueryBuilder_Select_Modifications));

					$this->_Shop_Items
							->queryBuilder()
							->setOr()
							->where('shop_items.shop_group_id', '=', 0)
							->where('shop_items.modification_id', 'IN', $oCore_QueryBuilder_Select_Modifications);

					// Совместное modificationsList + filterShortcuts
					if ($this->filterShortcuts)
					{
							$oCore_QueryBuilder_Select_Shortcuts_For_Modifications = Core_QueryBuilder::select('shop_items.shortcut_id')
									->from('shop_items')
									->where('shop_items.shop_id', '=', $oShop->id)
									->where('shop_items.deleted', '=', 0)
									->where('shop_items.active', '=', 1)
									->where('shop_items.shop_group_id', '=', $shop_group_id)
									->where('shop_items.shortcut_id', '>', 0);

							$this->_Shop_Items
									->queryBuilder()
									->setOr()
									->where('shop_items.shop_group_id', '=', 0)
									->where('shop_items.modification_id', 'IN', $oCore_QueryBuilder_Select_Shortcuts_For_Modifications);
					}
			}

			if ($this->filterShortcuts)
			{
					$oCore_QueryBuilder_Select_Shortcuts = Core_QueryBuilder::select('shop_items.shortcut_id')
							->from('shop_items')
							->where('shop_items.deleted', '=', 0)
							->where('shop_items.active', '=', 1)
							->where('shop_items.shop_group_id', '=', $shop_group_id)
							->where('shop_items.shortcut_id', '>', 0);

					// Стандартные ограничения для товаров
					$this->_applyItemConditionsQueryBuilder($oCore_QueryBuilder_Select_Shortcuts);

					$this->_Shop_Items
							->queryBuilder()
							->setOr()
							->where('shop_items.id', 'IN', $oCore_QueryBuilder_Select_Shortcuts);
			}

			$this->_Shop_Items
					->queryBuilder()
					->close();
		}

		return $this;
	}
}

//$Shop_Controller_Show = new Shop_Controller_Show($oShop);
$Shop_Controller_Show = new My_Shop_Controller_Show($oShop);

/* Количество */
$on_page = intval(Core_Array::getGet('on_page'));
if ($on_page > 0 && $on_page < 150)
{
	$limit = $on_page;

	$Shop_Controller_Show->addEntity(
		Core::factory('Core_Xml_Entity')
			->name('on_page')->value($on_page)
	);
}
else
{
	$limit = $oShop->items_on_page;
}

$Shop_Controller_Show
	->limit($limit)
	->parseUrl();

// Обработка скачивания файла электронного товара
$guid = Core_Array::getGet('download_file');
if (strlen($guid))
{
	$oShop_Order_Item_Digital = Core_Entity::factory('Shop_Order_Item_Digital')->getByGuid($guid);

	if (!is_null($oShop_Order_Item_Digital) && $oShop_Order_Item_Digital->Shop_Order_Item->Shop_Order->shop_id == $oShop->id)
	{
		$iDay = 7;

		// Проверяем, доступна ли ссылка (Ссылка доступна в течение недели после оплаты)
		if (Core_Date::sql2timestamp($oShop_Order_Item_Digital->Shop_Order_Item->Shop_Order->payment_datetime) > time() - 24 * 60 * 60 * $iDay)
		{
			$oShop_Item_Digital = $oShop_Order_Item_Digital->Shop_Item_Digital;
			if ($oShop_Item_Digital->filename != '')
			{
				Core_File::download($oShop_Item_Digital->getFullFilePath(), $oShop_Item_Digital->filename);
				exit();
			}
		}
		else
		{
			Core_Message::show(Core::_('Shop_Order_Item_Digital.time_is_up', $iDay));
		}
	}

	Core_Page::instance()->response->status(404)->sendHeaders()->showBody();
	exit();
}

// Сравнение товаров
if (Core_Array::getRequest('compare'))
{
	$shop_item_id = intval(Core_Array::getRequest('compare'));

	if (Core_Entity::factory('Shop_Item', $shop_item_id)->shop_id == $oShop->id)
	{
		Core_Session::start();
		if (isset($_SESSION['hostcmsCompare'][$oShop->id][$shop_item_id]))
		{
			unset($_SESSION['hostcmsCompare'][$oShop->id][$shop_item_id]);
		}
		else
		{
			$_SESSION['hostcmsCompare'][$oShop->id][$shop_item_id] = 1;
		}
	}

	Core_Page::instance()->response
		->status(200)
		->header('Pragma', "no-cache")
		->header('Cache-Control', "private, no-cache")
		->header('Vary', "Accept")
		->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')
		->header('X-Powered-By', 'HostCMS')
		->header('Content-Disposition', 'inline; filename="files.json"');

	Core_Page::instance()->response
		->body(json_encode('OK'))
		->header('Content-type', 'application/json; charset=utf-8');

	Core_Page::instance()->response
		->sendHeaders()
		->showBody();

	exit();
}

// Избранное
if (Core_Array::getRequest('favorite'))
{
	$shop_item_id = intval(Core_Array::getRequest('favorite'));

	if (Core_Entity::factory('Shop_Item', $shop_item_id)->shop_id == $oShop->id)
	{
		Core_Session::start();
		if (isset($_SESSION['hostcmsFavorite'][$oShop->id]) && in_array($shop_item_id, $_SESSION['hostcmsFavorite'][$oShop->id]))
		{
			unset($_SESSION['hostcmsFavorite'][$oShop->id][
				array_search($shop_item_id, $_SESSION['hostcmsFavorite'][$oShop->id])
			]);
		}
		else
		{
			$_SESSION['hostcmsFavorite'][$oShop->id][] = $shop_item_id;
		}
	}

	Core_Page::instance()->response
		->status(200)
		->header('Pragma', "no-cache")
		->header('Cache-Control', "private, no-cache")
		->header('Vary', "Accept")
		->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')
		->header('X-Powered-By', 'HostCMS')
		->header('Content-Disposition', 'inline; filename="files.json"');

	Core_Page::instance()->response
		->body(json_encode('OK'))
		->header('Content-type', 'application/json; charset=utf-8');

	Core_Page::instance()->response
		->sendHeaders()
		->showBody();

	exit();
}

// Viewed items
if ($Shop_Controller_Show->item)
{
	$view_item_id = $Shop_Controller_Show->item;

	if (Core_Entity::factory('Shop_Item', $view_item_id)->shop_id == $oShop->id)
	{
		Core_Session::start();

		// Добавляем если такой товар еще не был просмотрен
		if (!isset($_SESSION['hostcmsViewed'][$oShop->id]) || !in_array($view_item_id, $_SESSION['hostcmsViewed'][$oShop->id]))
		{
			$_SESSION['hostcmsViewed'][$oShop->id][] = $view_item_id;
		}
	}
}

if (!is_null(Core_Array::getGet('vote')))
{
	$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();
	$entity_id = intval(Core_Array::getGet('id'));

	if ($entity_id && !is_null($oSiteuser))
	{
		$entity_type = strval(Core_Array::getGet('entity_type'));
		$vote = intval(Core_Array::getGet('vote'));

		$oObject = Vote_Controller::instance()->getVotedObject($entity_type, $entity_id);

		if (!is_null($oObject))
		{
			$oVote = $oObject->Votes->getBySiteuser_Id($oSiteuser->id);

			$vote_value = $vote ? 1 : -1;

			$deleteVote = 0;
			// Пользователь не голосовал ранее
			if (is_null($oVote))
			{
				$oVote = Core_Entity::factory('Vote');
				$oVote->siteuser_id = $oSiteuser->id;
				$oVote->value = $vote_value;

				$oObject->add($oVote);
			}
			// Пользователь голосовал ранее, но поставил противоположную оценку
			elseif ($oVote->value != $vote_value)
			{
				$oVote->value = $vote_value;
				$oVote->save();
			}
			// Пользователь голосовал ранее и поставил такую же оценку как и ранее, обнуляем его голосование, как будто он вообще не голосовал
			else
			{
				$deleteVote = 1;
				$oVote->delete();
			}

			$aVotingStatistic = Vote_Controller::instance()->getRate($entity_type, $entity_id);

			Core_Page::instance()->response
			->body(
				json_encode(array('value' => $oVote->value, 'item' => $oObject->id, 'entity_type' => $entity_type,
					'likes' => $aVotingStatistic['likes'], 'dislikes' => $aVotingStatistic['dislikes'],
					'rate' => $aVotingStatistic['rate'], 'delete_vote' => $deleteVote)
				)
			);
		}
	}

	Core_Page::instance()->response
			->status(200)
			->header('Pragma', "no-cache")
			->header('Cache-Control', "private, no-cache")
			->header('Vary', "Accept")
			->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')
			->header('X-Powered-By', 'HostCMS')
			->header('Content-Disposition', 'inline; filename="files.json"');

	if (strpos(Core_Array::get($_SERVER, 'HTTP_ACCEPT', ''), 'application/json') !== FALSE)
	{
		Core_Page::instance()->response->header('Content-type', 'application/json; charset=utf-8');
	}
	else
	{
		Core_Page::instance()->response
			->header('X-Content-Type-Options', 'nosniff')
			->header('Content-type', 'text/plain; charset=utf-8');
	}

	if(Core_Array::getRequest('_'))
	{
		Core_Page::instance()->response
			->sendHeaders()
			->showBody();
		exit();
	}
}

// Текстовая информация для указания номера страницы, например "страница"
$pageName = Core_Array::get(Core_Page::instance()->libParams, 'page')
	? Core_Array::get(Core_Page::instance()->libParams, 'page')
	: 'страница';

// Разделитель в заголовке страницы
$pageSeparator = Core_Array::get(Core_Page::instance()->libParams, 'separator')
	? Core_Page::instance()->libParams['separator']
	: ' / ';

$aTitle = array($oShop->name);
$aDescription = array($oShop->name);
$aKeywords = array($oShop->name);

if (!is_null($Shop_Controller_Show->tag) && Core::moduleIsActive('tag'))
{
	$oTag = Core_Entity::factory('Tag')->getByPath($Shop_Controller_Show->tag);
	if ($oTag)
	{
		$aTitle[] = $oTag->seo_title != '' ? $oTag->seo_title : Core::_('Shop.tag', $oTag->name);
		$aDescription[] = $oTag->seo_description != '' ? $oTag->seo_description : $oTag->name;
		$aKeywords[] = $oTag->seo_keywords != '' ? $oTag->seo_keywords : $oTag->name;
	}
}

if ($Shop_Controller_Show->group)
{
	$oShop_Group = Core_Entity::factory('Shop_Group', $Shop_Controller_Show->group);
	
	/*do {
		$aTitle[] = $oShop_Group->seo_title != ''
			? $oShop_Group->seo_title
			: $oShop_Group->name;

		$aDescription[] = $oShop_Group->seo_description != ''
			? $oShop_Group->seo_description
			: $oShop_Group->name;

		$aKeywords[] = $oShop_Group->seo_keywords != ''
			? $oShop_Group->seo_keywords
			: $oShop_Group->name;

	} while($oShop_Group = $oShop_Group->getParent());*/
	
	$aTitleGroup = array();
	$aDescriptionGroup = array();
	$aKeywordsGroup = array();
	
	do {
		$aTitleGroup[] = $oShop_Group->seo_title != ''
			? $oShop_Group->seo_title
			: $oShop_Group->name;

		$aDescriptionGroup[] = $oShop_Group->seo_description != ''
			? $oShop_Group->seo_description
			: $oShop_Group->name;

		$aKeywordsGroup[] = $oShop_Group->seo_keywords != ''
			? $oShop_Group->seo_keywords
			: $oShop_Group->name;

	} while($oShop_Group = $oShop_Group->getParent());
	
	$aTitleGroup = array_reverse($aTitleGroup);
	$aDescriptionGroup = array_reverse($aDescriptionGroup);
	$aKeywordsGroup = array_reverse($aKeywordsGroup);
	
	foreach ($aTitleGroup as &$value) {
		$aTitle[] = $value;
	}
	
	foreach ($aDescriptionGroup as &$value) {
		$aDescription[] = $value;
	}
	
	foreach ($aKeywordsGroup as &$value) {
		$aKeywords[] = $value;
	}
}

if ($Shop_Controller_Show->item)
{
	$oShop_Item = Core_Entity::factory('Shop_Item', $Shop_Controller_Show->item);

	$aTitle[] = $oShop_Item->seo_title != ''
		? $oShop_Item->seo_title
		: $oShop_Item->name;

	$aDescription[] = $oShop_Item->seo_description != ''
		? $oShop_Item->seo_description
		: $oShop_Item->name;

	$aKeywords[] = $oShop_Item->seo_keywords != ''
		? $oShop_Item->seo_keywords
		: $oShop_Item->name;
}

if ($Shop_Controller_Show->producer)
{
	$oShop_Producer = Core_Entity::factory('Shop_Producer', $Shop_Controller_Show->producer);
	$aKeywords[] = $aDescription[] = $aTitle[] = $oShop_Producer->name;
}

if ($Shop_Controller_Show->page)
{
	array_unshift($aTitle, $pageName . ' ' . ($Shop_Controller_Show->page + 1));
}


if (count($aTitle) > 1)
{
	$aTitle = array_reverse($aTitle);
	$aDescription = array_reverse($aDescription);
	$aKeywords = array_reverse($aKeywords);

	//Core_Page::instance()->title(implode($pageSeparator, $aTitle));
	//Core_Page::instance()->description(implode($pageSeparator, $aDescription));
	//Core_Page::instance()->keywords(implode($pageSeparator, $aKeywords));
	
	Core_Page::instance()->title($aTitle[0]);
	Core_Page::instance()->description($aDescription[0]);
	Core_Page::instance()->keywords($aKeywords[0]);
}

/** Последние просмотренные товары: сбор информации - начало **/
if ($Shop_Controller_Show->item)
{
	$oShop_Item = Core_Entity::factory('Shop_Item', $Shop_Controller_Show->item);
	$oShop_Controller_Show_Last_Items = new Shop_Controller_Show_Last_Items($oShop);
	$oShop_Controller_Show_Last_Items->collectItem($oShop_Item);
}
/** Последние просмотренные товары: сбор информации - конец **/

Core_Page::instance()->object = $Shop_Controller_Show;