#!/usr/bin/perl

###########################
### Autor: Sebastian Enger / B.Sc. 
### Copyright: Sebastian Enger
### Licence: BSD
### Version: 1.1.b  - 20080716@19.Uhr
### Contact: sebastian.enger@gmail.com | icq: 135846444
### Latest Version: PENDRIVE:\Programming\Perl\IncrementalBackup
###########################

use strict;
use File::Find;	# perl -MCPAN -e 'force install "File::Find"'
use File::Copy;	# perl -MCPAN -e 'force install "File::Copy"'
use File::Path;	# perl -MCPAN -e 'force install "File::Path"'
# use File::Remove qw(remove);
my $SourceFolder = '/home/songtexts/may2014/test/www.leoslyrics.com';


open(R,"<out.txt");
	while(<R>){
		my $line = trim($_);
		if ( $line =~ /-albums$/ig || $line =~ /-lyrics$/ig){
			print "Working on: $SourceFolder/$line\n";	
			rmtree("$SourceFolder/$line");
		};
	};



sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}


exit;





# prepare files from SourceFolder
find(\&SourceStructure, $SourceFolder);


sub SourceStructure(){

    # $_ is set to the current file name
    # $File::Find::dir is set to the current directory
    # $File::Find::name is set to "$File::Find::dir/$_"
    # you are chdir()'d to $File::Find::dir 

	my $SourceFilename		= $File::Find::name;
	my $SourceDirname		= $File::Find::dir;
		
	if ( $SourceFilename =~ /-albums/ig || $SourceFilename =~ /-lyrics/ig){
		print "Working on: $SourceFilename\n";	
	};
	
}; # sub SourceStructure(){