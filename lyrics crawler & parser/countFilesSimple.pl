#!/usr/bin/perl
use strict;
use warnings;
use Cwd;
use File::Find;

#my $dir = getcwd; # Get the current working directory
my $dir = $ARGV[0];

my $counter = 0;
find(\&wanted, $dir);
print "Found $counter files at and below $dir\n";

sub wanted {
    if ( -f $_ ) {
		$counter++; # Only count files
	}
}