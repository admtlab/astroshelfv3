'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

import MySQLdb as mdb

class MySQL:

    def __init__(self):
        self.hostname = 'db10.cs.pitt.edu'
        self.username = 'dibao'
        self.password = 'bamboo@2012'
        self.database = 'new_astroshelf'
        self.conn = None
        self.curs = None

    def connect(self):
        self.conn = mdb.connect(self.hostname, self.username, self.password, self.database)
        self.conn.autocommit(True)
        self.curs = self.conn.cursor(mdb.cursors.DictCursor)

    def close(self):
        self.curs.close()
        self.conn.close()

    def retrieve(self, userID, flag):
        query = 'SELECT * FROM `SN_trains` WHERE `train_owner_id` = %d' % userID
        self.curs.execute(query)
        res = []
        if int(self.curs.rowcount) == 0:
            return res
        else:
            for i in range(int(self.curs.rowcount)):
                curr_row = self.curs.fetchone()

                label = int(curr_row["feedback"])
                feature = {}
                if flag == 0: # local
                    feature["w1"] = int(curr_row["weight1"])
                    feature["w2"] = int(curr_row["weight2"])
                    feature["w3"] = int(curr_row["weight3"])
                    feature["w4"] = int(curr_row["weight4"])
                else: # global
                    feature["w5"] = int(curr_row["weight5"])
                    feature["w6"] = int(curr_row["weight6"])
                    feature["w7"] = int(curr_row["weight7"])

                res.append([label, feature])

        return res

    def insert(self, userID, local_w, global_w, feedback):
        insert = 'INSERT INTO `SN_trains` (train_owner_id, feedback, weight1, ' +\
                 'weight2, weight3, weight4, weight5, weight6, weight7) VALUES ' +\
                 '(%d, %d, %d, %d, %d, %d, %d, %d, %d)' % (userID, feedback, local_w["w1"], local_w["w2"], local_w["w3"], local_w["w4"], global_w["w5"], global_w["w6"], global_w["w7"])
        self.curs.execute(insert)
        if int(self.curs.rowcount) == 1:
            return True
        else:
            return False
