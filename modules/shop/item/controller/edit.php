<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Item_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		$modelName = $object->getModelName();

		$iShopItemId = Core_Array::getGet('shop_item_id', 0);

		// Магазин
		$oShop = Core_Entity::factory('Shop', Core_Array::getGet('shop_id', 0));

		if ($oShop->id == 0)
		{
			$oShop = Core_Entity::factory('Shop_Item', $iShopItemId)->Shop;
		}

		switch ($modelName)
		{
			case 'shop_item':

				$this
					->addSkipColumn('image_large')
					->addSkipColumn('image_small')
					->addSkipColumn('shortcut_id');

				if ($object->shortcut_id != 0)
				{
					$object = $object->Shop_Item;
				}

				if (!$object->id)
				{
					$object->shop_id = Core_Array::getGet('shop_id');
					$object->shop_group_id = Core_Array::getGet('shop_group_id', 0);
					$object->shop_currency_id = $oShop->shop_currency_id;
				}

				if ($iShopItemId)
				{
					$ShopItemModification = Core_Entity::factory('Shop_Item', $iShopItemId);

					$object->modification_id = $iShopItemId;
					$object->shop_id = $ShopItemModification->Shop->id;

					$this->addSkipColumn('shop_group_id');
				}

				parent::setObject($object);

				$template_id = $this->_object->Shop->Structure->template_id
					? $this->_object->Shop->Structure->template_id
					: 0;

				$title = $this->_object->id
					? Core::_('Shop_Item.items_catalog_edit_form_title')
					: Core::_('Shop_Item.items_catalog_add_form_title');

				$oMainTab = $this->getTab('main');
				$oAdditionalTab = $this->getTab('additional');

				$this->getField('image_small_height')
					->divAttr(array('style' => 'display: none'));
				$this->getField('image_small_width')
					->divAttr(array('style' => 'display: none'));
				$this->getField('image_large_height')
					->divAttr(array('style' => 'display: none'));
				$this->getField('image_large_width')
					->divAttr(array('style' => 'display: none'));

				// Создаем вкладки
				$oShopItemTabDescription = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Item.tab_description'))
					->name('Description');
				$oShopItemTabExportImport = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Item.tab_export'))
					->name('ExportImport');
				$oShopItemTabSEO = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Item.tab_seo'))
					->name('SEO');
				$oShopItemTabTags = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Item.tab_tags'))
					->name('Tags');
				$oShopItemTabSpecialPrices = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Item.tab_special_prices'))
					->name('SpecialPrices');

				$oMainTab
					->add($oMainRow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow2 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow3 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow4 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow5 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow6 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopItemTabDescription
					->add($oShopItemTabDescriptionRow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabDescriptionRow2 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabDescriptionRow3 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabDescriptionRow4 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopItemTabExportImport
					->add($oShopItemTabExportImportRow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabExportImportRow2 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabExportImportRow3 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabExportImportRow4 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabExportImportRow5 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabExportImportRow6 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabExportImportRow7 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabExportImportRow8 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopItemTabSEO
					->add($oShopItemTabSEORow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabSEORow2 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopItemTabSEORow3 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopItemTabTags
					->add($oShopItemTabTagsRow1 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopItemTabSpecialPrices
					->add($oShopItemTabSpecialPricesRow1 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				// Добавляем вкладки
				$this
					->addTabAfter($oShopItemTabDescription, $oMainTab)
					->addTabAfter($oShopItemTabExportImport, $oShopItemTabDescription)
					->addTabAfter($oShopItemTabSEO, $oShopItemTabExportImport)
					->addTabAfter($oShopItemTabTags, $oShopItemTabSEO)
					->addTabAfter($oShopItemTabSpecialPrices, $oShopItemTabTags)
				;

				$oPropertyTab = Admin_Form_Entity::factory('Tab')
					->caption(Core::_("Shop_Item.tab_properties"))
					->name('Property');

				$this->addTabBefore($oPropertyTab, $oAdditionalTab);

				// Properties
				Shop_Item_Property_Controller_Tab::factory($this->_Admin_Form_Controller)
					->setObject($this->_object)
					->setDatasetId($this->getDatasetId())
					->linkedObject(Core_Entity::factory('Shop_Item_Property_List', $oShop->id))
					->setTab($oPropertyTab)
					->template_id($template_id)
					->fillTab();

				// Переносим поля на вкладки
				$oMainTab
					->move($oDescriptionField = $this->getField('description'), $oShopItemTabDescription)
					->move($this->getField('yandex_market'), $oShopItemTabExportImport)
					->move($this->getField('vendorcode'), $oShopItemTabExportImport)
					->move($this->getField('yandex_market_bid'), $oShopItemTabExportImport)
					->move($this->getField('yandex_market_cid'), $oShopItemTabExportImport)
					->move($this->getField('manufacturer_warranty'), $oShopItemTabExportImport)
					->move($this->getField('country_of_origin'), $oShopItemTabExportImport)
					->move($this->getField('guid'), $oShopItemTabExportImport)
					->move($this->getField('yandex_market_sales_notes'), $oShopItemTabExportImport)
					->move($this->getField('seo_title')->rows(3), $oShopItemTabSEO)
					->move($this->getField('seo_description')->rows(3), $oShopItemTabSEO)
					->move($this->getField('seo_keywords')->rows(3), $oShopItemTabSEO)
				;

				$oShopItemTabExportImport
					->move($this->getField('yandex_market')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow1)
					->move($this->getField('vendorcode')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow2)
					->move($this->getField('yandex_market_bid')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow3)
					->move($this->getField('yandex_market_cid')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow4)
					->move($this->getField('manufacturer_warranty')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow5)
					->move($this->getField('country_of_origin')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow6)
					->move($this->getField('guid')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow7)
					->move($this->getField('yandex_market_sales_notes')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabExportImportRow8)
				;

				$oShopItemTabSEO
					->move($this->getField('seo_title')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabSEORow1)
					->move($this->getField('seo_description')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabSEORow2)
					->move($this->getField('seo_keywords')->divAttr(array('class' => 'form-group col-lg-12')), $oShopItemTabSEORow3)
				;

				$oDescriptionField
					->wysiwyg(TRUE)
					->rows(7)
					->template_id($template_id);

				$oShopItemTabDescription->move($oDescriptionField, $oShopItemTabDescriptionRow1);

				if (Core::moduleIsActive('typograph'))
				{
					$oDescriptionField->value(
						Typograph_Controller::instance()->eraseOpticalAlignment($oDescriptionField->value)
					);

					// поля описания товара
					$oTypographicDescriptionCheckBox = Admin_Form_Entity::factory('Checkbox');
					$oTypographicDescriptionCheckBox
						->value(
							$oShop->typograph_default_items == 1 ? 1 : 0
						)
						->caption(Core::_("Shop_Item.exec_typograph_for_text"))
						->name("exec_typograph_for_description")
						->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6'))
						//->divAttr(array('style' => 'float: left'))
						;

					$oShopItemTabDescriptionRow2->add($oTypographicDescriptionCheckBox);

					$oOpticalAlignDescriptionCheckBox = Admin_Form_Entity::factory('Checkbox')
						->value(
							$oShop->typograph_default_items == 1 ? 1 : 0
						)
						->name("use_trailing_punctuation_for_description")
						->caption(Core::_("Shop_Item.use_trailing_punctuation_for_text"))
						->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6'))
						;

					$oShopItemTabDescriptionRow2->add($oOpticalAlignDescriptionCheckBox);
				}

				$oMainTab->moveAfter($oTextField = $this->getField('text'), isset($oOpticalAlignDescriptionCheckBox) ? $oOpticalAlignDescriptionCheckBox : $oDescriptionField, $oShopItemTabDescription);

				$oTextField
					->wysiwyg(TRUE)
					->rows(15)
					->template_id($template_id);

				$oShopItemTabDescription->move($oTextField, $oShopItemTabDescriptionRow3);

				if (Core::moduleIsActive('typograph'))
				{
					$oTextField->value(
						Typograph_Controller::instance()->eraseOpticalAlignment($oTextField->value)
					);

					// Добавляем два суррогатных поля текста товара
					$oTypographicTextCheckBox = Admin_Form_Entity::factory('Checkbox');
					$oTypographicTextCheckBox
						->value(
							$oShop->typograph_default_items == 1 ? 1 : 0
						)
						->caption(Core::_("Shop_Item.exec_typograph_for_text"))
						->name("exec_typograph_for_text")
						->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6'))
						;

					$oShopItemTabDescriptionRow4->add($oTypographicTextCheckBox);

					$oOpticalAlignCheckBox = Admin_Form_Entity::factory('Checkbox');
					$oOpticalAlignCheckBox
						->value(
							$oShop->typograph_default_items == 1 ? 1 : 0
						)
						->name("use_trailing_punctuation_for_text")
						->caption(Core::_("Shop_Item.use_trailing_punctuation_for_text"))
						->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6'));

					$oShopItemTabDescriptionRow4->add($oOpticalAlignCheckBox);
				}

				// Удаляем тип товара
				$oMainTab->delete($this->getField('type'));

				$windowId = $this->_Admin_Form_Controller->getWindowId();

				$oRadioType = Admin_Form_Entity::factory('Radiogroup')
					->name('type')
					->id('shopItemType' . time())
					->caption(Core::_('Shop_Item.type'))
					->value($this->_object->type)
					->divAttr(array('class' => 'form-group col-lg-8 col-md-12 col-sm-12'))
					->radio(array(
						0 => Core::_('Shop_Item.item_type_selection_group_buttons_name_simple'),
						2 => Core::_('Shop_Item.item_type_selection_group_buttons_name_divisible'),
						1 => Core::_('Shop_Item.item_type_selection_group_buttons_name_electronic')
					))
					->ico(
						array(
							0 => 'fa-file-text-o',
							2 => 'fa-puzzle-piece',
							1 => 'fa-table',
					));

				// Добавляем тип товара
				$oMainRow3->add($oRadioType);

				// Удаляем модификацию
				$oAdditionalTab->delete($this->getField('modification_id'));

				$oModificationSelect = Admin_Form_Entity::factory('Select');

				$oModificationSelect
					->caption(Core::_('Shop_Item.shop_item_catalog_modification_flag'))
					->options($this->_fillModificationList($this->_object))
					->name('modification_id')
					->value($this->_object->modification_id)
					->divAttr(array('class' => 'form-group col-lg-4 col-md-12 col-sm-12'));

				$oMainRow3->add($oModificationSelect);

				if(!$object->modification_id)
				{
					// Удаляем группу товаров
					$oAdditionalTab->delete($this->getField('shop_group_id'));

					$oShopGroupSelect = Admin_Form_Entity::factory('Select');
					$oShopGroupSelect->caption(Core::_('Shop_Item.shop_group_id'))
					->options(array(' … ') + self::fillShopGroup($this->_object->shop_id))
					->name('shop_group_id')
					->value($this->_object->shop_group_id)
					->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12'))
					->filter(TRUE);

					// Добавляем группу товаров
					$oMainRow1->add($oShopGroupSelect);
				}
				else
				{
					$this->_object->shop_group_id = 0;
				}

				$oMainTab
					->move($this->getField('datetime')->divAttr(array('class' => 'form-group col-lg-3 col-md-6 col-sm-6 col-xs-12')), $oMainRow4)
					->move($this->getField('start_datetime')->divAttr(array('class' => 'form-group col-lg-3 col-md-6 col-sm-6 col-xs-12')), $oMainRow4)
					->move($this->getField('end_datetime')->divAttr(array('class' => 'form-group col-lg-3 col-md-6 col-sm-6 col-xs-12')), $oMainRow4)
					->move($this->getField('showed')->divAttr(array('class' => 'form-group col-lg-3 col-md-6 col-sm-6 col-xs-12')), $oMainRow4)
				;

				// Добавляем новое поле типа файл
				$oImageField = Admin_Form_Entity::factory('File');

				$oLargeFilePath = is_file($this->_object->getLargeFilePath())
					? $this->_object->getLargeFileHref()
					: '';

				$oSmallFilePath = is_file($this->_object->getSmallFilePath())
					? $this->_object->getSmallFileHref()
					: '';

				$sFormPath = $this->_Admin_Form_Controller->getPath();

				$oImageField
					//->style("width: 400px;")
					->divAttr(array('class' => 'form-group col-lg-12'))
					->name("image")
					->id("image")
					->largeImage(array('max_width' => $oShop->image_large_max_width, 'max_height' => $oShop->image_large_max_height, 'path' => $oLargeFilePath, 'show_params' => TRUE, 'watermark_position_x' => $oShop->watermark_default_position_x, 'watermark_position_y' => $oShop->watermark_default_position_y, 'place_watermark_checkbox_checked' => $oShop->watermark_default_use_large_image, 'delete_onclick' =>
							"$.adminLoad({path: '{$sFormPath}', additionalParams:
							'hostcms[checked][{$this->_datasetId}][{$this->_object->id}]=1', action: 'deleteLargeImage', windowId: '{$windowId}'}); return false", 'caption' => Core::_('Shop_Item.items_catalog_image'), 'preserve_aspect_ratio_checkbox_checked' => $oShop->preserve_aspect_ratio)
					)
					->smallImage(array('max_width' => $oShop->image_small_max_width, 'max_height' => $oShop->image_small_max_height, 'path' => $oSmallFilePath, 'create_small_image_from_large_checked' =>
							$this->_object->image_small == '', 'place_watermark_checkbox_checked' =>
							$oShop->watermark_default_use_small_image, 'delete_onclick' => "$.adminLoad({path: '{$sFormPath}', additionalParams:
							'hostcms[checked][{$this->_datasetId}][{$this->_object->id}]=1', action: 'deleteSmallImage', windowId: '{$windowId}'}); return false", 'caption' => Core::_('Shop_Item.items_catalog_image_small'), 'show_params' => TRUE, 'preserve_aspect_ratio_checkbox_checked' => $oShop->preserve_aspect_ratio_small)
					);

				$oMainRow5->add($oImageField);

				$oMainTab
					->move($this->getField('marking')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4')), $oMainRow6)
					->move($this->getField('weight')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4 col-xs-6')), $oMainRow6);

				// Удаляем единицы измерения
				$oAdditionalTab->delete($this->getField('shop_measure_id'));

				$Shop_Controller_Edit = new Shop_Controller_Edit($this->_Admin_Form_Action);

				// Единицы измерения
				$oMainRow6->add(
					Admin_Form_Entity::factory('Select')
						->caption(Core::_('Shop_Item.shop_measure_id'))
						->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4 col-xs-6'))
						->options($Shop_Controller_Edit->fillMeasures())
						->name('shop_measure_id')
						->value($this->_object->shop_measure_id)
				);

				// Получаем список складов магазина
				$aWarehouses = $oShop->Shop_Warehouses->findAll();

				foreach($aWarehouses as $oWarehouse)
				{
					$oMainTab->add($oWarehouseCurrentRow = Admin_Form_Entity::factory('Div')->class('row'));

					$oWarehouseTextBox = Admin_Form_Entity::factory('Input');

					// Получаем количество товара на текущем складе
					$oWarehouseItem =
						$this->_object->Shop_Warehouse_Items->getByWarehouseId($oWarehouse->id);

					$value = is_null($oWarehouseItem)
						? 0
						: $oWarehouseItem->count;

					$oWarehouseTextBox
						->caption(Core::_("Shop_Item.warehouse_item_count", $oWarehouse->name))
						->value($value)
						->name("warehouse_{$oWarehouse->id}")
						->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4'))
						;

					$oWarehouseCurrentRow->add($oWarehouseTextBox);
				}

				$oMainTab
					->add($oMainRow7 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow8 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow9 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow10 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				// Удаляем группу доступа
				$oAdditionalTab->delete($this->getField('siteuser_group_id'));

				if (Core::moduleIsActive('siteuser'))
				{
					$oSiteuser_Controller_Edit =
										new Siteuser_Controller_Edit($this->_Admin_Form_Action);
					$aSiteuser_Groups =
						$oSiteuser_Controller_Edit->fillSiteuserGroups
						($this->_object->Shop->site_id);
				}
				else
				{
					$aSiteuser_Groups = array();
				}

				// Создаем поле групп пользователей сайта как выпадающий список
				$oSiteUserGroupSelect = Admin_Form_Entity::factory('Select');
				$oSiteUserGroupSelect
					->caption(Core::_("Shop_Item.siteuser_group_id"))
					->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4'))
					->options(array(-1 => Core::_('Shop_Item.shop_users_group_parrent')) + $aSiteuser_Groups)
					->name('siteuser_group_id')
					->value($this->_object->siteuser_group_id);

				// Добавляем группы пользователей сайта
				$oMainRow9->add($oSiteUserGroupSelect);

				// Удаляем продавцов
				$oAdditionalTab->delete($this->getField('shop_seller_id'));

				// Создаем поле продавцов как выпадающий список
				$oShopSellerSelect = Admin_Form_Entity::factory('Select');

				$oShopSellerSelect->caption(Core::_('Shop_Item.shop_seller_id'))
					->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4'))
					->options($this->_fillSellersList())
					->name('shop_seller_id')
					->value($this->_object->shop_seller_id);

				// Добавляем продавцов
				$oMainRow9->add($oShopSellerSelect);

				// Удаляем производителей
				$oAdditionalTab->delete($this->getField('shop_producer_id'));

				// Создаем поле производителей как выпадающий список
				$oShopProducerSelect = Admin_Form_Entity::factory('Select');

				$oShopProducerSelect->caption(Core::_('Shop_Item.shop_producer_id'))
					->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4'))
					->options($this->fillProducersList(intval(Core_Array::getGet('shop_id', 0))))
					->name('shop_producer_id')
					->value($this->_object->shop_producer_id);

				// Добавляем продавцов
				$oMainRow9->add($oShopProducerSelect);

				// Перемещаем цену
				$this->getField('price')
					->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2 col-xs-6'))
					->id('price');

				$oMainTab->move($this->getField('price'), $oMainRow10);

				// Удаляем валюты
				$oAdditionalTab->delete($this->getField('shop_currency_id'));

				// Создаем поле валюты как выпадающий список
				$oShopCurrencySelect = Admin_Form_Entity::factory('Select');

				$oShopCurrencySelect
					->caption("&nbsp;")
					->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2 col-xs-6'))
					->options($Shop_Controller_Edit->fillCurrencies())
					->name('shop_currency_id')
					->value($this->_object->shop_currency_id);

				// Добавляем валюты
				$oMainRow10->add($oShopCurrencySelect);

				// Удаляем налоги
				$oAdditionalTab->delete($this->getField('shop_tax_id'));

				// Создаем поле налогов как выпадающий список
				$oShopTaxSelect = Admin_Form_Entity::factory('Select')
					->caption(Core::_("Shop_Item.shop_tax_id"))
					->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4'))
					->options($this->fillTaxesList())
					->name('shop_tax_id')
					->value($this->_object->shop_tax_id);

				// Добавляем налоги
				$oMainRow10->add($oShopTaxSelect);

				$aShopPrices = $oShop->Shop_Prices->findAll(FALSE);
				foreach($aShopPrices as $oShopPrice)
				{
					$oMainTab->add($oPricesRow = Admin_Form_Entity::factory('Div')->class('row'));

					// Получаем значение специальной цены для товара
					$oShop_Item_Price = $this->_object->Shop_Item_Prices->getByPriceId($oShopPrice->id);

					$value = is_null($oShop_Item_Price)
						? 0
						: $oShop_Item_Price->value;

					$oItemPriceCheckBox = Admin_Form_Entity::factory('Checkbox')
						->caption($oShopPrice->name)
						->id("item_price_id_{$oShopPrice->id}")
						->value($value)
						->name("item_price_id_{$oShopPrice->id}")
						->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-6 col-xs-9'))
						->onclick("document.getElementById('item_price_value_{$oShopPrice->id}').disabled
					= !this.checked; if (this.checked)
					{document.getElementById('item_price_value_{$oShopPrice->id}').value
					= (document.getElementById('price').value
					* {$oShopPrice->percent} / 100).toFixed(2); }");

					$oItemPriceTextBox = Admin_Form_Entity::factory('Input')
						->id("item_price_value_{$oShopPrice->id}")
						->name("item_price_value_{$oShopPrice->id}")
						->value($value)
						->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2 col-xs-3'))
					;

					$value == 0 && $oItemPriceTextBox->disabled('disabled');

					$oPricesRow
						->add($oItemPriceCheckBox)
						->add($oItemPriceTextBox);
				}

				if ($this->_object->Modifications->getCount())
				{
					//Checkbox применения цен для модификаций
					$oModificationPrice = Admin_Form_Entity::factory('Checkbox');
					$oModificationPrice
						->value(0)
						->name("apply_price_for_modification")
						->caption(Core::_("Shop_Item.apply_price_for_modification"));

					$oMainTab->addAfter($oModificationPrice, $oShopTaxSelect);
					$fieldAfter = $oModificationPrice;
				}

				$oMainTab
					->move($this->getField('path')->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6')), $oMainRow7)
					->move($this->getField('sorting')->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6')), $oMainRow7)
					->move($this->getField('indexing')->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6')), $oMainRow8)
					->move($this->getField('active')->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6')), $oMainRow8);

				// Заполняем вкладку специальных цен
				$aShop_Specialprices = $this->_object->Shop_Specialprices->findAll();

				// Выводим форму добавления новой спеццены
				$oSpecMinQuantity = Admin_Form_Entity::factory('Input')
					->caption(Core::_("Shop_Item.form_edit_add_shop_special_prices_from"))
					->name('specMinQuantity_[]')
					->divAttr(array('class' => 'form-group col-xs-6 col-sm-3 col-md-3 col-lg-3'))
					->format(array('maxlen' => array('value' => 12), 'lib' => array('value' => 'integer')));

				$oSpecMaxQuantity = Admin_Form_Entity::factory('Input')
					->caption(Core::_("Shop_Item.form_edit_add_shop_special_prices_to"))
					->name('specMaxQuantity_[]')
					->divAttr(array('class' => 'form-group col-xs-6 col-sm-3 col-md-3 col-lg-3'))
					->format(array('maxlen' => array('value' => 12), 'lib' => array('value' => 'integer')));

				$oSpecPrice = Admin_Form_Entity::factory('Input')
					->caption(Core::_("Shop_Item.form_edit_add_shop_special_pricess_price"))
					->name('specPrice_[]')
					->divAttr(array('class' => 'form-group col-xs-4 col-sm-2 col-md-2 col-lg-2'))
					->format(array('maxlen' => array('value' => 12), 'lib' => array('value' => 'decimal')));

				ob_start();
				$oCore_Html_Entity_Div = Core::factory('Core_Html_Entity_Div')
					->style('float: left; padding-top: 30px;')
					->value(Core::_("Shop_Item.or"))
					->execute();
				$oOR = Admin_Form_Entity::factory('Code')->html(ob_get_clean());

				$oSpecPricePercent = Admin_Form_Entity::factory('Input')
					->caption(Core::_("Shop_Item.form_edit_add_shop_special_pricess_percent"))
					->name('specPercent_[]')
					->divAttr(array('class' => 'form-group col-xs-4 col-sm-2 col-md-2 col-lg-2'))
					->format(array('maxlen' => array('value' => 12), 'lib' => array('value' => 'decimal')));

				$oDivOpen = Admin_Form_Entity::factory('Code')->html('<div class="spec_prices item_div clear" width="600">');
				$oDivClose = Admin_Form_Entity::factory('Code')->html('</div>');

				if(count($aShop_Specialprices) > 0)
				{
					foreach($aShop_Specialprices as $oShop_Specialprice)
					{
						$oSpecMinQuantity = clone $oSpecMinQuantity;
						$oSpecMaxQuantity = clone $oSpecMaxQuantity;
						$oSpecPrice = clone $oSpecPrice;
						$oSpecPricePercent = clone $oSpecPricePercent;
						$oSpecMinQuantity->value($oShop_Specialprice->min_quantity);
						$oSpecMaxQuantity->value($oShop_Specialprice->max_quantity);
						$oSpecPrice->value($oShop_Specialprice->price);
						$oSpecPricePercent->value($oShop_Specialprice->percent);

						$oShopItemTabSpecialPricesRow1
							->add($oDivOpen)
							->add($oSpecMinQuantity->name("specMinQuantity_{$oShop_Specialprice->id}"))
							->add($oSpecMaxQuantity->name("specMaxQuantity_{$oShop_Specialprice->id}"))
							->add($oSpecPrice->name("specPrice_{$oShop_Specialprice->id}"))
							->add($oOR)
							->add($oSpecPricePercent->name("specPercent_{$oShop_Specialprice->id}"))
							->add($this->_imgBox())
							->add($oDivClose);
					}
				}
				else
				{
					$oShopItemTabSpecialPricesRow1
						->add($oDivOpen)
						->add($oSpecMinQuantity)
						->add($oSpecMaxQuantity)
						->add($oSpecPrice)
						->add($oOR)
						->add($oSpecPricePercent)
						->add($this->_imgBox())
						->add($oDivClose);
				}

				if (Core::moduleIsActive('tag'))
				{
					// Добавляем метки на вкладку меток
					$html = '<label class="tags_label" for="form-field-tags">' . Core::_('Shop_Item.items_catalog_tags') . '</label>
							<div class="item_div">
								<input type="text" name="tags" id="form-field-tags" value="' . implode(", ", $this->_object->Tags->findAll()) . '" placeholder="' . Core::_('Shop_Item.type_tag') . '" />
							</div>
							<script type="text/javascript">
								jQuery(function($){
									//we could just set the data-provide="tag" of the element inside HTML, but IE8 fails!
									var tag_input = $(\'#form-field-tags\');
									if(! ( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) )
									{
										tag_input.tag( { placeholder:tag_input.attr(\'placeholder\') } );
									}
									else {
										tag_input.after(\'<textarea id="\'+ tag_input.attr(\'id\')+\'" name="\'+tag_input.attr(\'name\')+\'" rows=\"3\">\'+tag_input.val()+\'</textarea>\').remove();
									}
								})</script>
							';
					$oShopItemTabTags->add(Admin_Form_Entity::factory('Code')->html($html));
				}

				$oMainTab->add($oMainRow9 = Admin_Form_Entity::factory('Div')->class('row'));

				$this->getField('length')
					->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2 col-xs-4 no-padding-right'))
					->add(
						Core::factory('Core_Html_Entity_Span')
						->class('input-group-addon dimension_patch')
						->value('×')
					)
					->caption(Core::_('Shop_Item.item_length'));

				$oMainTab->move($this->getField('length'), $oMainRow9);

				$this->getField('width')
					->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2 col-xs-4 no-padding'))
					->caption(Core::_('Shop_Item.item_width'))
					->add(
						Core::factory('Core_Html_Entity_Span')
						->class('input-group-addon dimension_patch')
						->value('×')
					);

				$oMainTab->move($this->getField('width'), $oMainRow9);

				$this->getField('height')
					->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2 col-xs-4 no-padding'))
					->caption(Core::_('Shop_Item.item_height'))
					->add(
						Core::factory('Core_Html_Entity_Span')
							->class('input-group-addon dimension_patch')
							->value(Core::_('Shop.size_measure_'.$oShop->size_measure))
					);
				$oMainTab->move($this->getField('height'), $oMainRow9);
			break;

			case 'shop_group':
				if (!$object->id)
				{
					$object->shop_id = Core_Array::getGet('shop_id');
					$object->parent_id = Core_Array::getGet('shop_group_id');
				}

				// Пропускаем поля, обработка которых будет вестись вручную ниже
				$this
					->addSkipColumn('image_large')
					->addSkipColumn('image_small')
					->addSkipColumn('image_large_width')
					->addSkipColumn('image_large_height')
					->addSkipColumn('image_small_width')
					->addSkipColumn('image_small_height')
					->addSkipColumn('subgroups_count')
					->addSkipColumn('subgroups_total_count')
					->addSkipColumn('items_count')
					->addSkipColumn('items_total_count')
					;

				parent::setObject($object);

				$template_id = $this->_object->Shop->Structure->template_id
					? $this->_object->Shop->Structure->template_id
					: 0;

				// Получаем стандартные вкладки
				$oMainTab = $this->getTab('main');
				$oAdditionalTab = $this->getTab('additional');

				// Добавляем новые вкладки
				$this->addTabAfter($oShopGroupDescriptionTab =
					Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Group.tab_group_description'))
					->name('Description'), $oMainTab);
				$this->addTabAfter($oShopGroupSeoTab = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Group.tab_group_seo'))
					->name('SEO'), $oShopGroupDescriptionTab);
				$this->addTabAfter($oShopGroupImportExportTab =
					Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Shop_Group.tab_yandex_market'))
					->name('ImportExport'), $oShopGroupSeoTab);

				$oPropertyTab = Admin_Form_Entity::factory('Tab')
					->caption(Core::_("Shop_Group.tab_properties"))
					->name('Property');

				$this->addTabBefore($oPropertyTab, $oAdditionalTab);

				// Properties
				Property_Controller_Tab::factory($this->_Admin_Form_Controller)
					->setObject($this->_object)
					->setDatasetId($this->getDatasetId())
					->linkedObject(Core_Entity::factory('Shop_Group_Property_List', $oShop->id))
					->setTab($oPropertyTab)
					->template_id($template_id)
					->fillTab();

				$oMainTab
					->add($oMainRow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow2 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow3 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow4 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow5 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow6 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oMainRow7 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopGroupDescriptionTab
					->add($oShopGroupDescriptionTabRow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopGroupDescriptionTabRow2 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopGroupSeoTab
					->add($oShopGroupSeoTabRow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopGroupSeoTabRow2 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oShopGroupSeoTabRow3 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				$oShopGroupImportExportTab
					->add($oShopGroupImportExportTabRow1 = Admin_Form_Entity::factory('Div')->class('row'))
				;

				// Перемещаем поля на их вкладки
				$oMainTab
					->move($oDescriptionField = $this->getField('description'),
					$oShopGroupDescriptionTab)
					->move($oSeoTitleField = $this->getField('seo_title'), $oShopGroupSeoTab)
					->move($oSeoDescriptionField = $this->getField('seo_description'),
					$oShopGroupSeoTab)
					->move($oSeoKeywordsField = $this->getField('seo_keywords'),
					$oShopGroupSeoTab)
					->move($oGuidField = $this->getField('guid'), $oShopGroupImportExportTab)
				;

				// Удаляем поле parent_id
				$oAdditionalTab->delete($this->getField('parent_id'));

				$oShopGroupParentSelect = Admin_Form_Entity::factory('Select');

				$oShopGroupParentSelect->caption(Core::_('Shop_Group.parent_id'))
					->options(array(' … ') + self::fillShopGroup($this->_object->shop_id, 0, array($this->_object->id)))
					->name('parent_id')
					->value($this->_object->parent_id);

				// Добавляем поле parent_id
				$oMainRow1->add($oShopGroupParentSelect);

				// Добавляем новое поле типа файл
				$oImageField = Admin_Form_Entity::factory('File');

				$oLargeFilePath = is_file($this->_object->getLargeFilePath())
					? $this->_object->getLargeFileHref()
					: '';

				$oSmallFilePath = is_file($this->_object->getSmallFilePath())
					? $this->_object->getSmallFileHref()
					: '';

				$sFormPath = $this->_Admin_Form_Controller->getPath();

				$windowId = $this->_Admin_Form_Controller->getWindowId();

				$oImageField
					->style("width: 400px;")
					->name("image")
					->id("image")
					->largeImage(array('max_width' => $oShop->group_image_large_max_width, 'max_height' => $oShop->group_image_large_max_height, 'path' => $oLargeFilePath, 'show_params' => TRUE, 'watermark_position_x' => $oShop->watermark_default_position_x, 'watermark_position_y' => $oShop->watermark_default_position_y, 'place_watermark_checkbox_checked' =>
						$oShop->watermark_default_use_large_image, 'delete_onclick' => "$.adminLoad({path: '{$sFormPath}', additionalParams:
						'hostcms[checked][{$this->_datasetId}][{$this->_object->id}]=1', action: 'deleteLargeImage', windowId: '{$windowId}'}); return false", 'caption' => Core::_('Shop_Group.items_catalog_image'), 'preserve_aspect_ratio_checkbox_checked' => $oShop->preserve_aspect_ratio_group))
					->smallImage(array('max_width' => $oShop->group_image_small_max_width, 'max_height' => $oShop->group_image_small_max_height, 'path' => $oSmallFilePath, 'create_small_image_from_large_checked' =>
						$this->_object->image_small == '', 'place_watermark_checkbox_checked' =>
						$oShop->watermark_default_use_small_image, 'delete_onclick' => "$.adminLoad({path: '{$sFormPath}', additionalParams:
						'hostcms[checked][{$this->_datasetId}][{$this->_object->id}]=1', action: 'deleteSmallImage', windowId: '{$windowId}'}); return false", 'caption' => Core::_('Shop_Group.items_catalog_image_small'), 'show_params' => TRUE, 'preserve_aspect_ratio_checkbox_checked' => $oShop->preserve_aspect_ratio_group_small));

				// Добавляем поле картинки группы товаров
				$oMainRow2->add($oImageField);

				$oMainTab
					->move($this->getField("sorting"), $oMainRow3)
					->move($this->getField("indexing"), $oMainRow4)
					->move($this->getField("active"), $oMainRow5);

				// Удаляем поле siteuser_group_id
				$oAdditionalTab->delete($this->getField('siteuser_group_id'));

				if (Core::moduleIsActive('siteuser'))
				{
					$oSiteuser_Controller_Edit = new Siteuser_Controller_Edit($this->_Admin_Form_Action);
					$aSiteuser_Groups = $oSiteuser_Controller_Edit->fillSiteuserGroups($this->_object->Shop->site_id);
				}
				else
				{
					$aSiteuser_Groups = array();
				}

				// Создаем поле групп пользователей сайта как выпадающий список
				$oSiteUserGroupSelect = Admin_Form_Entity::factory('Select');
				$oSiteUserGroupSelect
				->caption(Core::_("Shop_Item.siteuser_group_id"))
				->options(array(-1 => Core::_('Shop_Item.shop_users_group_parrent')) + $aSiteuser_Groups)
				->name('siteuser_group_id')
				->value($this->_object->siteuser_group_id);

				// Добавляем группы пользователей сайта
				$oMainRow6->add($oSiteUserGroupSelect);

				$oMainTab->move($this->getField("path"), $oMainRow7);

				$oDescriptionField = $this->getField("description")
					->wysiwyg(TRUE)
					->template_id($template_id);

				$oShopGroupDescriptionTab
					->move($this->getField("description"), $oShopGroupDescriptionTabRow1)
				;

				if (Core::moduleIsActive('typograph'))
				{
					$oDescriptionField->value(
						Typograph_Controller::instance()->eraseOpticalAlignment($oDescriptionField->value)
					);

					$oTypographField = Admin_Form_Entity::factory('Checkbox');

					$oTypographField
						->caption(Core::_("Shop_Group.exec_typograph_for_description"))
						->value(
							$oShop->typograph_default_items == 1 ? 1 : 0
						)
						->name("exec_typograph_for_description")
						->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6'))
					;

					$oShopGroupDescriptionTabRow2->add($oTypographField);

					// и "Оптическое выравнивание"
					$oOpticalAlignmentField = Admin_Form_Entity::factory('Checkbox');

					$oOpticalAlignmentField
						->caption(Core::_("Shop_Group.use_trailing_punctuation_for_text"))
						->name("use_trailing_punctuation_for_text")
						->value(
							$oShop->typograph_default_items == 1 ? 1 : 0
						)
						->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6'))
					;

					$oShopGroupDescriptionTabRow2->add($oOpticalAlignmentField);
				}

				$oShopGroupSeoTab->move($oSeoTitleField, $oShopGroupSeoTabRow1);
				$oShopGroupSeoTab->move($oSeoDescriptionField, $oShopGroupSeoTabRow2);
				$oShopGroupSeoTab->move($oSeoKeywordsField, $oShopGroupSeoTabRow3);


				$oShopGroupImportExportTab->move($oGuidField, $oShopGroupImportExportTabRow1);

				$oSeoDescriptionField->rows(5);
				$oSeoTitleField->rows(5);
				$oSeoKeywordsField->rows(5);

				// Выводим заголовок формы
				$title = $this->_object->id
					? Core::_("Shop_Group.groups_edit_form_title")
					: Core::_("Shop_Group.groups_add_form_title");

			break;
		}

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Shop_Item_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 * @return self
	 */
	protected function _applyObjectProperty()
	{
		$bNewObject = is_null($this->_object->id) && is_null(Core_Array::getPost('id'));

		parent::_applyObjectProperty();

		$oShop = $bNewObject
			? Core_Entity::factory('Shop', intval(Core_Array::getGet('shop_id', 0)))
			: $this->_object->Shop;

		$modelName = $this->_object->getModelName();

		switch ($modelName)
		{
			case 'shop_item':
				if ($this->_object->modification_id)
				{
					$this->_object->shop_group_id = 0;
				}

				// Проверяем подключен ли модуль типографики.
				if (Core::moduleIsActive('typograph'))
				{
					// Проверяем, нужно ли применять типографику к описанию
					if (Core_Array::getPost('exec_typograph_for_description', 0))
					{
						$this->_object->description = Typograph_Controller::instance()->process
						($this->_object->description, Core_Array::getPost('use_trailing_punctuation_for_description', 0));
					}

					// Проверяем, нужно ли применять типографику к тексту
					if (Core_Array::getPost('exec_typograph_for_text', 0))
					{
						$this->_object->text = Typograph_Controller::instance()->process
						($this->_object->text, Core_Array::getPost('use_trailing_punctuation_for_text', 0));
					}
				}

				if ($this->_object->start_datetime == '')
				{
					$this->_object->start_datetime = '0000-00-00 00:00:00';
				}

				if ($this->_object->end_datetime == '')
				{
					$this->_object->end_datetime = '0000-00-00 00:00:00';
				}

				// Обработка меток
				if (Core::moduleIsActive('tag'))
				{
					$item_tags = trim(Core_Array::getPost('tags'));

					if ($item_tags == '' && $oShop->apply_tags_automatically ||
						$oShop->apply_keywords_automatically && $this->_object->seo_keywords == '')
					{
						// Получаем хэш названия, описания и текста товара
						$array_text = Core_Str::getHashes(Core_Array::getPost('name') .
						Core_Array::getPost('description') . ' ' .
						Core_Array::getPost('text', ''), array('hash_function' => 'crc32'));
						$array_text = array_unique($array_text);

						$oTag = Core_Entity::factory('Tag');

						// Получаем список меток
						$aTags = $oTag->findAll();

						$coeff_intersect = array ();

						foreach($aTags as $oTag)
						{
							// Получаем хэш тэга
							$array_tags = Core_Str::getHashes($oTag->name, 	array('hash_function' => 'crc32'));

							// Получаем коэффициент схожести текста элемента с тэгом
							$array_tags = array_unique($array_tags);

							// Текст метки меньше текста инфоэлемента, т.к. должна
							// входить метка в текст инфоэлемента, а не наоборот
							if (count($array_text) >= count($array_tags))
							{
								// Расчитываем пересечение
								$intersect = count(array_intersect($array_text, $array_tags));

								$coefficient = count($array_tags) != 0
									? $intersect / count($array_tags)
									: 0;

								// Найдено полное вхождение
								if ($coefficient == 1 && !in_array($oTag->id, $coeff_intersect))
								{
									$coeff_intersect[] = $oTag->id;
								}
							}
						}
					}

					// Автоматическое применение ключевых слов
					if ($oShop->apply_keywords_automatically && $this->_object->seo_keywords == '')
					{
						// Найдено соответствие с тэгами
						if (count($coeff_intersect))
						{
							$aTmp = array();
							foreach ($coeff_intersect as $tag_id)
							{
								$oTag = Core_Entity::factory('Tag', $tag_id);
								$aTmp[] = $oTag->name;
							}

							$this->_object->seo_keywords = implode(',', $aTmp);
						}
					}
					if ($item_tags == '' && $oShop->apply_tags_automatically && count($coeff_intersect))
					{
						// Получаем список связей меток с товаром
						$this->_object->Tag_Shop_Items->deleteAll();

						// Вставка тэгов автоматически разрешена
						if (count($coeff_intersect) > 0)
						{
							foreach ($coeff_intersect as $tag_id)
							{
								$oTag = Core_Entity::factory('Tag', $tag_id);
								$this->_object->add($oTag);
							}
						}
					}
					else
					{
						$this->_object->applyTags($item_tags);
					}
				}

				// Дополнительные цены для групп пользователей
				$aAdditionalPrices = $this->_object->Shop->Shop_Prices->findAll();
				foreach($aAdditionalPrices as $oAdditionalPrice)
				{
					$oAdditionalPriceValue = $this->_object->Shop_Item_Prices->getByPriceId($oAdditionalPrice->id);

					if (is_null($oAdditionalPriceValue))
					{
						$oAdditionalPriceValue = Core_Entity::factory('Shop_Item_Price');
						$oAdditionalPriceValue->shop_item_id = $this->_object->id;
						$oAdditionalPriceValue->shop_price_id = $oAdditionalPrice->id;
					}

					if(!is_null(Core_Array::getPost("item_price_id_{$oAdditionalPrice->id}")))
					{
						$oAdditionalPriceValue->value = Core_Array::getPost("item_price_value_{$oAdditionalPrice->id}", 0);
						$oAdditionalPriceValue->save();
					}
					else
					{
						!is_null($oAdditionalPriceValue) && $oAdditionalPriceValue->delete();
					}
				}

				// Специальные цены, установленные значения
				$aShop_Specialprices = $this->_object->Shop_Specialprices->findAll();
				foreach($aShop_Specialprices as $oShop_Specialprice)
				{
					if(!is_null(Core_Array::getPost("specPrice_{$oShop_Specialprice->id}")))
					{
						$oShop_Specialprice
							->min_quantity(intval(Core_Array::getPost("specMinQuantity_{$oShop_Specialprice->id}", 0)))
							->max_quantity(intval(Core_Array::getPost("specMaxQuantity_{$oShop_Specialprice->id}", 0)))
							->price(Shop_Controller::instance()->convertPrice(Core_Array::getPost("specPrice_{$oShop_Specialprice->id}", 0)))
							->percent(Shop_Controller::instance()->convertPrice(Core_Array::getPost("specPercent_{$oShop_Specialprice->id}", 0)))
							->save();
					}
					else
					{
						$oShop_Specialprice->delete();
					}
				}

				// Специальные цены, новые значения
				$windowId = $this->_Admin_Form_Controller->getWindowId();
				$aSpecPrices = Core_Array::getPost('specPrice_');
				if ($aSpecPrices)
				{
					$aSpecMinQuantity = Core_Array::getPost('specMinQuantity_');
					$aSpecMaxQuantity = Core_Array::getPost('specMaxQuantity_');
					$aSpecPercent = Core_Array::getPost('specPercent_');

					foreach ($aSpecPrices as $key => $specPrice)
					{
						$price = Shop_Controller::instance()->convertPrice($specPrice);
						$percent = Shop_Controller::instance()->convertPrice(Core_Array::get($aSpecPercent, $key));

						if (!empty($price) || !empty($percent))
						{
							$oShop_Specialprice = Core_Entity::factory('Shop_Specialprice')
								->min_quantity(intval(Core_Array::get($aSpecMinQuantity, $key)))
								->max_quantity(intval(Core_Array::get($aSpecMaxQuantity, $key)))
								->price($price)
								->percent($percent);
							$this->_object->add($oShop_Specialprice);

							ob_start();
							Core::factory('Core_Html_Entity_Script')
								->type("text/javascript")
								->value("$(\"#{$windowId} input[name='specMinQuantity_\\[\\]']\").eq(0).prop('name', 'specMinQuantity_{$oShop_Specialprice->id}');
								$(\"#{$windowId} input[name='specMaxQuantity_\\[\\]']\").eq(0).prop('name', 'specMaxQuantity_{$oShop_Specialprice->id}');
								$(\"#{$windowId} input[name='specPrice_\\[\\]']\").eq(0).prop('name', 'specPrice_{$oShop_Specialprice->id}');
								$(\"#{$windowId} input[name='specPercent_\\[\\]']\").eq(0).prop('name', 'specPercent_{$oShop_Specialprice->id}');
								")
								->execute();

							$this->_Admin_Form_Controller->addMessage(ob_get_clean());
						}
					}
				}

				// Properties
				Shop_Item_Property_Controller_Tab::factory($this->_Admin_Form_Controller)
					->setObject($this->_object)
					->linkedObject(Core_Entity::factory('Shop_Item_Property_List', $oShop->id))
					->applyObjectProperty();

				// Обработка складов
				$aShopWarehouses = $oShop->Shop_Warehouses->findAll();

				foreach($aShopWarehouses as $oShopWarehouse)
				{
					$iWarehouseValue = Core_Array::getPost("warehouse_{$oShopWarehouse->id}", 0);

					$oShopItemWarehouse = $this->_object->Shop_Warehouse_Items->getByWarehouseId($oShopWarehouse->id);

					if(is_null($oShopItemWarehouse))
					{
						$oShopItemWarehouse = Core_Entity::factory('Shop_Warehouse_Item');

						$oShopItemWarehouse->shop_warehouse_id = $oShopWarehouse->id;

						$oShopItemWarehouse->shop_item_id = $this->_object->id;
					}

					$oShopItemWarehouse->count = $iWarehouseValue;

					$oShopItemWarehouse->save();
				}

				if (Core_Array::getPost('apply_price_for_modification'))
				{
					$aModifications = $this->_object->Modifications->findAll();
					foreach($aModifications as $oModification)
					{
						$oModification->price = $this->_object->price;
						$oModification->shop_currency_id = $this->_object->shop_currency_id;
						$oModification->save();
					}
				}
			break;
			case 'shop_group':
			default:

				// Проверяем подключен ли модуль типографики.
				if (Core::moduleIsActive('typograph'))
				{
					// Проверяем, нужно ли применять типографику к описанию информационной группы.
					if (Core_Array::getPost('exec_typograph_for_description', 0))
					{
						$this->_object->description =
						Typograph_Controller::instance()->process($this->_object->description, Core_Array::getPost('use_trailing_punctuation_for_text', 0));
					}
				}

				// Properties
				Property_Controller_Tab::factory($this->_Admin_Form_Controller)
					->setObject($this->_object)
					->linkedObject(Core_Entity::factory('Shop_Group_Property_List', $oShop->id))
					->applyObjectProperty();

				if ($bNewObject)
				{
					$aShop_Item_Property_For_Groups = Core_Entity::factory('Shop_Group', $this->_object->parent_id)->Shop_Item_Property_For_Groups->findAll();

					foreach($aShop_Item_Property_For_Groups as $oShop_Item_Property_For_Group)
					{
						$oShop_Item_Property_For_Group_new = clone $oShop_Item_Property_For_Group;
						$oShop_Item_Property_For_Group_new->shop_group_id = $this->_object->id;
						$oShop_Item_Property_For_Group_new->save();
					}
				}
		}

		// Clear tagged cache
		$this->_object->clearCache();

		// Обработка картинок
		$param = array();

		$large_image = $small_image = '';

		$aCore_Config = Core::$mainConfig;

		$create_small_image_from_large = Core_Array::getPost(
		'create_small_image_from_large_small_image');

		$bLargeImageIsCorrect =
			// Поле файла большого изображения существует
			!is_null($aFileData = Core_Array::getFiles('image', NULL))
			// и передан файл
			&& intval($aFileData['size']) > 0;

		if ($bLargeImageIsCorrect)
		{
			// Проверка на допустимый тип файла
			if (Core_File::isValidExtension($aFileData['name'], $aCore_Config['availableExtension']))
			{
				// Удаление файла большого изображения
				if ($this->_object->image_large)
				{
					// !! дописать метод
					$this->_object->deleteLargeImage();
				}

				$file_name = $aFileData['name'];

				// Не преобразовываем название загружаемого файла
				if (!$oShop->change_filename)
				{
					$large_image = $file_name;
				}
				else
				{
					// Определяем расширение файла
					$ext = Core_File::getExtension($aFileData['name']);
					//$large_image = 'information_groups_' . $this->_object->id . '.' . $ext;

					$large_image =
						($modelName == 'shop_item'
							? 'shop_items_catalog_image'
							: 'shop_group_image') . $this->_object->id . '.' . $ext;
				}
			}
			else
			{
				$this->addMessage(Core_Message::get(Core::_('Core.extension_does_not_allow', Core_File::getExtension($aFileData['name'])), 'error'));
			}
		}

		$aSmallFileData = Core_Array::getFiles('small_image', NULL);
		$bSmallImageIsCorrect =
			// Поле файла малого изображения существует
			!is_null($aSmallFileData)
			&& $aSmallFileData['size'];

		// Задано малое изображение и при этом не задано создание малого изображения
		// из большого или задано создание малого изображения из большого и
		// при этом не задано большое изображение.

		if ($bSmallImageIsCorrect || $create_small_image_from_large && $bLargeImageIsCorrect)
		{
			// Удаление файла малого изображения
			if ($this->_object->image_small)
			{
				// !! дописать метод
				$this->_object->deleteSmallImage();
			}

			// Явно указано малое изображение
			if ($bSmallImageIsCorrect
				&& Core_File::isValidExtension($aSmallFileData['name'],
				$aCore_Config['availableExtension']))
			{
				// Для инфогруппы ранее задано изображение
				if ($this->_object->image_large != '')
				{
					// Существует ли большое изображение
					$param['large_image_isset'] = true;
					$create_large_image = false;
				}
				else // Для информационной группы ранее не задано большое изображение
				{
					$create_large_image = empty($large_image);
				}

				$file_name = $aSmallFileData['name'];

				// Не преобразовываем название загружаемого файла
				if (!$oShop->change_filename)
				{
					if ($create_large_image)
					{
						$large_image = $file_name;
						$small_image = 'small_' . $large_image;
					}
					else
					{
						$small_image = $file_name;
					}
				}
				else
				{
					// Определяем расширение файла
					$ext = Core_File::getExtension($file_name);

					$small_image =
						($modelName == 'shop_item'
							? 'small_shop_items_catalog_image'
							: 'small_shop_group_image') . $this->_object->id . '.' . $ext;

				}
			}
			elseif ($create_small_image_from_large && $bLargeImageIsCorrect)
			{
				$small_image = 'small_' . $large_image;
			}
			// Тип загружаемого файла является недопустимым для загрузки файла
			else
			{
				$this->addMessage(Core_Message::get(Core::_('Core.extension_does_not_allow', Core_File::getExtension($aSmallFileData['name'])), 'error'));
			}
		}

		if ($bLargeImageIsCorrect || $bSmallImageIsCorrect)
		{
			if ($bLargeImageIsCorrect)
			{
				// Путь к файлу-источнику большого изображения;
				$param['large_image_source'] = $aFileData['tmp_name'];
				// Оригинальное имя файла большого изображения
				$param['large_image_name'] = $aFileData['name'];
			}

			if ($bSmallImageIsCorrect)
			{
				// Путь к файлу-источнику малого изображения;
				$param['small_image_source'] = $aSmallFileData['tmp_name'];
				// Оригинальное имя файла малого изображения
				$param['small_image_name'] = $aSmallFileData['name'];
			}

			if ($modelName == 'shop_group')
			{
			// Путь к создаваемому файлу большого изображения;
				$param['large_image_target'] = !empty($large_image)
					? $this->_object->getGroupPath() . $large_image
					: '';

				// Путь к создаваемому файлу малого изображения;
				$param['small_image_target'] = !empty($small_image)
					? $this->_object->getGroupPath() . $small_image
					: '' ;
			}
			else
			{
				// Путь к создаваемому файлу большого изображения;
				$param['large_image_target'] = !empty($large_image)
					? $this->_object->getItemPath() . $large_image
					: '';

				// Путь к создаваемому файлу малого изображения;
				$param['small_image_target'] = !empty($small_image)
					? $this->_object->getItemPath() . $small_image
					: '' ;
			}

			// Использовать большое изображение для создания малого
			$param['create_small_image_from_large'] = !is_null(Core_Array::getPost('create_small_image_from_large_small_image'));

			// Значение максимальной ширины большого изображения
			$param['large_image_max_width'] = Core_Array::getPost('large_max_width_image', 0);

			// Значение максимальной высоты большого изображения
			$param['large_image_max_height'] = Core_Array::getPost('large_max_height_image', 0);

			// Значение максимальной ширины малого изображения;
			$param['small_image_max_width'] = Core_Array::getPost('small_max_width_small_image');

			// Значение максимальной высоты малого изображения;
			$param['small_image_max_height'] = Core_Array::getPost('small_max_height_small_image');

			// Путь к файлу с "водяным знаком"
			$param['watermark_file_path'] = $oShop->getWatermarkFilePath();

			// Позиция "водяного знака" по оси X
			$param['watermark_position_x'] = Core_Array::getPost('watermark_position_x_image');

			// Позиция "водяного знака" по оси Y
			$param['watermark_position_y'] = Core_Array::getPost('watermark_position_y_image');

			// Наложить "водяной знак" на большое изображение (true - наложить (по умолчанию), false - не наложить);
			$param['large_image_watermark'] = !is_null(Core_Array::getPost('large_place_watermark_checkbox_image'));

			// Наложить "водяной знак" на малое изображение (true - наложить (по умолчанию), false - не наложить);
			$param['small_image_watermark'] = !is_null(Core_Array::getPost('small_place_watermark_checkbox_small_image'));

			// Сохранять пропорции изображения для большого изображения
			$param['large_image_preserve_aspect_ratio'] = !is_null(Core_Array::getPost('large_preserve_aspect_ratio_image'));

			// Сохранять пропорции изображения для малого изображения
			$param['small_image_preserve_aspect_ratio'] = !is_null(Core_Array::getPost('small_preserve_aspect_ratio_small_image'));

			$this->_object->createDir();

			$result = Core_File::adminUpload($param);

			if ($result['large_image'])
			{
				$this->_object->image_large = $large_image;
				$this->_object->setLargeImageSizes();
			}

			if ($result['small_image'])
			{
				$this->_object->image_small = $small_image;
				$this->_object->setSmallImageSizes();
			}
		}

		$this->_object->save();

		if (Core::moduleIsActive('search') && $this->_object->indexing && $this->_object->active)
		{
			Search_Controller::indexingSearchPages(array($this->_object->indexing()));
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 * @return boolean
	 */
	public function execute($operation = NULL)
	{
		if (!is_null($operation) && $operation != '')
		{
			$shop_id = Core_Array::getPost('shop_id');
			$path = Core_Array::getPost('path');

			if ($path == '')
			{
				$this->_object->name = Core_Array::getPost('name');
				$this->_object->path = Core_Array::getPost('path');
				$this->_object->makePath();
				$path = $this->_object->path;

				$this->addSkipColumn('path');
			}

			$modelName = $this->_object->getModelName();

			switch ($modelName)
			{
				case 'shop_item':
					$shop_group_id = Core_Array::getPost('shop_group_id');

					$oSameShopItem = Core_Entity::factory('Shop', $shop_id)->Shop_Items->getByGroupIdAndPath($shop_group_id, $path);

					if (!is_null($oSameShopItem) && $oSameShopItem->id != Core_Array::getPost('id'))
					{
						$this->addMessage(Core_Message::get(Core::_('Shop_Item.error_URL_shop_item'), 'error')
						);
						return TRUE;
					}

					$oSameShopGroup = Core_Entity::factory('Shop', $shop_id)->Shop_Groups->getByParentIdAndPath($shop_group_id, $path);

					if (!is_null($oSameShopGroup))
					{
						$this->addMessage(Core_Message::get(Core::_('Shop_Item.error_URL_isset_group') , 'error')
						);
						return TRUE;
					}
				break;
				case 'shop_group':
					$parent_id = Core_Array::getPost('parent_id');

					$oSameShopGroup = Core_Entity::factory('Shop', $shop_id)->Shop_Groups->getByParentIdAndPath($parent_id, $path);

					if (!is_null($oSameShopGroup) && $oSameShopGroup->id != Core_Array::getPost('id'))
					{
						$this->addMessage(
							Core_Message::get(Core::_('Shop_Group.error_URL_shop_group'), 'error')
						);
						return TRUE;
					}

					$oSameShopItem = Core_Entity::factory('Shop', $shop_id)->Shop_Items->getByGroupIdAndPath($parent_id, $path);

					if (!is_null($oSameShopItem))
					{
						$this->addMessage(
							Core_Message::get(Core::_('Shop_Group.error_URL_isset_item'), 'error')
						);
						return TRUE;
					}
				break;
			}
		}

		return parent::execute($operation);
	}

	/**
	 * Fill producers list
	 * @param int $iShopId shop ID
	 * @return array
	 */
	public function fillProducersList($iShopId)
	{
		$oShopProducer = Core_Entity::factory('Shop_Producer');

		!$iShopId && $iShopId = Core_Entity::factory('Shop_Item', intval(Core_Array::getGet('shop_item_id', 0)))->Shop->id;

		$oShopProducer->queryBuilder()
			->where("shop_id", "=", $iShopId);

		$aReturn = array(" … ");

		$aShopProducers = $oShopProducer->findAll();
		foreach ($aShopProducers as $oShopProducer)
		{
			$aReturn[$oShopProducer->id] = $oShopProducer->name;
		}

		return $aReturn;
	}

	/**
	 * Fill taxes list
	 * @return array
	 */
	public function fillTaxesList()
	{
		$oTax = Core_Entity::factory('Shop_Tax');

		$oTax
			->queryBuilder()
			->orderBy('id');

		$aTaxes = $oTax->findAll();

		$aReturn = array(' … ');

		foreach($aTaxes as $oTax)
		{
			$aReturn[$oTax->id] = $oTax->name;
		}

		return $aReturn;
	}

	/**
	 * Fill sellers list
	 * @return array
	 */
	protected function _fillSellersList()
	{
		$oShopSeller = Core_Entity::factory('Shop_Seller');

		$iShopId = intval(Core_Array::getGet('shop_id', 0));

		!$iShopId && $iShopId = Core_Entity::factory('Shop_Item', intval(Core_Array::getGet('shop_item_id', 0)))->Shop->id;

		$oShopSeller->queryBuilder()
			->where("shop_id", "=", $iShopId);

		$aReturn = array(" … ");

		$aShopSellers = $oShopSeller->findAll();
		foreach ($aShopSellers as $oShopSeller)
		{
			$aReturn[$oShopSeller->id] = $oShopSeller->name;
		}

		return $aReturn;
	}

	/**
	 * Fill modifications list
	 * @param Shop_Item_Model $oShopItem item
	 * @return array
	 */
	protected function _fillModificationList($oShopItem)
	{
		// Ограничение списка модификаций
		$iModificationsLimit = 250;

		$aReturnArray = array(' … ');

		// Если это модификация - её основной товар в любом случае должен быть в списке
		if ($oShopItem->modification_id)
		{
			$aReturnArray[$oShopItem->Modification->id] = $oShopItem->Modification->name;
			$iModificationsLimit--;
		}

		if (!$oShopItem->id)
		{
			if (intval(Core_Array::getGet('shop_item_id', 0)))
			{
				$oShopItemParent = Core_Entity::factory('Shop_Item', Core_Array::getGet('shop_item_id', 0));

				$iShopId = $oShopItemParent->Shop->id;
				$iShopGroupId = $oShopItemParent->Shop_Group->id;
			}
			else
			{
				$iShopId = intval(Core_Array::getGet('shop_id', 0));
				$iShopGroupId = intval(Core_Array::getGet('shop_group_id', 0));
			}
		}
		else
		{
			$iShopGroupId = $oShopItem->modification_id
				? $oShopItem->Modification->Shop_Group->id
				: $oShopItem->Shop_Group->id;

			$iShopId = $oShopItem->Shop->id;
		}

		$oShopItemTemp = Core_Entity::factory('Shop_Item');

		$oShopItemTemp
			->queryBuilder()
			// товары этой же группы
			->where('shop_group_id', '=', (int)$iShopGroupId)
			// этого же магазина
			->where('shop_id', '=', (int)$iShopId)
			// не модификации
			->where('modification_id', '=', 0)
			->limit($iModificationsLimit);

		$aShopItems = $oShopItemTemp->findAll(FALSE);
		foreach($aShopItems as $oShop_Item)
		{
			$oShop_Item->id != $oShopItem->id && $aReturnArray[$oShop_Item->id] = $oShop_Item->name;
		}

		return $aReturnArray;
	}

	/**
	 * Shop groups tree
	 * @var array
	 */
	static protected $_aGroupTree = array();

	/**
	 * Build visual representation of group tree
	 * @param int $iShopId shop ID
	 * @param int $iShopGroupParentId parent ID
	 * @param int $aExclude exclude group ID
	 * @param int $iLevel current nesting level
	 * @return array
	 */
	static public function fillShopGroup($iShopId, $iShopGroupParentId = 0, $aExclude = array(), $iLevel = 0)
	{
		$iShopId = intval($iShopId);
		$iShopGroupParentId = intval($iShopGroupParentId);
		$iLevel = intval($iLevel);

		if ($iLevel == 0)
		{
			$aTmp = Core_QueryBuilder::select('id', 'parent_id', 'name')
				->from('shop_groups')
				->where('shop_id', '=', $iShopId)
				->where('deleted', '=', 0)
				->orderBy('sorting')
				->orderBy('name')
				->execute()->asAssoc()->result();

			foreach ($aTmp as $aGroup)
			{
				self::$_aGroupTree[$aGroup['parent_id']][] = $aGroup;
			}
		}

		$aReturn = array();

		if (isset(self::$_aGroupTree[$iShopGroupParentId]))
		{
			$countExclude = count($aExclude);
			foreach (self::$_aGroupTree[$iShopGroupParentId] as $childrenGroup)
			{
				if ($countExclude == 0 || !in_array($childrenGroup['id'], $aExclude))
				{
					$aReturn[$childrenGroup['id']] = str_repeat('  ', $iLevel) . $childrenGroup['name'];
					$aReturn += self::fillShopGroup($iShopId, $childrenGroup['id'], $aExclude, $iLevel + 1);
				}
			}
		}

		$iLevel == 0 && self::$_aGroupTree = array();

		return $aReturn;
	}

	/**
	 * Show plus button
	 * @param string $function function name
	 * @return string
	 */
	protected function _getImgAdd($function = '$.cloneSpecialPrice')
	{
		$windowId = $this->_Admin_Form_Controller->getWindowId();

		ob_start();
		Core::factory('Core_Html_Entity_Img')
			->src('/admin/images/action_add.gif')
			->id('add')
			->class('pointer left5px img_line')
			->onclick("{$function}('{$windowId}', this)")
			->execute();

		return Admin_Form_Entity::factory('Code')->html(ob_get_clean());
	}

	/**
	 * Show minus button
	 * @param string $onclick onclick attribute value
	 * @return string
	 */
	protected function _getImgDelete($onclick = '$.deleteNewSpecialprice(this)')
	{
		ob_start();
		Core::factory('Core_Html_Entity_Img')
			->src('/admin/images/action_delete.gif')
			->id('delete')
			->class('pointer left5px img_line')
			->onclick($onclick)
			->execute();

		return Admin_Form_Entity::factory('Code')->html(ob_get_clean());
	}

	protected function _imgBox($addFunction = '$.cloneSpecialPrice', $deleteOnclick = '$.deleteNewSpecialprice(this)')
	{
		$windowId = $this->_Admin_Form_Controller->getWindowId();

		ob_start();
			Admin_Form_Entity::factory('Div')
				->class('no-padding add-remove-property margin-top-20 pull-left')
				->add(
					Admin_Form_Entity::factory('Div')
						->class('btn btn-palegreen')
						->add(Admin_Form_Entity::factory('Code')->html('<i class="fa fa-plus-circle close"></i>'))
						->onclick("{$addFunction}('{$windowId}', this);")
				)
				->add(
					Admin_Form_Entity::factory('Div')
						->class('btn btn-darkorange btn-delete')
						->add(Admin_Form_Entity::factory('Code')->html('<i class="fa fa-minus-circle close"></i>'))
						->onclick($deleteOnclick)
				)
				->execute();

		return Admin_Form_Entity::factory('Code')->html(ob_get_clean());
	}
}