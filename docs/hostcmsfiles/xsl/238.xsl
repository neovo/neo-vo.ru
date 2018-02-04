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
<div class="sli">
<div id="sli">
<xsl:apply-templates select="informationsystem_item"/>
</div>

</div>
</xsl:template>

<!-- Шаблон вывода информационного элемента -->
<xsl:template match="informationsystem_item">
<div class="about" style="background: url({dir}{image_large}) no-repeat 50% 0; background-size: 100%;">
	<!-- <div class="wrapBtnProduct"><a href="/catalog/#here" class="btnProduct">Ознакомиться с продукцией</a></div> //-->
</div>
</xsl:template>
</xsl:stylesheet>