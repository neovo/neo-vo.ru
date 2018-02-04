<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:hostcms="http://www.hostcms.ru/"
exclude-result-prefixes="hostcms">
<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict"
doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
encoding="utf-8" indent="yes" method="html"
omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
<!-- МагазинГруппыТоваровНаГлавной -->
<xsl:template match="/">
<xsl:apply-templates select="/shop"/>
</xsl:template>
<!-- Шаблон для магазина -->
<xsl:template match="/shop">
<h2>Каталог</h2>
<ul class="leftMenu">
<xsl:apply-templates select="shop_group"/>
</ul>
</xsl:template>
<!-- Шаблон для групп товара -->
<xsl:template match="shop_group">
<xsl:variable name="current_group_id" select="/shop/ТекущаяГруппа"/>
<li>
<a href="{url}#here" class="parent"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
<xsl:if test="shop_group">
<ul class="submenu">
<xsl:apply-templates select="shop_group" mode="sub"/>
</ul>
</xsl:if>
</li>
</xsl:template>

<!-- Шаблон для групп товара -->
<xsl:template match="shop_group" mode="sub">
<xsl:variable name="current_group_id" select="/shop/ТекущаяГруппа"/>
<li>
<xsl:choose>
<xsl:when test="$current_group_id = @id or count(.//shop_group[@id=$current_group_id])=1">
<a href="{url}#here"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
</xsl:when>
<xsl:otherwise>
<a href="{url}#here"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
</xsl:otherwise>
</xsl:choose>
</li>
</xsl:template>
</xsl:stylesheet>