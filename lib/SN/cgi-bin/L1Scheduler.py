'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

'''
# The global scheduler combined EDF, SRTF, LST algorithms,
# with dynamical weighting:
# w5 - EDF alg. weight
# w6 - SRTF alg. weight
# w7 - LST alg. weight
#
# --------------------
#
# _targeting_list = [SN1, SN2, ..., SNi, ..., SNn]
# _night_index, from 1st, 2nd, 3th, to nth night
# _night_capacity, how many minutes available for the night
'''

import heapq

class L1Scheduler:
    def __init__(self, _targeting_list, _night_map, _night_index, _night_capacity):
        self.tlist = _targeting_list
        self.nmap = _night_map
        self.ninde = _night_index
        self.ncapa = _night_capacity

        self.w5 = 1
        self.w6 = 1
        self.w7 = 1

        # generate 3 times the amount of night capacity candidates supernovae
        self.cutoff_ratio = 3

        # create a heap to maintain ordering
        self.h = []

        self.candidate_set = []

    def set_weight(self, _w5, _w6, _w7):
        self.w5 = _w5
        self.w6 = _w6
        self.w7 = _w7

    def set_cutoff_ratio(self, _c):
        self.cutoff_ratio = _c

    def schedule(self):

        # special case, no night capacity assigned to such night
        if self.ncapa == 0:
            return []
        
        for supnova in self.tlist:
            if supnova.available(self.ninde, self.nmap) is True:
                W = self.w5 * (1 / supnova.get_deadline(self.ninde, self.nmap)) + \
                    self.w6 * (1 / supnova.get_remaining_work(self.ninde, self.nmap)) + \
                    self.w7 * (1 / supnova.get_slack(self.ninde, self.nmap))
                heapq.heappush(self.h, (-W, supnova))

        candidate_set_capa = 0

        while candidate_set_capa < self.ncapa * self.cutoff_ratio and len(self.h) > 0:
            supnova = heapq.heappop(self.h)[1]
            self.candidate_set.append(supnova)
            candidate_set_capa += supnova.get_obsDuration(self.ninde, self.nmap)

        return self.candidate_set


if __name__ == "__main__":

    pass

# test code
'''
    import json
    import random
    from SN import SN
    
    json_f = open("sample_data.json")
    raw_data = json.load(json_f)
    json_f.close()
    
    #print(raw_data)
    target_list = []
                
    for item in raw_data:
            dict_O = item[0]
            dict_C = item[1]
            supnova = SN(dict_O, dict_C)
            for i in range(random.randrange(3)):
                supnova.update_status(i+2)
            #print(supnova)
            target_list.append(supnova)

    S1 = L1Scheduler(target_list, 5, 360)
    candi_set = S1.schedule()

    print(len(candi_set))
'''



