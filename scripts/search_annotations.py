import urllib, urllib2, json,operator

#this script searches for annotations by keyword

#REST url & header for searching
url = 'http://astro.cs.pitt.edu:8080/astroservice/v2/annotation/search?acting_user=5462125&limit=1000&keyword='

#set keyword here
keyword = 'wrong' 

#define full url
fullurl = url + keyword

#send GET request
try:
	req = urllib2.Request(fullurl)
	req.add_header("Accept","application/json")
	response = urllib2.urlopen(req)
except urllib2.HTTPError, e: 
	print "HTTP error:", e.code
except urllib2.URLError, e:
	print "Network error: ", e.reason.args[1]

results = json.loads(response.read())

print ' '
print "Searching all annotations with the keyword: "+keyword 
print ' '
print "Found",len(results),"results matching search criteria!"
print ' '

#get indices of certain tags
f = operator.itemgetter(0)
item=results[0].items()
inAnnoVal = map(f,item).index('annoValue')
inObj = map(f,item).index('objectInfoCollection')

#print results 
for i in range(len(results)):
	item = results[i].items()
	obj = item[inObj][1][0]
	print '--------------------------------'
	print 'Annotation '+str(i+1)
	print 'Object at RA,DEC: {0[ra]:.3f},{0[dec]:.3f}'.format(obj)
	print 'Survey: '+obj['surveyId']['surveyName'] 
	print "Note: "+item[inAnnoVal][1]

#output results to a file
f = open('results_annotation_search.csv','w')
f.write('Annotation Search Results based on Keyword: {} \n'.format(keyword))
f.write('Found {} results! \n'.format(len(results)))
for i in range(len(results)):
	item = results[i].items()
	obj = item[inObj][1][0]
	f.write('--------------------------------\n')
	f.write('Annotation '+str(i+1)+'\n')
	f.write('Object at RA,DEC: {0[ra]:.3f},{0[dec]:.3f} \n'.format(obj))
	f.write('Survey: '+obj['surveyId']['surveyName']+'\n')
	f.write("Note: "+item[inAnnoVal][1]+'\n')
f.close()



