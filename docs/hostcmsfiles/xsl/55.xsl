<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:hostcms="http://www.hostcms.ru/"
exclude-result-prefixes="hostcms">
<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>

<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>

<xsl:template match="/">
<xsl:apply-templates select="/shop"/>
</xsl:template>

<xsl:variable name="n" select="number(3)"/>

<xsl:template match="/shop">

<!-- Получаем ID родительской группы и записываем в переменную $group -->
<xsl:variable name="group" select="group"/>

<xsl:variable name="count">1</xsl:variable>


<!-- дополнение пути для action, если выбрана метка -->
<xsl:variable name="form_tag_url"><xsl:if test="count(tag) = 1">tag/<xsl:value-of select="tag/urlencode"/>/</xsl:if></xsl:variable>

<xsl:variable name="path"><xsl:choose>
<xsl:when test="/shop//shop_group[@id=$group]/node()"><xsl:value-of select="/shop//shop_group[@id=$group]/url"/></xsl:when>
<xsl:otherwise><xsl:value-of select="/shop/url"/></xsl:otherwise>
</xsl:choose></xsl:variable>

<form method="get" action="{$path}{$form_tag_url}">
<ul class="prodItems">
<xsl:choose>
<xsl:when test="$group = 0">
<h1>
<xsl:value-of disable-output-escaping="yes" select="name"/>
</h1>
</xsl:when>
<xsl:otherwise>
<h1>
<xsl:value-of disable-output-escaping="yes" select=".//shop_group[@id=$group]/name"/>
</h1>
</xsl:otherwise>
</xsl:choose>

<xsl:choose>
	<xsl:when test="count(tag) = 0 and count(shop_producer) = 0 and count(//shop_group[parent_id=$group]) &gt; 0">
		<xsl:if test="$group!=609">
			<xsl:apply-templates select=".//shop_group[parent_id=$group]"/>
		</xsl:if>
		<xsl:if test="$group=609">
			<xsl:apply-templates select="shop_item[@id=329]" />
			<xsl:apply-templates select="shop_item[@id=328]" />
			<xsl:apply-templates select="shop_item[@id=327]" />
		</xsl:if>
	</xsl:when>
	<xsl:otherwise>
		<xsl:apply-templates select="shop_item" />
		
		<li class="empty"></li>
		<li class="empty"></li>
		<xsl:if test="total &gt; 0 and limit &gt; 0">

		<xsl:variable name="count_pages" select="ceiling(total div limit)"/>

		<xsl:variable name="visible_pages" select="5"/>

		<xsl:variable name="real_visible_pages"><xsl:choose>
		<xsl:when test="$count_pages &lt; $visible_pages"><xsl:value-of select="$count_pages"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="$visible_pages"/></xsl:otherwise>
		</xsl:choose></xsl:variable>

		<!-- Считаем количество выводимых ссылок перед текущим элементом -->
		<xsl:variable name="pre_count_page"><xsl:choose>
		<xsl:when test="page - (floor($real_visible_pages div 2)) &lt; 0">
			<xsl:value-of select="page"/>
		</xsl:when>
		<xsl:when test="($count_pages - page - 1) &lt; floor($real_visible_pages div 2)">
			<xsl:value-of select="$real_visible_pages - ($count_pages - page - 1) - 1"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="round($real_visible_pages div 2) = $real_visible_pages div 2">
					<xsl:value-of select="floor($real_visible_pages div 2) - 1"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="floor($real_visible_pages div 2)"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
		</xsl:choose></xsl:variable>

		<!-- Считаем количество выводимых ссылок после текущего элемента -->
		<xsl:variable name="post_count_page"><xsl:choose>
		<xsl:when test="0 &gt; page - (floor($real_visible_pages div 2) - 1)">
			<xsl:value-of select="$real_visible_pages - page - 1"/>
		</xsl:when>
		<xsl:when test="($count_pages - page - 1) &lt; floor($real_visible_pages div 2)">
			<xsl:value-of select="$real_visible_pages - $pre_count_page - 1"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$real_visible_pages - $pre_count_page - 1"/>
		</xsl:otherwise>
		</xsl:choose></xsl:variable>

		<xsl:variable name="i"><xsl:choose>
		<xsl:when test="page + 1 = $count_pages"><xsl:value-of select="page - $real_visible_pages + 1"/></xsl:when>
		<xsl:when test="page - $pre_count_page &gt; 0"><xsl:value-of select="page - $pre_count_page"/></xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>

		<div class="pages">
		<xsl:call-template name="for">
		<xsl:with-param name="limit" select="limit"/>
		<xsl:with-param name="page" select="page"/>
		<xsl:with-param name="items_count" select="total"/>
		<xsl:with-param name="i" select="$i"/>
		<xsl:with-param name="post_count_page" select="$post_count_page"/>
		<xsl:with-param name="pre_count_page" select="$pre_count_page"/>
		<xsl:with-param name="visible_pages" select="$real_visible_pages"/>
		</xsl:call-template>
		</div>
		</xsl:if>
		
	</xsl:otherwise>
