#!/usr/bin/perl
#Script first used by User:ZorroIII, and later used by User:Laaknor, with some minor modifications.

my $namespace;
my $basename;
my $namespaceprefix;


my $cat = 0;
if ($ARGV[0] eq "-cat") {
	$cat = 1;
	 shift @ARGV;
}


shift @ARGV if ($ARGV[0] eq "-no");

if ($cat) {
	$namespace="Category";
	$basename = "Wikipedia:Kategorier som mangler interwiki/";
	$namespaceprefix = ":Kategori:";
} else {
# Main
	$namespace="main";
	$basename = "Wikipedia:Mangler interwiki/";
	$namespaceprefix = "";
}


if ($ARGV[0] eq "-wqno") {
	if ($cat) {
		$basename = "Wikiquote:Kategorier som mangler interwiki/";
	} else {
		$basename = "Wikiquote:Mangler interwiki/";
	}
	shift @ARGV;
	$lang = "-lang:no";
}
		
	

if ($ARGV[0] eq "-nn") {
	if ($cat) {
		$basename = "Wikipedia:Kategoriar som manglar interwiki/";
	} else {
		$basename = "Wikipedia:Manglar interwiki/";
	}
	shift @ARGV;
	$lang ="-lang:nn";
}

if ($ARGV[0] eq "-da") {
	if ($cat) {
        	$namespace="Category";
        	$basename = "Wikipedia:Kategorier som mangler interwiki/";
        	$namespaceprefix = ":Kategori:";
	} else {
	# Main
        	$namespace="main";
        	$basename = "Wikipedia:Mangler interwiki/";
        	$namespaceprefix = "";
	}
	shift @ARGV;
	$lang ="-lang:da";
} 

if ($ARGV[0] eq "-se") {
	if ($cat) {
		$namespace="Category";
		$basename = "Wikipedia:Kategorier som mangler interwiki/";
		$namespaceprefix = ":Category:";
	} else {
		# Main
		$namespace ="main";
		$basename = "Wikipedia:Mangler interwiki/";
		$namespaceprefix = "";
	}
	shift @ARGV;
	$lang = "-se";
}

if ($ARGV[0]) {
	my $family = $ARGV[0];
	$family =~ s/^\-//;
	$basename =~ s/pedia/$family/;
	print "Basename: $basename\n";
	shift @ARGV;
}


#print "Lang: $lang\n";

$basefile = "/data/project/laaknortools/temp/manglerinterwiki.wiki";
$a = 0;
$line = 0;


open SCRIPT, ">/data/project/laaknortools/temp/uploadmissingiw";
open FILE, ">$basefile";


foreach(<>) {
	chomp;
	($artikkel, $kategori) = split(/\t/);

	$artikkel{$artikkel}{tittel} = $artikkel;
	push @{$artikkel{$artikkel}{kategorier}}, $kategori;


}


foreach $tittel (sort keys %artikkel) {
	if ($line % 100 == 0) {
#		close FILE;
		if ($line > 0) {
			print FILE "--end--\n";
		}

		$a++;
		$n = sprintf ("%03d", $a);
		$file = $basefile; 
		# .$n;

		print SCRIPT "python pageupdate3.py -comment:\"Bot: Oppdaterer\" $lang -title:\"$basename$n\" -file:\"$file\"\n";

		print FILE "--start--\n--title--$basename$n--titleend--\n";
		print FILE "{{".$basename."header|$n}}\n";
	}
	$_ = $artikkel{$tittel}{tittel};
#	s/^/# \[\[/; 
	s/\s+$//; 
	s/_/ /g; 
#	s/^/:Kategori:/;	


	$kategorier = "[[:Category:" . join("|]], [[:Category:", @{$artikkel{$tittel}{kategorier}}) . "|]]";
	$kategorier =~ s/\s+$//;
	$kategorier =~ s/_/ /g;

	print FILE "# {{iw|$_|$kategorier|$namespace}}\n";
	$line++;
}
print FILE "--end--\n";
close FILE;
close SCRIPT;
