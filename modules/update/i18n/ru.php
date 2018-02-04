<?php
/**
 * Updates.
 *
 * @package HostCMS 6\Update
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'menu' => 'Обновления',
	'main_menu' => 'Обновления',
	'submenu' => 'Проверить обновления',
	'loadUpdates_success' => 'Список обновлений загружен',
	'install_success' => 'Обновление "%s" установлено.',
	'constant_check_error' => 'Не обнаружено необходимых констант, заполните поля формы Сайты &#8594; Настройки &#8594; Регистрационные данные',
	'isLastUpdate' => 'У Вас установлена последняя версия системы.',
	'server_error_xml' => 'Ошибка в структуре XML ответа сервера! Попробуйте запросить обновление еще раз.',
	'server_error_respond_0' => 'Неизвестная ошибка!',
	'server_error_respond_1' => 'Изменилась конфигурация сервера, получение обновлений невозможно. Обратитесь в службу поддержки.',
	'server_error_respond_2' => 'Не найден пользователь. Проверьте правильность указания логина в регистрационных данных в разделе Сайты.',
	'server_error_respond_3' => 'Не найден заказ. Вероятно заказ принадлежит другому пользователю. Проверьте правильность указания логина пользователя в регистрационных данных в разделе Сайты.',
	'server_error_respond_4' => 'Не обнаружена система HostCMS. Попробуйте еще раз запросить обновление. Обратите внимание &mdash; на основном домене текущего сайта должна быть доступна система управления. Указание основного домена осуществляется в списке доменов.',
	'server_error_respond_5' => 'Период технической поддержки истек! Продлить техническую поддержку можно в личном кабинете.',
	'server_error_respond_6' => 'Установка обновлений возможна только от младшей версии к старшей.',
	'server_error_respond_7' => 'Обновление недоступно для Вашей редакции.',
	'server_error_respond_8' => 'Не соответствует редакция системы управления.',
	'server_error_respond_9' => 'Лицензия имеет несколько установок.',
	'error_open_updatefile' => 'Файл обновлений не обнаружен.',
	'error_write_file_update' => 'Ошибка записи данных в файл "%s".',
	'update_constant_error' => 'Не обнаружена константа HOSTCMS_UPDATE_SERVER.',
	'update_files_error' => 'Ошибка распаковки tar.gz файла.',
	'server_return_empty_answer' => 'Сервер вернул пустой ответ, наиболее вероятно на хостинге запрещены исходящие соединения с использованием fsockopen(). Обратитесь к хостинг-провайдеру или администратору сервера.',
	'support_available' => 'Период технической поддержки доступен до %s г.',
	'support_has_expired' => 'Период технической поддержки истек %s г. <a href="http://%s/users/" target="_blank">Продлить поддержку</a>.',
	'msg_update_required' => 'Запрошено обновление %s',
	'msg_installing_package' => 'Установка пакета обновлений %s',
	'msg_unpack_package' => 'Распаковка файла %s',
);