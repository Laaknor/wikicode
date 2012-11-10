<?php
$argument = $argv[1];

if($argument == "nowiki") {
	$file = "/home/laaknor/public_html/SQL/nowiki_viktige_artikler.txt";
	echo "--start--\n";
	echo "--title--Wikipedia:Liste over artikler vi bør ha/metaliste_sortert--titleend--\n";
	echo "<!-- Automatisk oppdatert av Toolserver/Laaknor ".date("Y/m/d")." -->\n";
}
elseif($argument == "nnwiki") {
	$file = "/home/laaknor/public_html/SQL/nnwiki_viktige_artikler.txt";
	echo "--start--\n";
	echo "--title--Wikipedia:Kjerneartiklar/sortert--titleend--\n";
	echo "<!-- Automatisk oppdatert av Toolserver/Laaknor ".date("Y/m/d")." -->\n";
}

else die("No argument");

$fp = fopen($file, "r");
#echo "--start--\n";
#echo "--title--Wikiquote:Manglende artikler med mange IW--titleend--\n";
#echo "{{Wikiquote:Manglende artikler med mange IW/header}}\n";


#$line = 0;
#$pages = 2;

while($fline = fgets($fp)) {
#	print_r($fline);
	$split = split("\t", $fline);
	$storrelse = $split[0];
	$artikkel = split("\n", $split[1]);
#	$crat = split("\n", $split[2]);
#	echo $crat;
#	$editcount = split("\n", $split[1]);
#	print_r($editcount);
#	echo $split[0].";".$split[1]."\n";
#	if($line > 99) {
#		echo "--end--\n";
#		echo "--start--\n";
#		echo "--title--";
#		echo "Wikiquote:Manglende artikler med mange IW/".$pages;
#		echo "--titleend--\n";
#		echo "{{Wikiquote:Manglende artikler med mange IW/header}}\n";
#		$line = 0;
#		$pages++;
#	}
#	if($articlename != 'title' && $articlename != 'page_title') echo "# [[$articlename]] ($amount[0]) <small>([[:en:$articlename]])</small>\n";
	if($artikkel[0] == "page_title");
#	elseif($crat[0] == "bureaucrat") echo "*<!-- $editcount --> {{byråkrat|$adminname}}\n";
#	else echo "*<!-- $editcount --> {{admin|$adminname}}\n";
	else echo "# [[$artikkel[0]]] ($storrelse)\n";
	$line++;
}


fclose($fp);
echo "--end--";
