'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

'''
# Capacity Iterator
# Input: 1) normal user input 2) capacity distribution strategy
#
# (0) full capacity first and decrease - default option
# (1) evenly distributed capacity for all nights
# (2) most reserved capacity first and increase
#
# Output: a list of night capacity from 1st, 2nd, ..., to nth night
'''

import math
import datetime

class L1Iterator:
    def __init__(self, _targeting_list, _night_map, _night_num, _night_capa_map, _strategy = 0):
        self.tlist = _targeting_list
        self.nmap = _night_map
        self.nnum = _night_num
        #self.ncapa = _night_capacity * 60 # convert hours to minutes
        self.ncapamap = _night_capa_map
        self.strategy = _strategy
        
        # the capacity consumption cannot over 85% of the total full night capacity
        self.cutoff_ratio1 = 0.95               
        # the slack of capacity for each night, concerning temporal constraints
        self.cutoff_ratio2 = 0.10
        
        self.capa_distribution = []

    def set_cutoff_ratio1(self, _c1):
        self.cutoff_ratio1 = _c1

    def set_cutoff_ratio2(self, _c2):
        self.cutoff_ratio2 = _c2

    def get_capa_in_avg(self, sup):
        low = 0
        high = len(self.nmap) - 1
        mid = (low + high) / 2

        duration1 = sup.get_obsDuration(low, self.nmap)
        duration2 = sup.get_obsDuration(high, self.nmap)
        duration3 = sup.get_obsDuration(mid, self.nmap)

        return (duration1 + duration2 + duration3) / 3
        
    def get_capa_consume(self):
        capa_consume = 0
        for supnova in self.tlist:
            duration = self.get_capa_in_avg(supnova)
            times = supnova.get_obsTimes()
            capa_consume += (duration * times)
            
        return capa_consume
        
    def iterator(self):

        '''
        ###################################
        #return self.ncapamap
        ###################################
        '''

        if (sum(self.ncapamap) * self.cutoff_ratio1 < self.get_capa_consume()) and False:
            pass
        else:
            # Distribution 1: full capacity all
            if self.strategy == 0:
                self.capa_distribution = self.ncapamap
            
            # Distribution 2: around evenly distributed capacity
            elif self.strategy == 1:
                
                # initalize each night capacity to zero
                for i in range(self.nnum):
                        self.capa_distribution.append(0)

                # iterate each supernova to add their capacity
                for supnova in self.tlist:
                        # cannot make the deadline
                        if type(supnova.get_deadline(0, self.nmap)) is type(0.1):
                                base = (self.get_capa_in_avg(supnova) * supnova.get_obsTimes()) / self.nnum
                                for i in range(self.nnum):
                                        self.capa_distribution[i] += base
                        # case 1. first deadline out of range
                        # case 2. second deadline out of range
                        # case 3. first and second deadline both in range
                        else:
                                the_range1 = supnova.get_deadline(0, self.nmap)
                                absolute_deadline1 = self.nmap[0] + datetime.timedelta(days=the_range1)
                                the_real_range1 = 0
                                for i in range(len(self.nmap) - 1):
                                    if absolute_deadline1 >= self.nmap[i] and \
                                       absolute_deadline1 < self.nmap[i + 1]:
                                        the_real_range1 = i
                                        break
                                else:
                                    the_real_range1 = self.nnum - 1

                                the_range2 = the_range1 + 80
                                absolute_deadline2 = self.nmap[0] + datetime.timedelta(days=the_range2)
                                the_real_range2 = 0
                                for i in range(len(self.nmap) - 1):
                                    if absolute_deadline2 >= self.nmap[i] and \
                                       absolute_deadline2 < self.nmap[i + 1]:
                                        the_real_range2 = i
                                        break
                                else:
                                    the_real_range2 = self.nnum - 1                                

                                if supnova.get_obsTimes() > 3:
                                    base1 = (self.get_capa_in_avg(supnova) * 3) / (the_real_range1 + 1)
                                    base2 = (self.get_capa_in_avg(supnova) * (supnova.get_obsTimes() - 3)) / (the_real_range2 + 1)
                                else:
                                    base1 = (self.get_capa_in_avg(supnova) * supnova.get_obsTimes()) / (the_real_range1 + 1)
                                    base2 = 0
                            
                                for i in range(the_real_range1 + 1):
                                        self.capa_distribution[i] += base1
                                for i in range(the_real_range2 + 1):
                                        self.capa_distribution[i] += base2
                                        
                # adjust each night capacity
                for i in range(self.nnum):
                        new_capa = math.ceil(self.capa_distribution[i] * (self.cutoff_ratio2 + 1))
                        if new_capa > self.ncapamap[i]:
                                new_capa = self.ncapamap[i]
                        self.capa_distribution[i] = int(new_capa)
                
                self.capa_distribution[0] = self.ncapamap[0]
                
                        
            # Distribution 3: reserve capacity and postpone current observation as much as possible
            elif self.strategy == 2:
                # initalize each night capacity to zero
                for i in range(self.nnum):
                        self.capa_distribution.append(0)
                        
                # iterate each supernova to add their capacity
                for supnova in self.tlist:
                        # cannot make the deadline
                        if type(supnova.get_deadline(0, self.nmap)) is type(0.1):
                                tmp_index = self.nnum - 1
                                for i in range(supnova.get_obsTimes()):
                                        self.capa_distribution[tmp_index] += self.get_capa_in_avg(supnova)

                                        if tmp_index == 0:
                                            continue
                                        k = 1
                                        while (self.nmap[tmp_index] - self.nmap[tmp_index - k]) <= \
                                              datetime.timedelta(days=supnova.get_obsGap()):
                                            k += 1
                                            if tmp_index - k == 0:
                                                break
                                        tmp_index -= k
                        else:
                                the_range1 = supnova.get_deadline(0, self.nmap)
                                absolute_deadline1 = self.nmap[0] + datetime.timedelta(days=the_range1)
                                tmp_index1 = 0
                                for i in range(len(self.nmap) - 1):
                                    if absolute_deadline1 >= self.nmap[i] and \
                                       absolute_deadline1 < self.nmap[i + 1]:
                                        tmp_index1 = i
                                        break
                                else:
                                    tmp_index1 = self.nnum - 1

                                the_range2 = the_range1 + 80
                                absolute_deadline2 = self.nmap[0] + datetime.timedelta(days=the_range2)
                                tmp_index2 = 0
                                for i in range(len(self.nmap) - 1):
                                    if absolute_deadline2 >= self.nmap[i] and \
                                       absolute_deadline2 < self.nmap[i + 1]:
                                        tmp_index2 = i
                                        break
                                else:
                                    tmp_index2 = self.nnum - 1 

                                if supnova.get_obsTimes() > 3:
                                    for i in range(3):
                                            self.capa_distribution[tmp_index1] += self.get_capa_in_avg(supnova)

                                            if tmp_index1 == 0:
                                                continue
                                            k = 1
                                            while (self.nmap[tmp_index1] - self.nmap[tmp_index1 - k]) <= \
                                                  datetime.timedelta(days=supnova.get_obsGap()):
                                                k += 1
                                                if tmp_index1 - k == 0:
                                                    break
                                            tmp_index1 -= k
                                                    
                                    for i in range(supnova.get_obsTimes() - 3):
                                            self.capa_distribution[tmp_index2] += self.get_capa_in_avg(supnova)

                                            if tmp_index2 == 0:
                                                continue
                                            k = 1
                                            while (self.nmap[tmp_index2] - self.nmap[tmp_index2 - k]) <= \
                                                  datetime.timedelta(days=supnova.get_obsGap()):
                                                k += 1
                                                if tmp_index2 - k == 0:
                                                    break
                                            tmp_index2 -= k
                                else:
                                    for i in range(supnova.get_obsTimes()):
                                            self.capa_distribution[tmp_index1] += self.get_capa_in_avg(supnova)

                                            if tmp_index1 == 0:
                                                continue
                                            k = 1
                                            while (self.nmap[tmp_index1] - self.nmap[tmp_index1 - k]) <= \
                                                  datetime.timedelta(days=supnova.get_obsGap()):
                                                k += 1
                                                if tmp_index1 - k == 0:
                                                    break
                                            tmp_index1 -= k

                # adjust each night capacity
                for i in range(self.nnum - 1):
                    index = self.nnum - i - 1
                    if self.capa_distribution[index] > self.ncapamap[index]:
                        more = self.capa_distribution[index] - self.ncapamap[index]
                        self.capa_distribution[index] = self.ncapamap[index]
                        self.capa_distribution[index - 1] += more
                
                for i in range(self.nnum):
                        new_capa = math.ceil(self.capa_distribution[i] * (self.cutoff_ratio2 + 1))
                        if new_capa > self.ncapamap[i]:
                                new_capa = self.ncapamap[i]
                        self.capa_distribution[i] = int(new_capa)
                
                self.capa_distribution[0] = self.ncapamap[0]
                
        return self.capa_distribution

if __name__ == "__main__":

# test code & experiment
    import json
    import random
    from Funcs.SN import SN
    
    json_f = open("./Input/sample_input_part1.json")
    raw_data = json.load(json_f)
    json_f.close()
    
    target_list = []
        
    for item in raw_data:
        dict_O = item[0]
        dict_C = item[1]
        supnova = SN(dict_O, dict_C)
        #print(supnova)
        target_list.append(supnova)

    json_f2 = open("./Input/sip2exp.json")
    raw_data2 = json.load(json_f2)
    json_f2.close()

    night_map = []
    
    for date_str in raw_data2["LNights"]:
        date_L = date_str.split('-')
        date = datetime.date(int(date_L[0]), int(date_L[1]), int(date_L[2]))
        night_map.append(date)
    
    I1 = L1Iterator(target_list, night_map, 20, 6, 2)
    capa_list = I1.iterator()
    print(capa_list)
    print(sum(capa_list))
