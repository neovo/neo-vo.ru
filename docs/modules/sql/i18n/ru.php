<?php 
/**
 * SQL.
 *
 * @package HostCMS
 * @subpackage Sql
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'menu' => 'SQL-запросы',
	'title' => 'SQL-запросы',
	'warning' => 'Внимание! Выполнение SQL-запросов может повредить работоспособности системы. <br> 
	Перед выполнением запросов рекомендуется сделать резервное копирование.',
	'text' => '<acronym title="Текст SQL-запроса">Текст запроса</acronym>',
	'load_file' => '<acronym title="Файл с SQL-запросами">Загрузить файл</acronym>',
	'warningButton' => 'Вы уверены, что хотите выполнить данный запрос?',
	'button' => 'Выполнить запрос',
	'success_message' => 'Ваш запрос успешно выполнен. Выполненно инструкций: %d.',
	'error_message' => 'Запрос не содержит данных или содержит ошибку! <br> Проверьте, пожайлуста, его содержимое или правильность указания пути и имени для файла.',
	'table' => 'База данных',
	'optimize_table' => 'Оптимизировать',
	'repair_table' => 'Исправить (только для MyISAM)',
	'optimize_table_title' => 'Оптимизация таблиц',
	'repair_table_title' => 'Исправление таблиц',
	//'optimize_success' => 'Таблицы успешно оптимизированы',
	//'repair_success' => 'Таблицы успешно исправлены',

	'rows_count' => 'Найдено <b>%d</b> строк, показано <b>%d</b> строк.',
	'drop_index' => 'Дублирующий индекс %s таблицы %s был удален.',
	
	'delete_success' => 'Элемент удален!',
	'undelete_success' => 'Элемент восстановлен!',
);