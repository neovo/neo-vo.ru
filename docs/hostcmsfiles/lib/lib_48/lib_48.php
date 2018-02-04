<?php

$oShop = Core_Entity::factory('Shop', Core_Array::get(Core_Page::instance()->libParams, 'shopId'));
$Shop_Controller_Show = new Shop_Controller_Show($oShop);

$Shop_Controller_Show
	->xsl(
		Core_Entity::factory('Xsl')->getByName('МагазинПоследниеПросмотренные')
	)
	->viewedLimit(5)
	->show();


// если мы находимся на странице товара
//if ($Shop_Controller_Show->item)
{
	/** Показ последних просмотренных товаров - начало **/

	// XSL-шаблон для отображения последних просмотренных товаров
	$last_items_xslName = 'МагазинПоследниеПросмотренные';

	$oShop_Controller_Show_Last_Items = Shop_Controller_Show_Last_Items::instance($oShop);

	$oShop_Controller_Show_Last_Items
		->limit(10) // макс. количество выводимых товаров
		->is_random(FALSE) // сортировка: TRUE - в случайном порядке; FALSE - в порядке, обратном посещениям
		->allow_show_current_item(FALSE) // если находимся на странице товара: TRUE - разрешает показывать его среди последних просмотренных; FALSE - запрещает.
		->show_parent_items_instead_modifications(TRUE) // TRUE - показывать родительские товары вместо модификаций; FALSE - сами модификации.
		->exclude_incart_items(TRUE) // TRUE - запрещает показывать товары, которые уже есть в корзине; FALSE - разрешает.
		->exclude_out_of_rest_items(TRUE) // TRUE - запрещает показывать товары, которых нет в наличии; FALSE - разрешает.
		->cache(FALSE) // TRUE - разрешает использовать кеширование; FALSE - запрещает.
		->xsl(Core_Entity::factory('Xsl')->getByName($last_items_xslName))
		->showLastItems();
	/** Показ последних просмотренных товаров - конец **/
}