#!/usr/bin/perl

# See http://search.cpan.org/~rclamp/File-Find-Rule-0.32/README
use File::Find::Rule; # perl -MCPAN -e 'force install "File::Find::Rule"'

# If a base directory was not past to the script, assume current working director
my $base_dir = shift // '.';
my $find_rule = File::Find::Rule->new;

# Do not descend past the first level
$find_rule->maxdepth(1);

# Only return directories
$find_rule->directory;

# Apply the rule and retrieve the subdirectories
my @sub_dirs = $find_rule->in($base_dir);

# Print out the name of each directory on its own line
foreach my $dir (@sub_dirs){
	next if ($dir eq "/home/songtexts");
	my @array = split("\/", $dir);
	my $filename = $array[$#array];
	print "dir: $dir --> filename: $filename\n";
	system("ls -lh $dir > $filename.txt");
}