</xsl:choose>

<li class="empty"></li>

<xsl:choose>
			<xsl:when test="$group = 0">
				<!-- Описание выводится при отсутствии фильтрации по тэгам -->
				<xsl:if test="count(tag) = 0 and page = 0 and description != ''">
					<div class="desc"><xsl:value-of disable-output-escaping="yes" select="description"/></div>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>

				<!-- Описание выводим только на первой странице -->
				<xsl:if test="page = 0 and .//shop_group[@id=$group]/description != ''">
					<div class="desc"><xsl:value-of disable-output-escaping="yes" select=".//shop_group[@id=$group]/description"/></div>
				</xsl:if>

			</xsl:otherwise>
		</xsl:choose>

</ul>
</form>

</xsl:template>

<!-- Шаблон для товара -->
<xsl:template match="shop_item">
<li class="item">
<xsl:if test="position() mod 3 = 0">
				<xsl:attribute name="class">item three</xsl:attribute>
			</xsl:if>
<div class="itemImg">
<xsl:choose>
<xsl:when test="image_small != ''">
	<a href="{url}#here"><img src="{dir}{image_small}" alt="{name}" /></a>
</xsl:when>
<xsl:otherwise>
<img src="/templates/template1/img/noimage.jpg" alt="{name}" />
</xsl:otherwise>
</xsl:choose>
</div>
<div class="itemName">
<xsl:value-of disable-output-escaping="yes" select="name"/>
</div>
<div class="itemLink">
	<div class="wrapLink">
		<a href="{url}#here">Характеристики и цены</a>
	</div>
</div>
</li>
</xsl:template>

<!-- Шаблон для групп товара -->
<xsl:template match="shop_group">
<li class="item">
	<xsl:if test="position() mod 3 = 0">
				<xsl:attribute name="class">item three</xsl:attribute>
			</xsl:if>
<div class="itemImg">
<xsl:choose>
<xsl:when test="image_small != ''">
	<a href="{url}#here"><img src="{dir}{image_small}" alt="{name}" /></a>
</xsl:when>
<xsl:otherwise>
<img src="/templates/template1/img/noimage.jpg" alt="{name}" />
</xsl:otherwise>
</xsl:choose>
</div>
<div class="itemName">
<xsl:value-of disable-output-escaping="yes" select="name"/>
</div>
<div class="itemLink">
	<div class="wrapLink">
		<a href="{url}#here">Смотреть все товары</a>
	</div>
</div>
</li>

</xsl:template>

<!-- Шаблон выводит рекурсивно ссылки на группы магазина -->
<xsl:template match="shop_group" mode="breadCrumbs">
<xsl:param name="parent_id" select="parent_id"/>

<!-- Получаем ID родительской группы и записываем в переменную $group -->
<xsl:param name="group" select="/shop/shop_group"/>

<xsl:apply-templates select="//shop_group[@id=$parent_id]" mode="breadCrumbs"/>

