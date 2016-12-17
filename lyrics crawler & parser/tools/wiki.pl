#!/usr/bin/perl

#sleep 25000;

###########################
### Autor: Sebastian Enger / B.Sc. 
### Copyright: Sebastian Enger
### Licence: BSD
### Version: 1.1.b  - 20080716@19.Uhr
### Contact: sebastian.enger@gmail.com | icq: 135846444
### Latest Version: PENDRIVE:\Programming\Perl\IncrementalBackup
###########################

use strict;
use DBI;
use URI; 	# perl -MCPAN -e 'force install "CPan"'
use File::Find;	# perl -MCPAN -e 'force install "File::Find"'
use File::Copy;	# perl -MCPAN -e 'force install "File::Copy"'
use File::Path;	# perl -MCPAN -e 'force install "File::Path"'
#use XML::Code; # perl -MCPAN -e 'force install "Curses"'
use HTML::StripTags qw(strip_tags);
use File::Basename;
use Data::Dumper;
use LWP::Simple;
use Text::Unidecode qw(unidecode); # perl -MCPAN -e 'force install "Text::Unidecode"'
use HTML::Entities qw(decode_entities); # perl -MCPAN -e 'force install "HTML::Entities"'
use HTML::Strip; # perl -MCPAN -e 'force install "HTML::Strip"'

my $scriptname 		= basename($0);
my $count 			= 1;

my $SourceFolder 	= '/home/songtexts/neuigkeiten/lyrics.wikia.com/';
my $CopySrcFolder 	= '/home/songtexts/logs';
my $logfile  		= '/home/songtexts/logs/';
$logfile 			.= $scriptname."_log.txt";
my $allowedTags 	= "<li></li></ul><ul><ol></ol><strong><b></strong></b><h1><h2><h3><h4><h5></h1></h2></h3></h4></h5><br><br /><br/>";
#	my $SourceFolder = 'E:\Working 2013\www.youtube-mp3.mobi\songtexte\www.lyricsmode.com\www.lyricsmode\home\songtexts\www.lyricsmode.com\lyrics';
#	my $CopySrcFolder = 'E:\Working 2012\www.nookiestar.com\mirror\crawler';
#	my $logfile  	= 'E:\Working 2012\www.nookiestar.com\mirror\crawler\logfile\\';
#	$logfile 			.= $scriptname."_log.txt";



my $string1;
my $catg;
my $title;
my $string;
my $rcount = 0;
my $Hash;
my %Hash = ();


my $req_lenght		= 100;	# 1500
my $sd 				= $SourceFolder =~ /(\w+)\.(com|net|org|eu|de|\.co\.nz|biz|to|tv|ws|in|me)$/ig;
my $domain 			= "$1.$2"; 
#my $CopyToFolder 	= "$CopySrcFolder\\$domain";
my $CopyToFolder 	= "$CopySrcFolder/$domain";
mkdir $CopyToFolder;
copy($0,$CopyToFolder) or die "Copy failed: $!";

my $hs = HTML::Strip->new();

# prepare files from SourceFolder
#eval {
find(\&SourceStructure, $SourceFolder);
#};

print "I found $count entries\n";
#$dbh->disconnect();
exit;

