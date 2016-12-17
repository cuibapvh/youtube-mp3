#!/usr/bin/perl
use strict;
use warnings;
use Cwd;
use File::Find;

#my $dir = getcwd; # Get the current working directory
my $dir = '/home/songtexts/neuigkeiten/www.lyricsty.com/lyrics/';

my $counter = 0;
find(\&wanted, $dir);
print "Found $counter files at and below $dir\n";

sub wanted {
    if ( $_ =~ /\.html/ig ) {
		$counter++; # Only count files
	}
}