<?php
/**
* Online shop.
*
* @package HostCMS
* @version 6.x
* @author Hostmake LLC
* @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
*/
require_once('../../../../bootstrap.php');

Core_Auth::authorization($sModule = 'shop');

// Получаем параметры
$oShop = Core_Entity::factory('Shop', Core_Array::getGet('shop_id', 0));
$oShopDir = $oShop->Shop_Dir;
$oShopGroup = Core_Entity::factory('Shop_Group', Core_Array::getGet('shop_group_id', 0));

$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Entity_Breadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

// Контроллер формы
$oAdmin_Form_Controller->module(Core_Module::factory($sModule))->setUp()->path('/admin/shop/item/change/index.php');

ob_start();

$oAdmin_View = Admin_View::create();
$oAdmin_View
	->module(Core_Module::factory($sModule))
	->pageTitle(Core::_('Shop_Item.change_prices_for_shop_group'));

// Первая крошка на список магазинов
$oAdmin_Form_Entity_Breadcrumbs->add(
		Admin_Form_Entity::factory('Breadcrumb')
			->name(Core::_('Shop.menu'))
			->href($oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/index.php'))
			->onclick($oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/index.php'))
);

// Крошки по директориям магазинов
if ($oShopDir->id)
{
	$oShopDirBreadcrumbs = $oShopDir;

	$aBreadcrumbs = array();

	do
	{
		$aBreadcrumbs[] = Admin_Form_Entity::factory('Breadcrumb')
			->name($oShopDirBreadcrumbs->name)
			->href($oAdmin_Form_Controller->getAdminLoadHref(
					'/admin/shop/index.php', NULL, NULL, "shop_dir_id={$oShopDirBreadcrumbs->id}"
			))
			->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
					'/admin/shop/index.php', NULL, NULL, "shop_dir_id={$oShopDirBreadcrumbs->id}"
			)
		);
	}while($oShopDirBreadcrumbs = $oShopDirBreadcrumbs->getParent());

	$aBreadcrumbs = array_reverse($aBreadcrumbs);

	foreach ($aBreadcrumbs as $oBreadcrumb)
	{
		$oAdmin_Form_Entity_Breadcrumbs->add($oBreadcrumb);
	}
}

// Крошка на список товаров и групп товаров магазина
$oAdmin_Form_Entity_Breadcrumbs->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name($oShop->name)
		->href($oAdmin_Form_Controller->getAdminLoadHref(
						'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}"
		))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
						'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}"
		))
);

// Крошки по группам товаров
if ($oShopGroup->id)
{
	$oShopGroupBreadcrumbs = $oShopGroup;

	$aBreadcrumbs = array();

	do
	{
		$aBreadcrumbs[] = Admin_Form_Entity::factory('Breadcrumb')
			->name($oShopGroupBreadcrumbs->name)
			->href($oAdmin_Form_Controller->getAdminLoadHref(
					'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"
			))
			->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
					'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"
			)
		);
	}while($oShopGroupBreadcrumbs = $oShopGroupBreadcrumbs->getParent());

	$aBreadcrumbs = array_reverse($aBreadcrumbs);

	foreach ($aBreadcrumbs as $oBreadcrumb)
	{
		$oAdmin_Form_Entity_Breadcrumbs->add($oBreadcrumb);
	}
}

