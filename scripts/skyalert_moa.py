import urllib, urllib2, feedparser,json, operator, time, random

#Retrieve SkyAlerts as a feed (50 most recent) and insert objects+annotations into the database

#SWIFT - gamma ray bursts
#astro = feedparser.parse('http://skyalert.org/feeds/144/')

#MOA - microlensing events
astro = feedparser.parse('http://skyalert.org/feeds/146/')

#Central Bureau for Astronomical Telegrams
#astro = feedparser.parse('http://skyalert.org/feeds/290/')

#Catalina Realtime Transient Survey and SDSS Galaxy - CRTS events that have an SDSS galaxy within 5 arcsec
#astro = feedparser.parse('http://skyalert.org/feeds/147/')

print ' '
print astro.feed.title
print astro.feed.link
print astro.feed.subtitle
print astro.feed.updated
print astro.feed.id
print ' '
print "Number of feed entries: ", len(astro.entries)

url = 'http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/add'
hdr = {"Accept":"application/json","Content-Type":"application/json"}
search = 'http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/search?active_user=34765&keyword=SkyAlert+Event+1'
f = operator.itemgetter(0)

end = 19  #number of entries to read in -1 ; currently set to 20 entries; if want all change to 49
for i in range(len(astro.entries[0:end])):
	print '-------------------------'
	print astro.entries[i].title, ' ;', astro.entries[i].author
	print astro.entries[i].published,' ;', astro.entries[i].updated
	print astro.entries[i].id, ' ; ', astro.entries[i].link
	print astro.entries[i].voevent_ra, astro.entries[i].voevent_dec

	#randomize the time of doing stuff
	n=round(random.random()*10)

	#create object and parameter data to POST
	obj = { "surveyId":{"surveyId":3},"surveyObjId":"SkyAlert","name":"SkyAlert","ra":astro.entries[i].voevent_ra,
		"dec":astro.entries[i].voevent_dec,"raType":"type2-2000"}

	data = { "objectInfoCollection": [obj] , "annoValue": "SkyAlert: MOA, Microlensing Event {0}, {1}".format(i+1,astro.entries[i].author),
		 "targetType":"object", "userId":{"userId":105} , "annoTypeId" : {"annoTypeId":1},
		 "tsCreated":astro.entries[i].published
		 }
	data = json.dumps(data)

	#add the object + annotation
	try:
		req = urllib2.Request(url,data,hdr)
	except urllib2.HTTPError, e: 
		print "HTTP error: $d" % e.code
	except urllib2.URLError, e:
		print "Network error: $s" % e.reason.args[1]
	response = urllib2.urlopen(req)

	#get object id of newly inserted object to insert the link
	results = json.loads(response.read())
	inObj = map(f,results.items()).index('objectInfoCollection')
	objid = results.items()[inObj][1][0]['objectId']
	#print objid
	addlink = 'http://astro.cs.pitt.edu:8080/astroservice/v2/object/{}/annotate'.format(objid)
	data = {"targetType": "annotation", "userId": {"userId": 105}, "annoValue": astro.entries[i].link,
		"annoTypeId": {"annoTypeId": 4}}
	data = json.dumps(data)

	#add the annotation link
	try:
		req = urllib2.Request(url,data,hdr)
	except urllib2.HTTPError, e: 
		print "HTTP error: $d" % e.code
	except urllib2.URLError, e:
		print "Network error: $s" % e.reason.args[1]
	response = urllib2.urlopen(req)	
	
	#wait for n seconds
	#time.sleep(n)

print "Added {} new objects!".format(len(astro.entries[0:end]))