<xsl:if test="parent_id=0">
<a href="{/shop/url}" hostcms:id="{/shop/@id}" hostcms:field="name" hostcms:entity="shop">
<xsl:value-of select="/shop/name"/>
</a>
</xsl:if>

<span><xsl:text> → </xsl:text></span>

<a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group">
<xsl:value-of disable-output-escaping="yes" select="name"/>
</a>
</xsl:template>

<!-- Шаблон для списка товаров для сравнения -->
<xsl:template match="compare_items/compare_item">
<xsl:variable name="var_compare_id" select="."/>
<tr>
<td>
<input type="checkbox" name="del_compare_id_{compare_item_id}" id="id_del_compare_id_{compare_item_id}"/>
</td>
<td>
<a href="{/shop/url}{compare_item_url}{compare_url}/">
<xsl:value-of disable-output-escaping="yes" select="compare_name"/>
</a>
</td>
</tr>
</xsl:template>

<!-- Шаблон для фильтра по дополнительным свойствам -->
<xsl:template match="property" mode="propertyList">
<xsl:variable name="nodename">property_<xsl:value-of select="@id"/></xsl:variable>
<xsl:variable name="nodename_from">property_<xsl:value-of select="@id"/>_from</xsl:variable>
<xsl:variable name="nodename_to">property_<xsl:value-of select="@id"/>_to</xsl:variable>

<div class="filterField">

<xsl:if test="filter != 5">
<legend><xsl:value-of disable-output-escaping="yes" select="name"/><xsl:text> </xsl:text></legend>
</xsl:if>

<xsl:choose>
<!-- Отображаем поле ввода -->
<xsl:when test="filter = 1">
<br/>
<input type="text" name="property_{@id}">
<xsl:if test="/shop/*[name()=$nodename] != ''">
<xsl:attribute name="value"><xsl:value-of select="/shop/*[name()=$nodename]"/></xsl:attribute>
</xsl:if>
</input>
</xsl:when>
<!-- Отображаем список -->
<xsl:when test="filter = 2">
<br/>
<select name="property_{@id}">
<option value="0">...</option>
<xsl:apply-templates select="list/list_item"/>
</select>
</xsl:when>
<!-- Отображаем переключатели -->
<xsl:when test="filter = 3">
<br/>
<div class="propertyInput">
<input type="radio" name="property_{@id}" value="0" id="id_prop_radio_{@id}_0"></input>
<label for="id_prop_radio_{@id}_0">Любой вариант</label>
<xsl:apply-templates select="list/list_item"/>
</div>
</xsl:when>
<!-- Отображаем флажки -->
<xsl:when test="filter = 4">
<div class="propertyInput">
<xsl:apply-templates select="list/list_item"/>
</div>
</xsl:when>
<!-- Отображаем флажок -->
<xsl:when test="filter = 5">
<input type="checkbox" name="property_{@id}" id="property_{@id}" style="padding-top:4px">
<xsl:if test="/shop/*[name()=$nodename] != ''">
<xsl:attribute name="checked"><xsl:value-of select="/shop/*[name()=$nodename]"/></xsl:attribute>
</xsl:if>
</input>
<label for="property_{@id}">
<xsl:value-of disable-output-escaping="yes" select="name"/><xsl:text> </xsl:text>
</label>
</xsl:when>
<!-- Отображение полей "от и до" -->
<xsl:when test="filter = 6">
<br/>
от: <input type="text" name="property_{@id}_from" size="2" value="{/shop/*[name()=$nodename_from]}"/> до: <input type="text" name="property_{@id}_to" size="2" value="{/shop/*[name()=$nodename_to]}"/>
</xsl:when>
<!-- Отображаем список с множественным выбором-->
<xsl:when test="filter = 7">
<br/>
<select name="property_{@id}[]" multiple="multiple">
<xsl:apply-templates select="list/list_item"/>
</select>
</xsl:when>
</xsl:choose>
</div>
</xsl:template>

