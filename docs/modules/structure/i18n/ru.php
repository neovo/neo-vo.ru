<?php
/**
 * Structure.
 *
 * @package HostCMS
 * @subpackage Structure
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'model_name' => 'Структура сайта',
	'menu' => 'Структура сайта',
	'title' => 'Структура сайта',
	'main_menu' => 'Раздел',
	'add' => 'Добавить',
	'seo_tab' => 'SEO',
	'sitemap_tab' => 'Sitemaps',
	'parent_dir' => 'Разделы структуры',
	'edit_success' => 'Раздел структуры добавлен!',
	'edit_error' => 'Ошибка! Раздел структуры не добавлен!',
	'apply_success' => 'Информация о разделе структуры изменена!',
	'apply_error' => 'Ошибка! Информация о разделе структуры не изменена!',
	'markDeleted_success' => 'Раздел структуры успешно удален!',
	'markDeleted_error' => 'Ошибка! Раздел структуры не удален!',
	'copy_success' => 'Раздел структуры успешно скопирован!',
	'copy_error' => 'Раздел структуры успешно скопирован!',
	'changeStatus_success' => 'Активность раздела структуры успешно изменена.',
	'changeStatus_error' => 'Ошибка при изменении активности раздела структуры.',
	'changeIndexing_success' => 'Статус индексирования раздела структуры успешно изменен.',
	'changeIndexing_error' => 'Ошибка при изменении статус индексирования раздела структуры.',
	'add_title' => 'Добавление раздела',
	'edit_title' => 'Редактирование узла структуры',
	'parent_id' => '<acronym title="Раздел, в который помещается данный раздел">Родительский раздел</acronym>',
	'structure_menu_id' => '<acronym title="Меню, в котором будет отображаться раздел сайта">Меню</acronym>',
	'template_id' => '<acronym title="Выберите макет сайта. Например «Основной»">Макет</acronym>',
	'document_dir_id' => '<acronym title="Раздел документов сайта">Раздел документов</acronym>',
	'document_id' => '<acronym title="Внутреннее название документа в системе управления">Название документа</acronym>',
	'show' => '<acronym title="Управляет видимостью данного раздела в меню">Отображать в меню сайта</acronym>',
	'active' => '<acronym title="Управляет активностью страницы и подразделами">Активность страницы</acronym>',
	'indexing' =>'<acronym title="Управляет индексированием страницы">Индексировать</acronym>',
	'name' => '<acronym title="Название раздела в меню сайта">Название раздела в меню</acronym>',
	'seo_title' => '<acronym title="Значение мета-тега <title> для страницы">Заголовок страницы [Title]</acronym>',
	'seo_description' => '<acronym title="Значение мета-тега <description> для страницы">Описание страницы [Description]</acronym>',
	'seo_keywords' => '<acronym title="Значение мета-тега <keywords> для страницы">Ключевые слова [Keywords]</acronym>',
	'url' => '<acronym title="Раздел сайта может являться внешней ссылкой">Ссылка на другой файл</acronym>',
	'sorting' => '<acronym title="Поле, по которому производится сортировка страницы">Сортировка для текущего уровня</acronym>',
	'path' => '<acronym title="Фрагмент пути относительно родительского раздела, например, about-company">Путь</acronym>',
	'type' => '<acronym title="Выберите тип раздела">Тип раздела</acronym>',
	'static_page' => 'Страница',
	'typical_dynamic_page' => 'Типовая динамическая страница',
	'dynamic_page' => 'Динамическая страница',
	'siteuser_group_id' => '<acronym title="Группа, имеющая права доступа к данной странице">Группа доступа</acronym>',
	'https' => '<acronym title="Использовать HTTPS для доступа к узлу структуры">Доступ через HTTPS</acronym>',
	'structure_source' => '<acronym title="PHP код вызова динамической страницы">Динамическая страница</acronym>',
	'structure_config_source' => '<acronym title="Позволяют переопределять значения параметров страницы. Например значение параметра «title»">Настройки динамической страницы</acronym>',
	'lib_dir_id' => '<acronym title="Раздел типовых динамических страниц">Раздел</acronym>',
	'lib_id' => '<acronym title="Название типовой динамической страницы">Страница</acronym>',
	'lib_contains_no_parameters' => 'Типовая динамическая страница не содержит параметров.',
	'query_error' => "Ошибка выполнения запроса '%s'",
	'id' => 'Идентификатор',
	'site_id' => 'Идентификатор сайта',
	'properties' => 'Свойства',
	'changefreq' => "<acronym title=\"Частота обновления страницы\">Частота обновления</acronym>",
	'priority' => "<acronym title=\"Приоритет данной страницы относительно остальных страниц\">Приоритет</acronym>",
	'sitemap_refrashrate_never' => "Никогда",
	'sitemap_refrashrate_yearly' => "Ежегодно",
	'sitemap_refrashrate_monthly' => "Ежемесячно",
	'sitemap_refrashrate_weekly' => "Еженедельно",
	'sitemap_refrashrate_daily' => "Ежедневно",
	'sitemap_refrashrate_hourly' => "Ежечасно",
	'sitemap_refrashrate_always' => "Всегда",
	'sitemap_priority_small' => "0 Низший приоритет",
	'sitemap_priority_0_1' => "0.1",
	'sitemap_priority_0_2' => "0.2",
	'sitemap_priority_0_3' => "0.3",
	'sitemap_priority_0_4' => "0.4",
	'sitemap_priority_normal' => "0.5 Средний приоритет",
	'sitemap_priority_0_6' => "0.6",
	'sitemap_priority_0_7' => "0.7",
	'sitemap_priority_0_8' => "0.8",
	'sitemap_priority_0_9' => "0.9",
	'sitemap_priority_above_normal' => "1 Высший приоритет",
	'additional_params_tab' => 'Дополнительные свойства',
	'all' => 'Все',
	'like_parent' => 'Как у родителя',
	// Константы, используемые в классе
	'file_write_error_message' => 'Ошибка записи файла %s. Проверьте права доступа к директории!',
	'save_lib_file_error' => 'Невозможно сохранить lib-файл, проверьте права доступа.',
	'delete_success' => 'Элемент удален!',
	'undelete_success' => 'Элемент восстановлен!',
);