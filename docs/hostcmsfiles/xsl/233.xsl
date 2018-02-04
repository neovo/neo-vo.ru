<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:hostcms="http://www.hostcms.ru/"
exclude-result-prefixes="hostcms">
<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
<!-- СписокЭлементовИнфосистемы -->
<xsl:template match="/">
<xsl:apply-templates select="/informationsystem"/>
</xsl:template>
<xsl:template match="/informationsystem">
<!-- Получаем ID родительской группы и записываем в переменную $group -->
<xsl:variable name="group" select="group"/>
<div class="title"><xsl:value-of disable-output-escaping="yes" select="name"/></div>
<a href="{url}#here" class="link">все новости</a>
<xsl:apply-templates select="informationsystem_item"/>
</xsl:template>
<!-- Шаблон вывода информационного элемента -->
<xsl:template match="informationsystem_item">
<article>
<a href="{url}#here" class="pressArticleTitle"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
<div class="pressArticleDescr"><xsl:value-of disable-output-escaping="yes" select="description"/></div>
<a href="{url}#here" class="pressArticleLink">Читать полностью</a>
</article>
</xsl:template>
</xsl:stylesheet>