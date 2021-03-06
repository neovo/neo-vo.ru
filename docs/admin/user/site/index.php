<?php
/**
 * Administration center users.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../../bootstrap.php');

Core_Auth::authorization($sModule = 'user');

// Код формы
$iAdmin_Form_Id = 182;
$sAdminFormAction = '/admin/user/site/index.php';

$oAdmin_Form = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id);

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create($oAdmin_Form);
$oAdmin_Form_Controller
	->module(Core_Module::factory($sModule))
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('User_Group.choosing_site'))
	->pageTitle(Core::_('User_Group.choosing_site'));

// Элементы строки навигации
$oAdmin_Form_Entity_Breadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

// Путь к контроллеру формы групп пользователей
$sUserGroupsPath = '/admin/user/index.php';

$user_group_id = Core_Array::getGet('user_group_id');

// Путь к контроллеру формы пользователей определенной группы
$sUsersPath = '/admin/user/user/index.php';
$sAdditionalUsersParams = 'user_group_id=' . $user_group_id;

$oUser_Group = Core_Entity::factory('User_Group', $user_group_id);

// Элементы строки навигации
$oAdmin_Form_Entity_Breadcrumbs->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('User_Group.ua_link_users_type'))
		->href(
			$oAdmin_Form_Controller->getAdminLoadHref($sUserGroupsPath, NULL, NULL, '')
		)
		->onclick(
			$oAdmin_Form_Controller->getAdminLoadAjax($sUserGroupsPath, NULL, NULL, '')
	)
)
->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('User.ua_show_users_title', $oUser_Group->name))
		->href(
			$oAdmin_Form_Controller->getAdminLoadHref($sUsersPath, NULL, NULL, $sAdditionalUsersParams)
		)
		->onclick(
			$oAdmin_Form_Controller->getAdminLoadAjax($sUsersPath, NULL, NULL, $sAdditionalUsersParams)
	)
)
->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('User_Group.choosing_site'))
		->href(
			$oAdmin_Form_Controller->getAdminLoadHref($oAdmin_Form_Controller->getPath())
		)
		->onclick(
			$oAdmin_Form_Controller->getAdminLoadAjax($oAdmin_Form_Controller->getPath())
	)
);

// Добавляем все хлебные крошки контроллеру
$oAdmin_Form_Controller->addEntity($oAdmin_Form_Entity_Breadcrumbs);

// Действие "Применить"
$oAdminFormActionApply = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('apply');

if ($oAdminFormActionApply && $oAdmin_Form_Controller->getAction() == 'apply')
{
	$oControllerApply = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Apply', $oAdminFormActionApply
	);

	// Добавляем типовой контроллер редактирования контроллеру формы
	$oAdmin_Form_Controller->addAction($oControllerApply);
}

// Действие "Копировать"
$oAdminFormActionCopy = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('copy');

if ($oAdminFormActionCopy && $oAdmin_Form_Controller->getAction() == 'copy')
{
	$oControllerCopy = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Copy', $oAdminFormActionCopy
	);

	// Добавляем типовой контроллер редактирования контроллеру формы
	$oAdmin_Form_Controller->addAction($oControllerCopy);
}

// Источник данных 0
$oAdmin_Form_Dataset = new Admin_Form_Dataset_Entity(
	Core_Entity::factory('Site')
);

$oUser = Core_Entity::factory('User')->getCurrent();

// Ограничение списка сайтов для непривилегированного пользователя
if ($oUser->superuser == 0)
{
	$oAdmin_Form_Dataset->addCondition(
		array('select' => array('sites.*'))
	)->addCondition(
		array('join' => array('user_modules', 'sites.id', '=', 'user_modules.site_id'))
	)->addCondition(
		array('where' => array('user_modules.user_group_id', '=', $oUser->user_group_id))
	)->addCondition(
		array('groupBy' => array('sites.id'))
	);
}

// Добавляем источник данных контроллеру формы
$oAdmin_Form_Controller->addDataset($oAdmin_Form_Dataset);

// Внешняя заменя для onclick и href
$oAdmin_Form_Controller->addExternalReplace('{user_group_id}', $user_group_id);

// Change links to other form
if (Core_Array::getGet('mode') == 'action')
{
	$oAdmin_Form_Dataset->changeField('name', 'link', '/admin/user/site/form/index.php?user_group_id={user_group_id}&site_id={id}&mode=action');
	$oAdmin_Form_Dataset->changeField('name', 'onclick', "$.adminLoad({path: '/admin/user/site/form/index.php', additionalParams: 'user_group_id={user_group_id}&site_id={id}&mode=action', windowId: '{windowId}'}); return false");

	$oAdmin_Form_Controller->addExternalReplace('{mode}', 'action');
}
else
{
	$oAdmin_Form_Controller->addExternalReplace('{mode}', 'module');
}

$oAdmin_Form_Controller->execute();