// Крошка на текущую форму
$oAdmin_Form_Entity_Breadcrumbs->add(
Admin_Form_Entity::factory('Breadcrumb')
	->name(Core::_('Shop_Item.change_prices_for_shop_group'))
	->href($oAdmin_Form_Controller->getAdminLoadHref(
		$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
	->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
		$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
);

$oAdmin_Form_Entity_Form = Admin_Form_Entity::factory('Form')
		->controller($oAdmin_Form_Controller)
		->action($oAdmin_Form_Controller->getPath());

$oAdmin_View->addChild($oAdmin_Form_Entity_Breadcrumbs);

$oMainTab = Admin_Form_Entity::factory('Tab')->name('main');
$oMainTab
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Radiogroup')
			->radio(array(0 => Core::_('Shop_Item.add_price_to_digit')))
			->caption(Core::_('Shop_Item.select_price_form'))
			->ico(array('fa-plus'))
			->name('type_of_change')
			->divAttr(array('class' => 'form-group col-xs-7 col-sm-4')))
		->add(Admin_Form_Entity::factory('Input')
			->name('increase_price_rate')
			->caption('&nbsp;')
			->value('0.00')
			->divAttr(array('class' => 'form-group col-xs-3 col-sm-2')))
		->add(Admin_Form_Entity::factory('Span')
			->value($oShop->Shop_Currency->name)
			->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2 col-xs-2', 'style' => 'margin-top: 35px')))
			)
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Radiogroup')
			->radio(array(1 => Core::_('Shop_Item.multiply_price_to_digit')))
			->name('type_of_change')
			->ico(array(1 => 'fa-asterisk'))
			->divAttr(array('class' => 'form-group col-xs-7 col-sm-4')))
		->add(Admin_Form_Entity::factory('Input')
			->name("multiply_price_rate")
			->divAttr(array('class' => 'form-group col-xs-3 col-sm-2'))
			->value('1.00'))
	)
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Checkbox')
			->name('flag_include_modifications')
			->caption(Core::_('Shop_Item.flag_include_modifications')))
	);

// Получение списка скидок
$aDiscounts = array(" … ");
$aShop_Discounts = $oShop->Shop_Discounts->findAll();
foreach($aShop_Discounts as $oShop_Discount)
{
	$aDiscounts[$oShop_Discount->id] = $oShop_Discount->name;
}

$Shop_Item_Controller_Edit = Admin_Form_Action_Controller::factory(
		'Shop_Item_Controller_Edit', Core_Entity::factory('Admin_Form', 65)->Admin_Form_Actions->getByName('edit')
);

$oMainTab
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Select')
			->options($aDiscounts)
			->caption(Core::_('Shop_Item.select_discount_type'))
			->name('shop_discount_id')
			->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12'))
			->filter(TRUE))
	)
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Checkbox')
			->name('flag_delete_discount')
			->caption(Core::_('Shop_Item.flag_delete_discount')))
	);

// Получение бонусов
if (Core::moduleIsActive('siteuser'))
{
	$aBonuses = array(" … ");
	$aShop_Bonuses = $oShop->Shop_Bonuses->findAll();
	foreach($aShop_Bonuses as $oShop_Bonus)
	{
		$aBonuses[$oShop_Bonus->id] = $oShop_Bonus->name;
	}

	$oMainTab
		->add(Admin_Form_Entity::factory('Div')->class('row')
			->add(Admin_Form_Entity::factory('Select')
				->options($aBonuses)
				->caption(Core::_('Shop_Item.select_bonus_type'))
				->name('shop_bonus_id')
				->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12'))
				->filter(TRUE))
	)
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Checkbox')
			->name('flag_delete_bonus')
			->caption(Core::_('Shop_Item.flag_delete_bonus')))
	);
}

$oMainTab
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Select')
			->name('shop_groups_parent_id')
			->caption(Core::_('Shop_Item.select_parent_group'))
			->options(array(Core::_('Shop_Item.load_parent_group')) + Shop_Item_Controller_Edit::fillShopGroup($oShop->id))
			->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12'))
			->filter(TRUE)
			->value($oShopGroup->id)))
	->add(Admin_Form_Entity::factory('Div')->class('row')
		->add(Admin_Form_Entity::factory('Select')
			->name('shop_producers_list_id')
			->caption(Core::_('Shop_Item.shop_producer_id'))
			->options($Shop_Item_Controller_Edit->fillProducersList($oShop->id))
			->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12'))
			->filter(TRUE)))
	;

$oAdmin_Form_Entity_Form
	->add($oMainTab)
	->add(
		Admin_Form_Entity::factory('Button')
		->name('do_accept_new_price')
		->type('submit')
		->class('applyButton btn btn-blue')
		->onclick($oAdmin_Form_Controller->getAdminSendForm('do_accept_new_price'))
	);

$oUser = Core_Entity::factory('User')->getCurrent();

