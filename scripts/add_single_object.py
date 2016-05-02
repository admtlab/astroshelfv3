
import urllib, urllib2, json,operator

#adding a single object for testing

url = "http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/add"

hdr = {"Accept":"application/json","Content-Type":"application/json"}

obj = { "surveyId":{"surveyId":3},"surveyObjId":"SkyTest","name":"test","ra":130.00,"dec":23.00,"raType":"type2-2000"}

data = { "objectInfoCollection": [obj] , "annoValue": "this is a skytest",
         "targetType":"object", "userId":{"userId":97} , "annoTypeId" : {"annoTypeId":1}
         }
data = json.dumps(data) #convert to json format

print data

try:
	req = urllib2.Request(url,data,hdr)
	#req.add_header("Accept","application/json")
except urllib2.HTTPError, e: 
	print "HTTP error: $d" % e.code
except urllib2.URLError, e:
	print "Network error: $s" % e.reason.args[1]
response = urllib2.urlopen(req)

print "Added new object!"


