#!/usr/bin/python

from pytube import YouTube
import sys

#print ("First argument: %s" % str(sys.argv[1]))
#print ("Second argument: %s" % str(sys.argv[2]))
#sys.exit(0)

videofile = str(sys.argv[1])
md5id = str(sys.argv[2])

yt = YouTube()

# Set the video URL.
yt.url = videofile	
#"http://www.youtube.com/watch?v=ZBHWAY7-cwg"

# view the auto generated filename:
#print yt.filename

yt.filename = md5id;
#video = yt.get('mp4', '720p')
video = yt.get('mp4')

# Okay, let's download it!
#video.download()

# Note: If you wanted to choose the output directory, simply pass it as an 
# argument to the download method.
video.download('/home/wwwyoutube/tmp/')
sys.exit(0)