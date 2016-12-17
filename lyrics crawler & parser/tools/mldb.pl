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

no warnings;

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
use Email::MIME; # perl -MCPAN -e 'force install "Email::MIME"'
use Email::Sender::Simple qw(sendmail); # perl -MCPAN -e 'force install "Email::Sender::Simple"'
use Encode qw(encode decode);

my $scriptname 		= basename($0);
my $count 			= 1;
my $enc 			= 'utf-8'; # in dieser Kodierung ist das Script gespeichert

my $SourceFolder 	= '/home/songtexts/neuigkeiten/www.mldb.org/';
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

my $message = Email::MIME->create(
  header_str => [
    From    => 'lyricsinstaller@youtube-mp3.mobi',
    To      => 'sebastian.enger@gmail.com',
    Subject => "$scriptname Ready with $count Entries install at " . localtime(),
  ],
  attributes => {
    encoding => 'quoted-printable',
    charset  => 'ISO-8859-1',
  },
  body_str => "$scriptname Ready with $count Entries as Install! Please setup next lyrics crawler.\n",
);

system("indexer --rotate --config /home/sphinx/sphinx.conf songtext");
# send the message
sendmail($message);
exit;

sub SourceStructure(){

    # $_ is set to the current file name
    # $File::Find::dir is set to the current directory
    # $File::Find::name is set to "$File::Find::dir/$_"
    # you are chdir()'d to $File::Find::dir 

	my $SourceFilename		= $File::Find::name;
	my $SourceDirname		= $File::Find::dir;
	my $string = "";
	
	my $size 				= -s $SourceFilename;
	# if ( $SourceFilename =~ /([a-z0-9])\.html$/ig && -e $SourceFilename ){	
	if ( $SourceFilename =~ /song/ig && -e $SourceFilename && $size > 100){	
	#	print "SourceFilename=$SourceFilename\n";
	#	return;
	
	#	if ($SourceFilename =~ m/change\?id=(\d)$/ig){
	#		unlink $SourceFilename if -f $SourceFilename;
	#		print "SourceFilename=$SourceFilename\n";
	#		print "Deleted!\n";
	#		return;
	#	};
	
	
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
				
				my ($cover) = ""; #($string =~ m#link rel='image_src' href='\s*(.*?)\s*'>#igs);
				my ($title_temp) = ($string =~ m#<TITLE>\s*(.*?)\s*</TITLE>#igs);
				$title_temp =~ s/(Lyrics|Songtexte|Songtext|songtekst|of)//ig;
				$title_temp =~ s/&#(\d+);//gi;
				
				my ( $title1, $artist, $title, undef, undef, undef, undef,undef,undef) = split(' - ', $title_temp);
				#my ($artist, $title) = split(/ - /ig, $title1);
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
				$artist = encode($enc, $artist);
				if (length $artist <= 0 || $artist !~ /(\w+)/ig ){
					$artist = "General";
				}
				return if ($artist =~ /(changes|version)/ig);
				
				#print "Artist: $artist\n";
				#return ;
				
				$title =~ s/(Lyrics|Songtexte|Songtext|songtekst|von)//ig;
				$title =~ s/,//ig;
				$title = trim($title);
				$title = encode($enc, $title);
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
				my ($content1) = ($string =~ m#<p class="songtext"\s*(.*?)\s*<table width="100%" cellspacing="0" cellpadding="2">#igs);
				$content1 =~ s/(Lyrics|Songtexte|Songtext)//ig;
				my $content = encode($enc, $content1);
		#		
		#		my @encoded = split("<li>|<br />|<br/>", $content);
		#		my $songtext = "";
		#		my $s;
		#		foreach my $entry (@encoded){
		#		#	my $s = unidecode(decode_entities($entry));
		#		#	$s =~ s/(<p>|<\/p>)//ig;
		#			$s = encode($enc,$entry);
		#			if ( $s =~ /(\w+|\d+)/ig && length($s) > 5 ){	
		#				$songtext .= $s . "<br />";
		#			} elsif ( $s !~ /(\w+|\d+)/ig && length($s) < 2 ){	
		#				$songtext .= $s . "<br /><br />";
		#			} else {
		#				$songtext .= $s . "<br />";
		#			}
		#		}
		#		
		#		$content = encode($enc, $songtext);
		
				my $re = qr{
					   \(
					   (?:
						  (?> [^()]+ )    # Non-parens without backtracking
						|
						  (??{ my $re })     # Group with matching parens
					   )*
					   \)
					}x;
				$content = $hs->parse( $content );
				$hs->eof;
								
				my $c = "";
				my $ccc = 0; 
				#$title = ""; 
				#$artist = "";
				#my $ret = 0;
				foreach my $entry (split("\n",$content )){
					$ccc++;
				#	print "$ccc) $entry\n"; next;
					$entry = encode($enc,$entry);
					$entry = trim($entry);
					
					$entry =~ s/$re//g; # entferne ()
					$entry =~ s/\[([^\[\]]++|(?R))*+\]//ig; # entferne []
					
				#	if ( $ccc == 1 ){
				#		$title = $entry;
				#		$title = trim($title);
				#		$title = encode($enc, $title);
				#	} 
				#	if ( ($ccc > 1 && $ccc < 10) && $ret == 0){
				#		my @c = split("\n",$content );
				#		
				#		$artist = @c[$ccc];
				#		if ($artist =~ /(\w)/ig && length $artist > 5 ){
				#			$ret = 1;
				#		}
				#		$artist =~ s/ - / /ig;
				#		$artist = trim($artist);
				#		$artist = encode($enc, $artist);
				#		#print "SourceFilename=$SourceFilename\n";
				#	#	print "title: $title -> artist: $artist\n"; return;
				#	}
				#						
				#	if ($entry =~ /Song:/ig){# temp
				#		(undef, $title) = split(/\: /ig, $entry);
				#		$title = trim($title);
				#		$title = encode($enc, $title);
				#		#print "Title: $title\n";
				#	}
				#	if ($entry =~ /Artist:/ig){# temp
				#		(undef, $artist) = split(/\: /ig, $entry);
				#		$artist = trim($artist);
				#		$artist = encode($enc, $artist);
				#		#print "ARtist: $artist\n";
				#	}
					#my ($log1,$log2) = split(':',$entry);
					if ($entry =~ /lang=\"/ig){
						(undef,$entry) = split(">",$entry);
					}
					next if ($entry =~ /(\bad\b|ringtone|received)/ig);
					$c .= "$entry<br />\n";
					
				}
				
				#$title = "$artist - $title"; # temporary, remove for title grabber
				$content = encode($enc, $c);
				
				my $lenght = length($content);
				#print "Lenght: $lenght -> $SourceFilename\n";
				
			#	if ( $lenght>=$req_lenght ) {
			#		print "File: $SourceFilename\n";
			#		print "$count) Title: '$title' \n";
			#		print "Artist: $artist \n";
			#		print "Content Lenght: $lenght\n";
			#		print "Cover: $cover \n";
			#		$content = strip_tags( $content, $allowedTags );
			#		print "Content: $content \n";		
			#		$count++;
			#
			#	}
			#	return;
			
	#		if ( $lenght>$req_lenght&&$count<=95279){
	#			$count++;
	#			print "I am skipping: $count of 100K\n";
	#			return;
	#		};
		
			if ( $Hash{$title} ) {
			#	print "ERROR: Double Title - $title ---> Hash Count: ".keys(%Hash)." \n";
			#	return;
			} else {	
			#	$Hash{$title} = $title;
			}
			my $query = "";	
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
  
				open(W,"+>>$logfile");
					print W "(r: $count) -> $lenght Bytes -> $artist -> $title \n";
					print "(r: $count) -> $lenght Bytes -> $artist -> $title \n";
				
				if ( $title =~ /(\w{3,})/ig && $artist =~ /(\w{3,})/ig && $content =~ /(\w{3,})/ig ) {
					$query = sprintf("%s (%s, %s, %s, %s, %s)",
						"INSERT INTO songtexts (id,title,artist,songtext,cover) VALUES",
						$dbh->quote(""),
						$dbh->quote( $title ),
						$dbh->quote( $artist ),
						$dbh->quote( $content ),			
						$dbh->quote( $cover ),
					);
			  ##	print $query; return; exit;
					eval { $dbh->do($query) };
					$dbh->disconnect;
				
					warn $@ if $@;
					print W "(r: $count) -> Error: $@ \n" if $@;
				}
				
				select(undef, undef, undef, 0.07);
				$count++;
				close W;
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