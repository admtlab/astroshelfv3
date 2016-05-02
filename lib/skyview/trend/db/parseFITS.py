import os, sys, json
import numpy as np
import pyfits as pf
import MySQLdb as mdb
import dbinfo as DB

try:
	# names of the files to be parsed
	fileNames  = json.loads(sys.argv[1])
	my_id = json.loads(sys.argv[2])
	
	# login info for the DB
	info = DB.getInfo()
	
	# Open database connection
	db = mdb.connect( info["host"], info["username"],
		info["password"], info["dbName"] )
	
	# prepare a cursor object using cursor() method
	cursor = db.cursor()
	
	# numRows = cursor.execute("SELECT * FROM sdssSpectra2")
	# 			
	# rows = cursor.fetchall()
	# for row in rows:
	# 	s = ""
	# 	for col in row:
	# 		s += str(col) + " "
	# 	print s 
	# 	print "\n\n"
			
except e:
    
	print "Error %d: %s" % (e.args[0],e.args[1])
	sys.exit(1)

finally:
	
	# array of specOBJID
	spec_objid = []
	#iterate over the fits files
	for f in range(0,len(fileNames)):
		
		files = ((fileNames[f].split("spec-"))[1].split("."))[0] # gets the name for the hash
		#fileNames = "spec-2437-53848-0345.fits"
		#files = ((fileNames[f].split("spec-"))[1].split("."))[0] # gets the name for the hash
		
		main_table = "INSERT INTO sdssSpectra (ID,SIZE,Z,OBJID,RA,DECL,SPEC_OBJID,"
		main_table += "MAX_WAVE,MIN_WAVE,MAX_FLUX,MIN_FLUX,MAX_COMMON,MIN_COMMON) "
		main_table += "VALUES('" + str(files) + "',"
		
		# insert ID into main table
		path = '/u/astro/images/TREND/' + str(my_id) + '/fits/' + fileNames[f]	  # path of fits file
		
		# PYFits Parsing
		try:
		
			hdulist = pf.open( path ) # get fits class object
		
		except:
			continue

		data = hdulist[1] # get the data header
		data = data.data # get data
		
		#redshift (Z)
		redshift = hdulist[2].data['Z'][0]
		
		# ra and dec
		ra = hdulist[0].header['RA']
		dec = hdulist[0].header['DEC']
		
		#spec_objID
		spec_ob = hdulist[0].header['SPEC_ID']
		spec_objid.append(str(spec_ob))
		
		wavelength = data['loglam'] # log_10(wavelength)
		flux = data['flux'] # flux of the spectra
		
		length = len(flux) # number of values in the flux
		
		sql = "INSERT INTO" # begginning of query
		cols_wave = "(ID," # set initial value
		vals_wave = "VALUES('" + str(files) + "', " # set initial value
		
		cols_flux = "(ID," # set initial value
		vals_flux = "VALUES('" + str(files) + "', " # set initial value
		
		max_wave = wavelength[0]
		min_wave = wavelength[0]
		max_flux = flux[0]
		min_flux = flux[0]
		
		for i in range( (wavelength.shape)[0] ): # iterate over the wavelength
			
			if wavelength[i] > max_wave:
				max_wave = wavelength[i];
			if wavelength[i] < min_wave:
				min_wave = wavelength[i];
			
			if flux[i] > max_flux: 
				max_flux = flux[i];
			if flux[i] < min_flux:
				min_flux = flux[i];
			
			if i < 1000: # if index is < 1000
				
				cols_wave += "w" + str(i) + ", " # 'w# ' -- add column number to string
				vals_wave += str(pow(10,wavelength[i])) + ", " # 10^wavelength == Angstrom -- add to values string
				
				cols_flux += "f" + str(i) + ", " # 'f# ' -- add column number to string
				vals_flux += str(flux[i]) + ", " # flux -- add to values string
				
			elif i < 2000: # if index is < 2000
				
				if i == 1000: # we have finished with the first table's entries
					
					# wavelength					
					table = sql + " sdssSpectraWaveLen1" # select table to insert into
					cols_wave = cols_wave[:-2] + ")" # eliminate trailing comma and add )
					vals_wave = vals_wave[:-2] + ")" # eliminate trailing comma and add )
					
					insert_wave1 = table + cols_wave + vals_wave
					
					#flux
					table = sql + " sdssSpectraFlux1" # select table to insert into
					cols_flux = cols_flux[:-2] + ")" # eliminate trailing comma and add )
					vals_flux = vals_flux[:-2] + ")" # eliminate trailing comma and add )
					
					insert_flux1 = table + cols_flux + vals_flux
					
					cols_wave = "(ID," # reset columns string
					vals_wave = "VALUES('" + str(files) + "', " # reset values string
					
					cols_flux = "(ID," # reset columns string
					vals_flux = "VALUES('" + str(files) + "', " # reset values string
					
				cols_wave += "w" + str(i) + ", " # 'w# ' -- add column number to string
				vals_wave += str(pow(10,wavelength[i])) + ", " # 10^wavelength == Angstrom -- add to values string
				
				cols_flux += "f" + str(i) + ", " # 'f# ' -- add column number to string
				vals_flux += str(flux[i]) + ", " # flux -- add to values string
				
			elif i < 3000: # if index is < 3000
				
				if i == 2000: # we have finished with the second table's entries
									
					# wavelength					
					table = sql + " sdssSpectraWaveLen2" # select table to insert into
					cols_wave = cols_wave[:-2] + ")" # eliminate trailing comma and add )
					vals_wave = vals_wave[:-2] + ")" # eliminate trailing comma and add )

					insert_wave2 = table + cols_wave + vals_wave

					#flux
					table = sql + " sdssSpectraFlux2" # select table to insert into
					cols_flux = cols_flux[:-2] + ")" # eliminate trailing comma and add )
					vals_flux = vals_flux[:-2] + ")" # eliminate trailing comma and add )

					insert_flux2 = table + cols_flux + vals_flux

					cols_wave = "(ID," # reset columns string
					vals_wave = "VALUES('" + str(files) + "', " # reset values string

					cols_flux = "(ID," # reset columns string
					vals_flux = "VALUES('" + str(files) + "', " # reset values string

				cols_wave += "w" + str(i) + ", " # 'w# ' -- add column number to string
				vals_wave += str(pow(10,wavelength[i])) + ", " # 10^wavelength == Angstrom -- add to values string

				cols_flux += "f" + str(i) + ", " # 'f# ' -- add column number to string
				vals_flux += str(flux[i]) + ", " # flux -- add to values string
				
			else: # if index is < 4000
				
				if i == 3000: # we have finished with the third table's entries
										
					# wavelength					
					table = sql + " sdssSpectraWaveLen3" # select table to insert into
					cols_wave = cols_wave[:-2] + ")" # eliminate trailing comma and add )
					vals_wave = vals_wave[:-2] + ")" # eliminate trailing comma and add )
					
					insert_wave3 = table + cols_wave + vals_wave
					
					#flux
					table = sql + " sdssSpectraFlux3" # select table to insert into
					cols_flux = cols_flux[:-2] + ")" # eliminate trailing comma and add )
					vals_flux = vals_flux[:-2] + ")" # eliminate trailing comma and add )
					
					insert_flux3 = table + cols_flux + vals_flux
					
					cols_wave = "(ID," # reset columns string
					vals_wave = "VALUES('" + str(files) + "', " # reset values string
					
					cols_flux = "(ID," # reset columns string
					vals_flux = "VALUES('" + str(files) + "', " # reset values string
					
				cols_wave += "w" + str(i) + ", " # 'w# ' -- add column number to string
				vals_wave += str(pow(10,wavelength[i])) + ", " # 10^wavelength == Angstrom -- add to values string
				
				cols_flux += "f" + str(i) + ", " # 'f# ' -- add column number to string
				vals_flux += str(flux[i]) + ", " # flux -- add to values string
			
			##### END FOR LOOP
		
		# we have finished with the final table's entries
		
		table = sql + " sdssSpectraWaveLen4"
		cols_wave = cols_wave[:-2] + ")" # eliminate trailing comma and add )
		vals_wave = vals_wave[:-2] + ")" # eliminate trailing comma and add )
		
		insert_wave4 = table + cols_wave + vals_wave
		
		table = sql + " sdssSpectraFlux4"
		cols_flux = cols_flux[:-2] + ")" # eliminate trailing comma and add )
		vals_flux = vals_flux[:-2] + ")" # eliminate trailing comma and add )
		
		insert_flux4 = table + cols_flux + vals_flux
		
		if wavelength[i] <= 0 :
			break
				
		main_table += str(length) + "," + str(redshift) + "," + "00000000000000000000," + str(ra) + "," + str(dec)
		main_table += "," + str(spec_ob) + "," + str(pow(10,max_wave)) + "," + str( pow(10,min_wave) )
		main_table += "," + str(max_flux) + "," + str(min_flux) + "," + str( pow(10,max_wave) / (1.0+redshift) ) 
		main_table += "," + str( pow(10,min_wave)/(1.0+redshift) ) + ")"
		
		cursor.execute(main_table) # execute the query
		
		# insert wavelengths
		cursor.execute(insert_wave1) # execute the query
		cursor.execute(insert_wave2) # execute the query
		cursor.execute(insert_wave3) # execute the query
		cursor.execute(insert_wave4) # execute the query
		
		# insert flux
		cursor.execute(insert_flux1) # execute the query
		cursor.execute(insert_flux2) # execute the query
		cursor.execute(insert_flux3) # execute the query
		cursor.execute(insert_flux4) # execute the query
		
		db.commit() # commit the insert
		
	##### END FOR LOOP OVER FILES
	
	print json.dumps(spec_objid)
	
##### END FINALLY CLAUSE
