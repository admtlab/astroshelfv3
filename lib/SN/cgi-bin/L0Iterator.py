'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

'''
# Weight Iterator
# Input: 1) local/global weights tune 2) the file of training data
# Output: a configuration of weights with high probability to be positive
'''

import random
from Funcs.LR import LogisticRegression
from Funcs.MySQL import MySQL

class L0Iterator:
    def __init__(self, _user_id, _local_global = 0):
        self.local_range = 31
        self.global_range = 11
        self.userID = _user_id
        self.flag = _local_global
        
        self.size = 0
        
        self.data = []
        self.LR = LogisticRegression()
        self.iteration = 100
        
        # the probability threshold to be positive
        self.cutoff = 0.9
        self.counter = 100

        db = MySQL()
        db.connect()
        # Return value is [[label, {features...}]]
        data = db.retrieve(self.userID, self.flag)
        db.close()
        self.size = len(data)
        self.data = data    

        if self.size >= 10:
                self.LR.train(self.data, self.iteration)
                        
    def set_cutoff(self, _c):
        self.cutoff = _c
        
    def set_iter(self, _iter):
        self.iteration = _iter
        
    def get_weights(self):
        
        while True:
            if self.flag == 0: # local, 4 weights
                feature = {}
                feature["w1"] = random.randrange(self.local_range)
                feature["w2"] = random.randrange(self.local_range)
                feature["w3"] = random.randrange(self.local_range)
                feature["w4"] = random.randrange(self.local_range)
            else: # global, 3 weights
                feature = {}
                feature["w5"] = random.randrange(self.global_range)
                feature["w6"] = random.randrange(self.global_range)
                feature["w7"] = random.randrange(self.global_range)

            if (self.LR.classify(feature) >= self.cutoff) or (self.size < 10):
                return feature
            else:
                self.counter -= 1
                if self.counter <= 0:
                    if random.randint(0, 1) == 0:
                        self.cutoff -= 0.05
                    else:
                        self.cutoff = (self.cutoff / 3) + 0.2

if __name__ == "__main__":

# test code
    filename1 = "sample_local_w.txt"
    filename2 = "sample_global_w.txt"

    I0 = L0Iterator(filename2, 1)
    print(I0.get_weights())