<xsl:template match="list/list_item">
<xsl:if test="../../filter = 2">
<!-- Отображаем список -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<option value="{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
<xsl:value-of disable-output-escaping="yes" select="value"/>
</option>
</xsl:if>
<xsl:if test="../../filter = 3">
<!-- Отображаем переключатели -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<br/>
<input type="radio" name="property_{../../@id}" value="{@id}" id="id_property_{../../@id}_{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
</input>
<label for="id_property_{../../@id}_{@id}">
<xsl:value-of disable-output-escaping="yes" select="value"/>
</label>
</xsl:if>
<xsl:if test="../../filter = 4">
<!-- Отображаем флажки -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<br/>
<input type="checkbox" value="{@id}" name="property_{../../@id}[]" id="property_{../../@id}_{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
<label for="property_{../../@id}_{@id}">
<xsl:value-of disable-output-escaping="yes" select="value"/>
</label>
</input>
</xsl:if>
<xsl:if test="../../filter = 7">
<!-- Отображаем список -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<option value="{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id">
<xsl:attribute name="selected">
</xsl:attribute>
</xsl:if>
<xsl:value-of disable-output-escaping="yes" select="value"/>
</option>
</xsl:if>
</xsl:template>

<!-- Метки для товаров -->
<xsl:template match="tag">
<a href="{/shop/url}tag/{urlencode}/" class="tag">
<xsl:value-of select="tag_name"/>
</a>
<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
</xsl:template>

<!-- Цикл для вывода строк ссылок -->
<xsl:template name="for">

<xsl:param name="limit"/>
<xsl:param name="page"/>
<xsl:param name="pre_count_page"/>
<xsl:param name="post_count_page"/>
<xsl:param name="i" select="0"/>
<xsl:param name="items_count"/>
<xsl:param name="visible_pages"/>

<xsl:variable name="n" select="ceiling($items_count div $limit)"/>

<xsl:variable name="start_page"><xsl:choose>
<xsl:when test="$page + 1 = $n"><xsl:value-of select="$page - $visible_pages + 1"/></xsl:when>
<xsl:when test="$page - $pre_count_page &gt; 0"><xsl:value-of select="$page - $pre_count_page"/></xsl:when>
<xsl:otherwise>0</xsl:otherwise>
</xsl:choose></xsl:variable>

<xsl:if test="$i = $start_page and $page != 0">
</xsl:if>

<xsl:if test="$i = ($page + $post_count_page + 1) and $n != ($page+1)">
</xsl:if>

<!-- Передаем фильтр -->
<xsl:variable name="filter"><xsl:if test="/shop/filter/node()">?filter=1&amp;sorting=<xsl:value-of select="/shop/sorting"/>&amp;price_from=<xsl:value-of select="/shop/price_from"/>&amp;price_to=<xsl:value-of select="/shop/price_to"/><xsl:for-each select="/shop/*"><xsl:if test="starts-with(name(), 'property_')">&amp;<xsl:value-of select="name()"/>[]=<xsl:value-of select="."/></xsl:if></xsl:for-each></xsl:if></xsl:variable>

<xsl:variable name="on_page"><xsl:if test="/shop/on_page/node() and /shop/on_page > 0"><xsl:choose><xsl:when test="/shop/filter/node()">&amp;</xsl:when><xsl:otherwise>?</xsl:otherwise></xsl:choose>on_page=<xsl:value-of select="/shop/on_page"/></xsl:if></xsl:variable>

<xsl:if test="$items_count &gt; $limit and ($page + $post_count_page + 1) &gt; $i">
<!-- Заносим в переменную $group идентификатор текущей группы -->
<xsl:variable name="group" select="/shop/group"/>

<!-- Путь для тэга -->
<xsl:variable name="tag_path"><xsl:if test="count(/shop/tag) != 0">tag/<xsl:value-of select="/shop/tag/urlencode"/>/</xsl:if></xsl:variable>

