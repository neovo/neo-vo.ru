<?php

	$shopId = Core_Array::get(Core_Page::instance()->widgetParams, 'shopId');
	$xslName = Core_Array::get(Core_Page::instance()->widgetParams, 'xsl');
	$limit = Core_Array::get(Core_Page::instance()->widgetParams, 'limit');
	$propertyId = Core_Array::get(Core_Page::instance()->widgetParams, 'propertyId');

	if (Core::moduleIsActive('shop')) {
		$Shop_Controller_Show = new Shop_Controller_Show(
			Core_Entity::factory('Shop', $shopId)
		);
		$Shop_Controller_Show
			->xsl(
				Core_Entity::factory('Xsl')->getByName($xslName)
			)
			->groupsMode('none')
			->group(false)
			->cache(false)
			->itemsProperties(true)
			->limit($limit)
		;
		$Shop_Controller_Show
			->shopItems()
			->queryBuilder()
			->join('shop_item_properties', 'shop_items.shop_id', '=', 'shop_item_properties.shop_id')
			->join('property_value_ints', 'shop_items.id', '=', 'property_value_ints.entity_id')
			->where('shop_items.modification_id', '=', 0)
			->where('shop_item_properties.property_id', '=', $propertyId)
			->where('property_value_ints.property_id', '=', $propertyId)
			->where('property_value_ints.value', '=', '1') 
			->clearOrderBy()
			->orderBy('rand()')
		;
		$Shop_Controller_Show->show();
	}

?>