sub SourceStructure(){

    # $_ is set to the current file name
    # $File::Find::dir is set to the current directory
    # $File::Find::name is set to "$File::Find::dir/$_"
    # you are chdir()'d to $File::Find::dir 

	my $SourceFilename		= $File::Find::name;
	my $SourceDirname		= $File::Find::dir;
	my $string = "";
	
	# if ( $SourceFilename =~ /([a-z0-9])\.html$/ig && -e $SourceFilename ){	
	if ( $SourceFilename =~ /([a-z0-9])\:([a-z0-9])/ig && -e $SourceFilename ){	
	#	print "SourceFilename=$SourceFilename\n";
	
		if ($SourceFilename =~ m/index\.html/ig){
		#	print "Trackback found\n";
		#	return;
		};
	
#	my $p;
#	my @p = split("\/", $SourceFilename);
#	#print Dumper @p;
#	my $artist = @p[6];
#	$artist =~ s/\_/ /ig;
#	$artist =~ s/([\w']+)/\u\L$1/g;
#	print "Artist: $artist\n";
#	return ;
			
			if ( $count >= 0 ) {
			
				{
				  local $/ = undef;
				  open FILE, "$SourceFilename" or return "Couldn't open file: $SourceFilename $!";
				  binmode FILE;
				  $string = <FILE>;
				  close FILE;
				}
			#	print length $string ." \n";

				$title = "";
									
				# Get Title from filename: Start
			#	my $temp;
			#	my @temp = split("\/",$SourceFilename);	
			#	$title = "$temp[$#temp]"; 
			#	#my (undef,undef,$title) = split(",",$title);
			#	$title =~ s/(\.html|_|-)/ /ig;
			#	$title =~ s/\-/ /ig;
			#	$title =~ s/-/ /g;
			#	$title =~ s/(\w+)/\u$1/g;
			#	$title = ucfirst($title);
								
				##next if $title =~ /(feed|\&|\$)/ig;
				###$title .= " - $temp[1]";
				#print ">>>>>>>>>>>>>>> ".length($title).": $title <<<<<<<<<<<<< \n";
				#return;
				# Get Title from filename: End
				
				my ($cover) = ($string =~ m#<meta property="og:image" content="\s*(.*?)\s*">#igs);
				my ($title_temp) = ($string =~ m#<TITLE>\s*(.*?)\s*</TITLE>#igs);
				$title_temp =~ s/Lyrics//ig;
				my ( $title1, undef, undef, undef, undef,undef,undef) = split('-', $title_temp);
				my ($artist,$title) = split(/\:/ig, $title1);
				return if ($title_temp =~ /(comment|by)/ig );
				
				#my (@cover) = split("\"",$cover );
				#$cover = @cover[1];
				#print "Cover: @cover[1] \n";
				#return;
				
				if ($cover!~/^http/ig){
					$cover = "";
				}
				#print "TITLE: $title \n";
				#return;
				
				$artist =~ s/(Lyrics|Songtexte|Songtext|von)//ig;
				$artist =~ s/([\w']+)/\u\L$1/g;
				$artist = trim($artist);
				if (length $artist <= 0 ){
					$artist = "General";
				}
				#print "Artist: $artist\n";
				#return ;
				
				$title =~ s/(Lyrics|Songtexte|Songtext|von)//ig;
				$title =~ s/,//ig;
				$title = trim($title);
				$title = ucfirst($title);
				$title = "$artist - $title";
															
				$title =~ s/\'//ig;
				$title =~ s/\s{2,}+/ /ig;		
				$title =~ s/_/ /g;
				#$title =~ s/([\w']+)/\u\L$1/g;
				$title =~ s/&+(\w+)+;/ /g;
				$title =~ s/\s+/ /g;
				$title =~ s/&+(\w+)+;/ /g;
				$title =~ s/\s+/ /g;
				$title =~ s/&(\w+);//g;
				$title =~ s/&#(\w+);//g;
									
				my @split = split("\n", $string);
				my ($content) = ($string =~ m#<div id="WikiaArticle" class="WikiaArticle">\s*(.*?)\s*NewPP limit report#igs);
				$content =~ s/(Lyrics|Songtexte|Songtext)//ig;
							
				$content = $hs->parse( $content );
				$hs->eof;
				my $c = "";
				foreach my $entry (split("\n",$content )){
					$entry = trim($entry);
					#print "ID: $entry\n";
					next if ($entry =~ /(style|Submit|Ringtone|copy|wiki)/ig);
					$c .= "$entry<br />\n";
				}
				$content = $c;
				
				my @encoded = split("<br>|<br \/>|<br\/>", $content);
				my $songtext = "";
				foreach my $entry (@encoded){
					my $s = unidecode(decode_entities($entry));
					$s =~ s/(<p>|<\/p>)//ig;
					return if $s =~ m/(itunes|amazon|javascript|category|illegal)/ig;
					if ( $s =~ /(\w+|\d+)/ig && length($s) > 5 ){	
						$songtext .= $s . "<br />";
					} elsif ( $s !~ /(\w+|\d+)/ig && length($s) < 2 ){	
						$songtext .= $s . "<br /><br />";
					} else {
						$songtext .= $s . "<br />";
					}
				}
				
				
				$content = $songtext;
				
				print "content: $content\n";
				sleep 1;
				return;
				my $lenght = length($content);
				#print "Lenght: $lenght -> $SourceFilename\n";
				
				if ( $lenght>=$req_lenght ) {
					print "File: $SourceFilename\n";
					print "$count) Title: '$title' \n";
					print "Artist: $artist \n";
					print "Content Lenght: $lenght\n";
			#		print "Cover: $cover \n";
					$content = strip_tags( $content, $allowedTags );
					print "Content: $content \n";	
					sleep 1;
					$count++;
			
				}
				return;
			
			if ( $lenght>$req_lenght&&$count<=359043){
				$count++;
				print "I am skipping: $count of 100K\n";
				return;
			};
		
			if ( $Hash{$title} ) {
			#	print "ERROR: Double Title - $title ---> Hash Count: ".keys(%Hash)." \n";
			#	return;
			} else {	
			#	$Hash{$title} = $title;
			}
			
			if ( $lenght>$req_lenght&&$lenght<2500000){
			
				# remove html from text 
				$content = strip_tags( $content, $allowedTags );
	  			$content =~ s/(\w+)\.(com|net|org|eu|biz|ch|de|us)//ig;
				#$content =~ s/[^[:ascii:]]/ /g;
				$content =~ s/\r\n/ /g;
				$content =~ s/&(\w+);//g;
				$content =~ s/\n/<br \/>/g;
				$content =~ s/(<br\ ?\/?>)+/<br \/>/ig; # http://stackoverflow.com/questions/7738439/how-to-regex-replace-multiple-br-tags-with-one-br-tag
				
				#$content =~ s/(ero-geschichte\.(com|net|org|eu|tv|biz|ch|de|us|at|to|me|mobi))/nookiestar\.com/ig;

				check();
				
				my $dbh = DBI->connect("dbi:mysql:dbname=songtexts","root","rouTer99", {
					AutoCommit => 1,#0=$dbh->commit(); 
					RaiseError => 0,
				  });
  
				if ( $lenght > $req_lenght ){
											
					open(W,"+>>$logfile");
					print W "(r: $count) -> $lenght Bytes -> $artist -> $title \n";
					print "(r: $count) -> $lenght Bytes -> $artist -> $title \n";
					close W;
									
					my $query = sprintf("%s (%s, %s, %s, %s, %s)",
						"INSERT INTO songtexts (id,title,artist,songtext,cover) VALUES",
						$dbh->quote(""),
						$dbh->quote( $title ),
						$dbh->quote( $artist ),
						$dbh->quote( $content ),			
						$dbh->quote( $cover ),
					);
				
			###	print $query; exit;
					eval { $dbh->do($query) };
					$dbh->disconnect;
					select(undef, undef, undef, 0.04);
					
					$count++;
				
				}
			}
								
		} # if ( $count > 55 ) {
	}; # if ( $SourceFilename =~ /\.txt/ig ){#&& $SourceDirname =~ /Team/ig){
	
}; # sub SourceStructure(){

sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}

sub check(){
	
	my $max_retry = 200;
	my $sleep = 40;
	
	my $length_of_randomstring=32;# the length of 
			 # the random string to generate

	my @chars=('a'..'z','A'..'Z','0'..'9','_');
	my $random_string;
	foreach (1..$length_of_randomstring) 
	{
		# rand @chars will generate a random 
		# number between 0 and scalar @chars
		$random_string.=$chars[rand @chars];
	}
	
	while($max_retry>0){
		my $doc = get("http://www.nookiestar.com/alive.php?id=$random_string");
		
		if ( $doc =~ /online/ig ){
			return 1;
		}
		
		if ( $doc =~ /offline/ig ){
			$max_retry--;
			print "Mysql Database has gone away: waiting $sleep second\n";
			sleep $sleep;
		}
		
	}

	return 0;
}