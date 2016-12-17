#!/usr/bin/perl

use strict;
use Net::Amazon;
use LWP::UserAgent;
use HTTP::Cookies;

my $lwpua = LWP::UserAgent->new();
my $amzua = Net::Amazon->new(token => '14NV3Q57Y02CD41HF2G2',
							 secret_key    => 'm1rWyBK7yTop5Cbw6SDqkXJ2efgckwjv7li2OCn4');

my $artist;
my $album;
my $path = $ARGV[0];
my $artist = "DVBBS Borgeous";
my $album = "Tsunami";


if($album =~ /(.+?) disc.+/i) {
    $album = $1;
}

# Get a request object
my $response = $amzua->search(artist => $artist,
                              mode => 'music',
                              keywords => $album);

my $result;
my $img;
if ($response->is_success()) {
    foreach $result ($response->properties) {
        my $fartist = $result->artist();
        my $falbum = $result->album();

        if($artist =~ /$fartist/i && $album =~ /$falbum/i) {
            printf("Found %s by %s\n", $falbum, $fartist);

            if(defined $result && exists $result->{'ImageUrlLarge'}) {
                $img = $lwpua->get($result->{'ImageUrlLarge'});
            } elsif(defined $result && exists $result->{'ImageUrlMedium'}) {
                $img = $lwpua->get($result->{'ImageUrlMedium'});
            } elsif(defined $result && exists $result->{'ImageUrlSmall'}) {
                $img = $lwpua->get($result->{'ImageUrlSmall'});
            } else {
                print("No images.\n");
                next;
            }

            if($img->is_success()) {
                unlink("$path/cover.jpg");
                open(F, ">$path/cover.jpg");
                print(F $img->content());
                close(F);
                printf("Downloaded %s/cover.jpg\n", $path);
                last;
            }
        }
    }
} else {
    print("Error: ", $response->message(), "\n");
}