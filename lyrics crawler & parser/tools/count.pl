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
use File::Find;	# perl -MCPAN -e 'force install "File::Find"'
use File::Copy;	# perl -MCPAN -e 'force install "File::Copy"'
use File::Path;	# perl -MCPAN -e 'force install "File::Path"'
#use XML::Code; # perl -MCPAN -e 'force install "Curses"'
use HTML::StripTags qw(strip_tags);
use File::Basename;
use Data::Dumper;
use LWP::Simple;

my $scriptname 		= basename($0);
my $count = 1;

my $SourceFolder = '/home/songtexts';
my $CopySrcFolder = '/home/songtexts/logs';
my $logfile  	= '/home/songtexts/logs/';
$logfile 			.= $scriptname."_log.txt";

my $string1;
my $catg;
my $title;
my $string;
my $rcount = 0;
my $Hash;
my %Hash = ();


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
	
	# if ( $SourceFilename =~ /\.html/ig && $SourceFilename !~ /trackback/ig){ #$SourceFilename =~ /\.(html|htm|shtm|shtml|php)/ig ){
	if ( $SourceFilename =~ /\.(html|htm|xhtml|shtm|shtml|php|txt|xml)/ig){	
		$count++;
	}
}