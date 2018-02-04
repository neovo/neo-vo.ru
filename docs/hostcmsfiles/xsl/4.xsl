<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">

	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>

	<!-- ВыводЕдиницыИнформационнойСистемы  -->

	<xsl:template match="/">
		<xsl:apply-templates select="/informationsystem/informationsystem_item"/>
	</xsl:template>

	<xsl:template match="/informationsystem/informationsystem_item">

		<!-- Получаем ID родительской группы и записываем в переменную $group -->
		<!-- <xsl:variable name="group" select="informationsystem_group_id"/> -->

		<h1 hostcms:id="{@id}" hostcms:field="name" hostcms:entity="informationsystem_item"><xsl:value-of disable-output-escaping="yes" select="name"/></h1>

				<xsl:value-of disable-output-escaping="yes" select="text"/>
			
	</xsl:template>
</xsl:stylesheet>