<!-- Путь для сравнения товара -->
<xsl:variable name="shop_producer_path"><xsl:if test="count(/shop/shop_producer)">producer-<xsl:value-of select="/shop/shop_producer/@id"/>/</xsl:if></xsl:variable>

<!-- Определяем группу для формирования адреса ссылки -->
<xsl:variable name="group_link"><xsl:choose><xsl:when test="$group != 0"><xsl:value-of select="/shop//shop_group[@id=$group]/url"/></xsl:when><xsl:otherwise><xsl:value-of select="/shop/url"/></xsl:otherwise></xsl:choose></xsl:variable>

<!-- Определяем адрес ссылки -->
<xsl:variable name="number_link"><xsl:if test="$i != 0">page-<xsl:value-of select="$i + 1"/>/</xsl:if></xsl:variable>

<!-- Выводим ссылку на первую страницу -->
<xsl:if test="$page - $pre_count_page &gt; 0 and $i = $start_page">
<a href="{$group_link}{$tag_path}{$shop_producer_path}{$filter}{$on_page}#here">←</a>
</xsl:if>

<!-- Ставим ссылку на страницу-->
<xsl:if test="$i != $page">
<xsl:if test="($page - $pre_count_page) &lt;= $i and $i &lt; $n">
<!-- Выводим ссылки на видимые страницы -->
<a href="{$group_link}{$number_link}{$tag_path}{$shop_producer_path}{$filter}{$on_page}#here">
<xsl:value-of select="$i + 1"/>
</a>
</xsl:if>

<!-- Выводим ссылку на последнюю страницу -->
<xsl:if test="$i+1 &gt;= ($page + $post_count_page + 1) and $n &gt; ($page + 1 + $post_count_page)">
<!-- Выводим ссылку на последнюю страницу -->
<a href="{$group_link}page-{$n}/{$tag_path}{$shop_producer_path}{$filter}{$on_page}#here">→</a>
</xsl:if>
</xsl:if>

<!-- Ссылка на предыдущую страницу для Ctrl + влево -->
<xsl:if test="$page != 0 and $i = $page"><xsl:variable name="prev_number_link"><xsl:if test="$page &gt; 1">page-<xsl:value-of select="$i"/>/</xsl:if></xsl:variable></xsl:if>

<!-- Ссылка на следующую страницу для Ctrl + вправо -->
<xsl:if test="($n - 1) > $page and $i = $page">

</xsl:if>

<!-- Не ставим ссылку на страницу-->
<xsl:if test="$i = $page">
<span>
<xsl:value-of select="$i+1"/>
</span>
</xsl:if>

<!-- Рекурсивный вызов шаблона. НЕОБХОДИМО ПЕРЕДАВАТЬ ВСЕ НЕОБХОДИМЫЕ ПАРАМЕТРЫ! -->
<xsl:call-template name="for">
<xsl:with-param name="i" select="$i + 1"/>
<xsl:with-param name="limit" select="$limit"/>
<xsl:with-param name="page" select="$page"/>
<xsl:with-param name="items_count" select="$items_count"/>
<xsl:with-param name="pre_count_page" select="$pre_count_page"/>
<xsl:with-param name="post_count_page" select="$post_count_page"/>
<xsl:with-param name="visible_pages" select="$visible_pages"/>
</xsl:call-template>
</xsl:if>
</xsl:template>

<!-- Шаблон для фильтра производителей -->
<xsl:template match="producers/shop_producer">
<!-- Заносим в переменную $group идентификатор текущей группы -->
<xsl:variable name="group" select="/shop/group"/>

<!-- Определяем группу для формирования адреса ссылки -->
<xsl:variable name="group_link"><xsl:choose><xsl:when test="$group != 0"><xsl:value-of select="/shop//shop_group[@id=$group]/url"/></xsl:when><xsl:otherwise><xsl:value-of select="/shop/url"/></xsl:otherwise></xsl:choose></xsl:variable>

<a href="{$group_link}?producer_id={@id}"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
</xsl:template>
</xsl:stylesheet>