<?php

require_once '/home/laaknor/mysql_config.php';
#require_once 'shared_config.php';
require_once '/home/laaknor/bin/geo_param.php';
require_once 'mapsources.php';

$q1 = mysql_connect("sql-s2", $sql_username, $sql_password) or die(mysql_error());
$q1db = mysql_select_db("toolserver") or die(mysql_error());
$db = $argv[1];

$qFindDB = mysql_query("SELECT server,dbname,domain,lang FROM wiki WHERE dbname LIKE '$db' OR dbname LIKE '".$db."_p'") or die(mysql_error());
$rFindDB = mysql_fetch_object($qFindDB) or die(mysql_error());


mysql_connect("sql-s".$rFindDB->server, $sql_username, $sql_password) or die(mysql_error());
mysql_select_db($rFindDB->dbname) or die(mysql_error());

$limit = "0,6000";
$red = 1000;
$orange = 3500;
$blue = 10000;
$URL = 'http://toolserver.org/~geohack/geohack.php%';
$description = 'The Wikipedia article <a href="http://%s/wiki/%s">%s</a> is missing images';

switch($rFindDB->lang) {
	case "no":
		$description = 'Wikipedia-artikkelen <a href="http://%s/wiki/%s">%s</a> mangler bilder';
		$URL = '//toolserver.org/~geohack/geohack.php%';
		break;
	case "nn":
	case "sv":
	case "da":
		$description = 'Wikipedia-artikkelen <a href="http://%s/wiki/%s">%s</a> mangler bilder';
		break;
	case "ru":
		$limit = "0,3500";
		break;
	case "he":
		$URL = 'http://stable.toolserver.org/geohack/geohack.php%';
		break;
	case "de":
		$URL = '//toolserver.org/~geohack/geohack.php%';
		break;
	default:
		break;
}

$qFindMissing = mysql_query(" SELECT p.page_id,p.page_title,p.page_len,e.el_to,(SELECT COUNT(*) FROM imagelinks i WHERE i.il_from=p.page_id AND i.il_to NOT LIKE '%.svg' AND i.il_to NOT LIKE '%.gif') AS antall FROM page p JOIN externallinks e ON p.page_id=e.el_from WHERE e.el_to LIKE '$URL' AND p.page_namespace=0 AND p.page_title NOT IN ('Grunnlinje') AND p.page_title NOT LIKE 'Liste%' AND page_title NOT LIKE 'Tettsteder%' AND page_title NOT LIKE 'Bompengefinansierte_veier_i_Norge' AND (SELECT COUNT(*) FROM imagelinks i WHERE i.il_from=p.page_id AND i.il_to NOT LIKE '%.svg' AND i.il_to NOT LIKE '%.gif') = 0  /* SLOW_OK */ LIMIT $limit") or die(mysql_error());

echo '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.1">
<Document>
        <name>Laaknors list of Wikipediaarticles that needs picture</name>
        <open>1</open>
        <Style id="red">
                <IconStyle>
                       <Icon>
                               <href>http://maps.google.com/mapfiles/ms/icons/red-dot.png</href>
                       </Icon>
                </IconStyle>
        </Style>
        <Style id="blue">
                <IconStyle>
                       <Icon>
                               <href>http://maps.google.com/mapfiles/ms/icons/blue-dot.png</href>
                       </Icon>
                </IconStyle>
        </Style>

        <Style id="orange">
                <IconStyle>
                       <Icon>
                               <href>http://maps.google.com/mapfiles/ms/icons/orange-dot.png</href>
                       </Icon>
                </IconStyle>
        </Style>
        <Style id="green">
                <IconStyle>
                       <Icon>
                               <href>http://maps.google.com/mapfiles/ms/icons/green-dot.png</href>
                       </Icon>
                </IconStyle>
        </Style>

	<Folder>
	<open>1</open>';

while($rFindMissing = mysql_fetch_object($qFindMissing)) {

	$link = $rFindMissing->el_to;
	$title = htmlspecialchars($rFindMissing->page_title);
	$title = str_replace("_", " ", $title);
	$size = $rFindMissing->page_len;
	if($size < $red) $style = "red";
	elseif($size < $orange) $style = "orange";
	elseif($size < $blue) $style = "blue";
	else $style = "green";
	$koordinater = strstr($link, "&params=");
	$koordinater = str_replace("&params=", "", $koordinater);
	$md = new map_sources ( $koordinater , "Some title" ) ;
	#	echo $koordinater."\n";
	echo '
		<Placemark id="'.$rFindMissing->page_id.'">
		<name><![CDATA[';
	echo $title;
	echo ']]></name>
		<styleUrl>';
	echo "#$style";
	echo '</styleUrl>
	<Point>
		<coordinates>';
	echo $md->p->londeg.",";
	echo $md->p->latdeg;
	echo '</coordinates>
	</Point>';
#	<Snippet></Snippet>
	echo '<description><![CDATA[<p>'.sprintf($description, $rFindDB->domain, $rFindMissing->page_title, $title).'</p>]]></description>
	</Placemark>';
}

echo '</Folder>
	</Document>
	</kml>';
