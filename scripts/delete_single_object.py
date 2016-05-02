
#search for and delete a single object based on object and annotation id

import urllib, urllib2, json,operator

url = 'http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/search?keyword=skytest&acting_user=5462125'
hdr = {"Accept":"application/json"}
delobjurl = 'http://astro.cs.pitt.edu:8080/astroservice/v2/object/{}/delete'
delannurl = 'http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/{}/delete'

try:
	req = urllib2.Request(url)
	req.add_header("Accept","application/json")
	response = urllib2.urlopen(req)
except urllib2.HTTPError, e: 
	print "HTTP error: " , e.code
except urllib2.URLError, e:
	print "Network error: " , e.reason.args[1]

results = json.loads(response.read())
print ' '
print "Found",len(results),"results!"
print ' '
numitems = len(results)

if len(results) > 0:
	#get index for annotation ID
	f = operator.itemgetter(0)
	items = results[0].items()
	inAnnoId = map(f,items).index('annoId')
	inObj = map(f,items).index('objectInfoCollection')

	#retrieve annotation and object ids and delete them
	for i in range(numitems):
		data = {"dummy":"dummy"} #dummy data for passing into POST
		data = json.dumps(data)
		item = results[i].items()
		annoId = item[inAnnoId][1]
		obj = item[inObj][1][0]
		objId = obj['objectId']
		if i == 0:
			print 'annoId', 'objectId'
			print '--------------------'
		print annoId, objId

		#delete the annotations
		delannurl=delannurl.format(annoId)
		print delannurl
		try:
			req = urllib2.Request(delannurl,data)
			response = urllib2.urlopen(req)
			print 'Successful Annotation Deletion'
		except urllib2.HTTPError,e:
			print 'HTTP Error: ',e.code
		except urllib2.URLError,e:
			print 'URL Error: ',e.reason.args[1]

		#delete the objects
		delobjurl=delobjurl.format(objId)
		print delobjurl
		try:
			req = urllib2.Request(delobjurl,data)
			response = urllib2.urlopen(req)
			print 'Successful Object Deletion'
		except urllib2.HTTPError,e:
			print 'HTTP Error: ',e.code
		except urllib2.URLError,e:
			print 'URL Error: ',e.reason.args[1]		
		


