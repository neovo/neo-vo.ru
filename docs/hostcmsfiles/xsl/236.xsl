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
<xsl:if test="count(tag) = 0 and count(shop_producer) = 0 and count(//shop_group[parent_id=$group]) &gt; 0">

				<xsl:apply-templates select=".//shop_group[parent_id=$group]"/>

		</xsl:if>
						<li class="empty"></li>
						<li class="empty"></li>
						<li class="empty"></li>
</ul>
</form>

</xsl:template>

<!-- Шаблон для товара -->
<xsl:template match="shop_group">
<xsl:if test="property_value[tag_name='pop']/value = 1">
<li class="item">
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
</xsl:if>
</xsl:template>
</xsl:stylesheet>