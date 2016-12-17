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
use Email::MIME; # perl -MCPAN -e 'force install "Email::MIME"'
use Email::Sender::Simple qw(sendmail); # perl -MCPAN -e 'force install "Email::Sender::Simple"'
use Encode qw(encode decode);

my $scriptname 		= basename($0);
my $count = 0;
my $SourceFolder = '/home/songtexts/neuigkeiten/www.songteksten.nl/songteksten/';
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
	
	if ($SourceFilename =~ m/change\?id/ig){
		unlink $SourceFilename if -f $SourceFilename;
		print "$count ) SourceFilename=$SourceFilename\n";
		print "Deleted!\n";
		$count++;
		return;
	};
	
}