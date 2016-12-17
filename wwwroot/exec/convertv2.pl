#!/usr/bin/perl

print "\033[2J";    #clear the screen
print "\033[0;0H"; #jump to 0,0

###########################
### Autor: Sebastian Enger / B.Sc. 
### Copyright: Sebastian Enger
### Licence: BSD
### Version: 1.1  - 28-10-2013@16.20.Uhr
### Contact: sebastian.enger@gmail.com | icq: 135846444
### Latest Version: PENDRIVE:\Programming\Perl\IncrementalBackup
###########################

## http://search.cpan.org/~mizzy/FFmpeg-Command-0.07/lib/FFmpeg/Command.pm
	
#use List::Util qw(min);
#use Data::Dumper;
use Getopt::Long;
no strict "refs";
use DBI;
use Digest::MD5 qw(md5 md5_hex md5_base64);
use FFmpeg::Command; # perl -MCPAN -e 'force install "FFmpeg::Command"'
 
GetOptions ("video_url=s" => \$video_url) or die("Error in command line arguments\n");

my $store_path 	= "/home/www/mp3/store";
my $tmp_path 	= "/home/www/mp3/tmp";
my $ffmpeg_bin 	= "/usr/local/bin/ffmpeg";
my $python 		= "/usr/bin/python";
my $python_script = "/home/www/mp3/exec/convert_simple.py";
my $error 		= "";
my $query_update = "";

my $ffmpeg 		= FFmpeg::Command->new($ffmpeg_bin);
my $dbh 		= DBI->connect("dbi:mysql:dbname=youtube","root","rouTer99", {
    AutoCommit 	=> 1,#0=$dbh->commit(); 
    RaiseError 	=> 1,
  });

# LOOP THROUGH RESULTS
my $mp3_id 	 	= md5_hex($video_url.$$.$client.$query.$dbh.$title.$fmt.$hash.rand(8094).rand(8094));
my @args 		= ("$python", "$python_script", "$video_url", "$mp3_id");
my $returnCodeDownload = system(@args);# == 0 or warn "system @args failed: $?"; # later give back error occurred
if ($returnCodeDownload != 0) {
	$error = "E100: Video could not be downloaded."; # PyTube PRoxy Support einbauen
	$query_update = "UPDATE converter SET mp3_ready = '0', mp3_error = '$error' WHERE video_url = '$video_url';";
	eval { $dbh->do($query_update) };
	exit(1);
}

#system("$python $python_script $video_url $mp3_id");	
my $tmp_file	= "$tmp_path/$mp3_id.mp4"; 
	
	###########################
	### Todo: Add Tags to mp3
	### Todo: Add Songtext to mp3
	### TODO: get songtext from sphinx search perl api or php request
	###########################

#print "CONVERTING TO $store_path/$mp3_id.mp3 \n";
my @args2 		= ("$ffmpeg_bin","-i", "$tmp_file", "-acodec", "libmp3lame", "-ab", "192k", "$store_path/$mp3_id.mp3");
my $returnCodeConvert = system(@args2); # == 0 or warn "system @args2 failed: $?"; # later give back error occurred
if ($returnCodeConvert != 0) {
	$error = "E101: Error Converting To MP3";
	$query_update = "UPDATE converter SET mp3_ready = '0', mp3_error = '$error' WHERE video_url = '$video_url';";
	eval { $dbh->do($query_update) };
	exit(1);
}

$query_update = "UPDATE converter SET mp3_ready = '1', mp3_id = '$mp3_id' WHERE video_url = '$video_url';";
eval { $dbh->do($query_update) };
#warn "Updating MYSQL Failed: $@ \n" if ($@);
	
exit(0);

#system("$ffmpeg_bin -i $tmp_file -acodec libmp3lame -ab 192k $store_path/$mp3_id.mp3");
#print "UPDATE QUERY\n";
#if ($error eq ""){
#	$query_update = "UPDATE converter SET mp3_ready = '1', mp3_id = '$mp3_id' WHERE video_url = '$video_url';";
#} else { # error OCCURED
#	$query_update = "UPDATE converter SET mp3_ready = '0', mp3_error = '$error' WHERE video_url = '$video_url';";
#}