import sys
import MySQLdb as mdb
import dbinfo as DB

def hash(plate, mjd, fiberID):
	# this is a terrible hash function :)
	return plate+mdj+fiberID

info = DB.getInfo()

db = None

try:
	# Open database connection
	db = mdb.connect( info["host"], info["username"],
		info["password"], info["dbName"] )

	# prepare a cursor object using cursor() method
	cursor = db.cursor()
 
except mdb.Error, e:
	 
	print "Error %d: %s" % (e.args[0],e.args[1])
	sys.exit(1)

finally:
	
	# Drop table if it already exist using execute() method.
	# ignore foreign keys when deleting
	
	cursor.execute("SET FOREIGN_KEY_CHECKS=0")
	cursor.execute("DROP TABLE IF EXISTS sdssSpectra")
	
	## add main table
	table = """CREATE TABLE sdssSpectra(
        OBJID varchar(20) NOT NULL,
        SPEC_OBJID varchar(20) NOT NULL,
        RA FLOAT NOT NULL DEFAULT 0,
        DECL FLOAT NOT NULL DEFAULT 0,
        SPEC_CLASS INT NOT NULL DEFAULT 0,
        SIZE INT NOT NULL DEFAULT 0,
        Z_MAG FLOAT NOT NULL DEFAULT 0,
        G_MAG FLOAT NOT NULL DEFAULT 0,
        R_MAG FLOAT NOT NULL DEFAULT 0,
        I_MAG FLOAT NOT NULL DEFAULT 0,
        U_MAG FLOAT NOT NULL DEFAULT 0,
        REDSHIFT FLOAT NOT NULL DEFAULT 0,
		MAX_WAVE FLOAT NOT NULL DEFAULT 0,
		MIN_WAVE FLOAT NOT NULL DEFAULT 0,
		MAX_REST FLOAT NOT NULL DEFAULT 0,
		MIN_REST FLOAT NOT NULL DEFAULT 0,
		PRIMARY KEY (OBJID) )"""
	
	cursor.execute(table)
	
	for j in range(1,7): # create 6 subtables for 
		
		# drop each of the tables
		table = "DROP TABLE IF EXISTS sdssSpectraWaveLen" + str(j)
		cursor.execute(table)

		# Create table as per requirement
		sql = """CREATE TABLE sdssSpectraWaveLen""" 
		sql += str(j)
		sql += """(OBJID varchar(20) NOT NULL, """
		
		# iterate over the columns 
		for i in range( (j-1)*750, j*750):
			sql += """w""" + str(i) 
			sql += """ DOUBLE NOT NULL DEFAULT 0, """
				
		#sql = sql[:-2] # eliminate last comma
		
		# add constraints
		sql += """PRIMARY KEY(OBJID), """
		sql += """FOREIGN KEY (OBJID) REFERENCES sdssSpectra(OBJID)) """
		#print sql
		try:	
			cursor.execute(sql)
		except Exception as e:
        		print "--->SQL Error: %s" % e    
	for j in range(1,7): # create 6 subtables for 

		# drop each of the tables
		table = "DROP TABLE IF EXISTS sdssSpectraFlux" + str(j)
		cursor.execute(table)

		# Create table as per requirement
		sql = """CREATE TABLE sdssSpectraFlux""" 
		sql += str(j)
		sql += """(OBJID varchar(20) NOT NULL, """

		# iterate over the columns 
		for i in range( (j-1)*750, j*750):
			sql += """f""" + str(i) 
			sql += """ FLOAT NOT NULL DEFAULT 0, """
			
		#sql = sql[:-2] # eliminate last comma

		# add constraints
		sql += """PRIMARY KEY(OBJID), """
		sql += """FOREIGN KEY (OBJID) REFERENCES sdssSpectra(OBJID)) """
		
		#print sql
		try:
			cursor.execute(sql)
		except Exception as e:
			print "--->SQL Error: %s" % e
db.commit()
# disconnect from server
db.close()
