#!/usr/bin/perl

###########################
### Autor: Sebastian Enger / B.Sc. 
### Copyright: Sebastian Enger
### Licence: BSD
### Version: 1.1.b  - 20080716@19.Uhr
### Contact: sebastian.enger@gmail.com | icq: 135846444
### Latest Version: PENDRIVE:\Programming\Perl\IncrementalBackup
###########################

# http://search.cpan.org/~xaicron/WWW-YouTube-Download/lib/WWW/YouTube/Download.pm
## http://search.cpan.org/~mizzy/FFmpeg-Command-0.07/lib/FFmpeg/Command.pm
	
use WWW::YouTube::Download; # perl -MCPAN -e 'force install "WWW::YouTube::Download"'
#use List::Util qw(min);
#use Data::Dumper;
use Getopt::Long;
use DBI;
use Digest::MD5 qw(md5 md5_hex md5_base64);
use FFmpeg::Command; # perl -MCPAN -e 'force install "FFmpeg::Command"'
 
GetOptions ("video_id=s" => \$video_id) or die("Error in command line arguments\n");

my $store_path 	= "/home/www/mp3/store";
my $tmp_path 	= "/home/www/mp3/tmp";
my $ffmpeg_bin 	= "/usr/local/bin/ffmpeg";

my $ffmpeg 		= FFmpeg::Command->new($ffmpeg_bin);
my $dbh 		= DBI->connect("dbi:mysql:dbname=youtube","root","rouTer99", {
    AutoCommit => 1,#0=$dbh->commit(); 
    RaiseError => 1,
  });

 print "PREPARE QUERY \n";
# PREPARE THE QUERY
my $query 			= "SELECT video_url,video_author,video_title,video_category FROM converter WHERE mp3_ready='0' AND video_id='$video_id' LIMIT 1;";
my $query_handle 	= $dbh->prepare($query);
#open(W,"+>>out.txt");
#   print W "$query";
#close W;

# EXECUTE THE QUERY
$query_handle->execute();

# BIND TABLE COLUMNS TO VARIABLES
$query_handle->bind_columns(\$video_url,\$video_author,\$video_title,\$video_category);

 print "LOOP THROUGH RESULTS \n";
# LOOP THROUGH RESULTS
while($query_handle->fetch()) {
open(W,">/tmp/out.txt");
 print W "$video_url,$video_author,$video_title,$video_category <br />";
print "$video_url,$video_author,$video_title,$video_category \n";
close W;

	my ($youtube_id) = $video_url =~ /watch\?v=([\w-]{11})/igs;
	print "video_url: $video_url\n";
	print "youtube_id: $youtube_id\n";
	
	my $client 		= WWW::YouTube::Download->new();
#	my $video_url 	= $client->get_video_url($youtube_id);
#	my $title     	= $client->get_title($youtube_id);     # maybe encoded utf8 string.
#	my $fmt1       	= $client->get_fmt($youtube_id);       # maybe highest quality.
#	my $suffix    	= $client->get_suffix($youtube_id);    # maybe highest quality file suffix
#	my $fmt 		= $client->get_fmt_list($youtube_id);
#	my $hash    	=  $client->prepare_download($youtube_id);
#	my $min_value 	= min @{$fmt};
	my $mp3_id 		= $video_id; 
	my $tmp_id	 	= md5_hex($video_id.$$.$client.$query.$dbh.$title.$fmt.$hash.rand(8094).rand(8094));

	my $tmp_file	= "$tmp_path/$tmp_id"; 
	print "DOWNLOADING $tmp_file \n";
	$client->download($youtube_id, {
		fmt      => 25,
		filename => $tmp_file, # maybe `video_title.mp4`
	});

	###########################
	### Todo: Add Tags to mp3
	### Todo: Add Songtext to mp3
	### TODO: get songtext from sphinx search perl api or php request
	###########################
	
	 print "CONVERTING TO $store_path/$mp3_id.mp3 \n";
#	system("$ffmpeg_bin -i $tmp_file -vn -ar 44100 -ac 2 -ab 192 -f mp3 $store_path/$mp3_id.mp3");
#	open(W,">out.txt");
#	print W "CONVERTING $ffmpeg_bin -i $tmp_file -vn -ar 44100 -ac 2 -ab 192 -f mp3 $store_path/$mp3_id.mp3\n";
#	close W;

	$ffmpeg->input_file($tmp_file);
    $ffmpeg->output_file("$store_path/$mp3_id.mp3");
	$ffmpeg->global_options(qw/-vn -ar 44100 -ac 2 -ab 192/);
	$ffmpeg->output_options({
        file                => "$store_path/$mp3_id.mp3",
        format              => 'mp3',
        audio_codec         => 'mp3',
        audio_sampling_rate => 44000,
        audio_bit_rate      => 192,
    });
	
	my $result = $ffmpeg->exec();
    croak $ffmpeg->errstr unless $result;
		
	print "CONVERTING $ffmpeg_bin -i $tmp_file -vn -ar 44100 -ac 2 -ab 192 -f mp3 $store_path/$mp3_id.mp3\n";
	 print "UPDATE QUERY\n";
	my $query_update = "UPDATE converter SET mp3_ready = '1', mp3_id = '$mp3_id' WHERE video_id = '$video_id';";
	eval { $dbh->do($query_update) };
	
}   # while($query_handle->fetch()) {

exit(0);