if ($oAdmin_Form_Controller->getAction() == 'do_accept_new_price')
{
	if (!$oUser->read_only)
	{
		$oShopItems = Core_Entity::factory('Shop', $oShop->id)->Shop_Items;
		$oShopItems->queryBuilder()->where('modification_id', '=', 0);

		if ($iParentGroup = Core_Array::getPost('shop_groups_parent_id'))
		{
			$oShopItems->queryBuilder()->where('shop_group_id', 'IN', array_merge(array($iParentGroup), Core_Entity::factory('Shop_Group', $iParentGroup)->Shop_Groups->getGroupChildrenId()));
		}

		$iProducerID = Core_Array::getPost('shop_producers_list_id');
		$iProducerID && $oShopItems->queryBuilder()->where('shop_producer_id', '=', $iProducerID);

		$increase_price_rate = Core_Array::getPost('increase_price_rate');
		$multiply_price_rate = Core_Array::getPost('multiply_price_rate');
		$iDiscountID = Core_Array::getPost('shop_discount_id', 0);
		$iBonusID = Core_Array::getPost('shop_bonus_id', 0);

		// Step-by-step
		$offset = 0;
		$limit = 500;

		do {
			$oShopItems->queryBuilder()
				->offset($offset)
				->limit($limit);

			$aShopItems = $oShopItems->findAll(FALSE);

			foreach($aShopItems as $oShopItem)
			{
				applySettings($oUser, $oShopItem, $increase_price_rate, $multiply_price_rate, $iDiscountID, $iBonusID);

				if(!is_null(Core_Array::getPost('flag_include_modifications')))
				{
					$aShopItemModifications = $oShopItem->Modifications->findAll(FALSE);
					foreach($aShopItemModifications as $oShopItemModification)
					{
						applySettings($oUser, $oShopItemModification, $increase_price_rate, $multiply_price_rate, $iDiscountID, $iBonusID);
					}
				}
			}

			// Inc offset
			$offset += $limit;
		} while (count($aShopItems));

		Core_Message::show(Core::_('Shop_Item.accepted_prices'));
	}
	else
	{
		Core_Message::show(Core::_('User.demo_mode'), 'error');
	}
}

$oAdmin_Form_Entity_Form->execute();
$content = ob_get_clean();

ob_start();
$oAdmin_View
	->content($content)
	->show();

Core_Skin::instance()
	->answer()
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	->title(Core::_('Shop_Item.change_prices_for_shop_group'))
	->execute();

function applySettings(User_Model $oUser, Shop_Item_Model $oShopItem, $sTextAddition, $sTextMultiplication, $iDiscountID, $iBonusID)
{
	$oShop_Discount = Core_Entity::factory('Shop_Discount', $iDiscountID);

	$oShop_Bonus = Core_Entity::factory('Shop_Bonus', $iBonusID);

	// Проверка через user_id на право выполнения действия над объектом
	if ($oUser->checkObjectAccess($oShopItem))
	{
		if (Core_Array::getPost('type_of_change', 0) == 0)
		{
			if ($oShopItem->shop_currency_id != 0 && $oShopItem->Shop->shop_currency_id != 0)
			{
				$iCoefficient = Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
					$oShopItem->Shop->Shop_Currency, $oShopItem->Shop_Currency
				);
			}
			else
			{
				$iCoefficient = 0;
			}

			$oShopItem->price += $sTextAddition * $iCoefficient;
		}
		else
		{
			$oShopItem->price *= $sTextMultiplication;
		}

		$oShopItem->save();

		if ($iDiscountID)
		{
			if (!is_null(Core_Array::getPost('flag_delete_discount')))
			{
				$oShopItem->remove($oShop_Discount);
			}
			else
			{
				$bIsNull = is_null($oShopItem->Shop_Item_Discounts->getByDiscountId($iDiscountID));

				if ($bIsNull)
				{
					// Устанавливаем скидку товару
					$oShopItem->add($oShop_Discount);
				}
			}
		}

		if ($iBonusID)
		{
			if (!is_null(Core_Array::getPost('flag_delete_bonus')))
			{
				$oShopItem->remove($oShop_Bonus);
			}
			else
			{
				$bIsNull = is_null($oShopItem->Shop_Item_Bonuses->getByShop_bonus_id($iBonusID));
				
				if ($bIsNull)
				{
					// Устанавливаем бонус товару
					$oShopItem->add($oShop_Bonus);
				}
			}
		}
	}
}