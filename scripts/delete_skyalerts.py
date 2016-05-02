

#delete the objects and annotations for the Sky Alerts

#REMEMBER DELETES ARE POSTS!!!!  NEEDS DUMMY DATA

import urllib, urllib2, json,operator

url = 'http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/search?keyword=SkyAlert&acting_user=5462125'
hdr = {"Accept":"application/json"}
objurl = 'http://astro.cs.pitt.edu:8080/astroservice/v2/object/'     
annurl = 'http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/'  

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

	#retrieve annotation and object ids and delete them
	for i in range(0,numitems,2):
		item = results[i].items()
		annoId = item[inAnnoId][1]
		obj = item[9][1][0]
		objId = obj['objectId']
		if i == 0:
			print 'annoId', 'linkId','objectId'
			print '--------------------'
		print annoId, annoId+1,objId

                data = {"dummy":"dummy"} #dummy data for passing into POST
		data = json.dumps(data)

		#delete the annotations
		delannurl=annurl+'{}/delete'.format(annoId)
		print delannurl
		try:
			req = urllib2.Request(delannurl,data,hdr)
			response = urllib2.urlopen(req)
                        print 'Successful Annotation Deletion'
		except urllib2.HTTPError,e:
			print 'HTTP Error: ',e.code
		except urllib2.URLError,e:
			print 'URL Error: ',e.reason.args[1]

                #delete the link
                dellink = annurl+'{}/delete'.format(annoId+1)
                print dellink
                try:
			req = urllib2.Request(dellink,data,hdr)
			response = urllib2.urlopen(req)
                        print 'Successful Link Deletion'
		except urllib2.HTTPError,e:
			print 'HTTP Error: ',e.code
		except urllib2.URLError,e:
			print 'URL Error: ',e.reason.args[1]


		#delete the objects
		delobjurl=objurl+'{}/delete'.format(objId)
		print delobjurl
		try:
			req = urllib2.Request(delobjurl,data,hdr)
			response = urllib2.urlopen(req)
                        print 'Successful Object Deletion'
		except urllib2.HTTPError,e:
			print 'HTTP Error: ',e.code
		except urllib2.URLError,e:
			print 'URL Error: ',e.reason.args[1]		
		


