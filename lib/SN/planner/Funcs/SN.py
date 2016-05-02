'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

'''
# The Declaration of SN class
# dict_O = _Name, _RA, _Dec, _Type, _Redshift, _Mag, _Phase, _Priority
# dict_C = _obsDuration, _obsGap, _obsTimes
'''

import random
import datetime

class SN:
    def __init__(self, dict_O, dict_C):

        self.Name = dict_O['_Name']
        self.RA = dict_O['_RA']
        self.Dec = dict_O['_Dec']
        self.Type = dict_O['_Type']
        self.Redshift = dict_O['_Redshift']
        self.Mag = dict_O['_Mag']
        
        # input B-Peak instead of relative Phase
        self.BPeak = dict_O['_B-Peak']
        peak_L = self.BPeak.split('-')
        self.Peakdate = datetime.date(int(peak_L[0]), int(peak_L[1]), int(peak_L[2]))
        
        self.Priority = dict_O['_Priority']

        self.obsD = dict_C['_obsDuration']
        self.obsG = dict_C['_obsGap']
        self.obsT = dict_C['_obsTimes']

        # The observation of one supernova consists of multiple instances
        self.curr_status = {}
        self.curr_status['last_instance'] = 0 # the index of last finished instance
        self.curr_status['last_time'] = -1 # the time of last instance
        self.curr_status['remaining_instances'] = self.obsT # the number of remaining instances
        self.curr_status['missed_deadline'] = False

    def update_status(self, _night_index):
        self.curr_status['last_instance'] += 1
        self.curr_status['last_time'] = _night_index
        self.curr_status['remaining_instances'] -= 1

    def reset_status(self):
        self.curr_status['last_instance'] = 0
        self.curr_status['last_time'] = -1
        self.curr_status['remaining_instances'] = self.obsT
        self.curr_status['missed_deadline'] = False

    def available(self, _night_index, _night_map):
        
        date1 = _night_map[self.curr_status['last_time']]
        date2 = _night_map[_night_index]
        delta = date2 - date1
    
        if (self.curr_status['remaining_instances'] > 0) and \
           (self.curr_status['last_time'] == -1 or delta.days > self.obsG):
            return True
        else:
            return False

    # Days after peak, brightnesss, etc
    def get_deadline(self, _night_index, _night_map):
        '''
        # 1. at least three observations between 0-20 days after peak
        # 2. additional observations up to 100 days after peak would be useful
        '''
        start_date = _night_map[0]
        curr_date = _night_map[_night_index]
        phase = (start_date - self.Peakdate).days
        if self.curr_status["last_instance"] < 3:
                deadline = 20 - phase
        else:
                deadline = 100 - phase

        if (start_date + datetime.timedelta(days=deadline)) >= curr_date:
                if deadline == 0:
                        return 0.01
                else:
                        return deadline
        else: # miss the deadline!!!
                self.curr_status['missed_deadline'] = True
                return -0.01

    # Amount of work (# of minutes) rest to finish
    def get_remaining_work(self):
        
        remaining = self.curr_status['remaining_instances'] * self.obsD
        if remaining > 0:
                return remaining
        else: # already finished!!!
                return -0.01

    # Amount of time (# of nights) left after a job if was started now
    def get_slack(self, _night_index, _night_map):
        
        relative_deadline = self.get_deadline(_night_index, _night_map)
        if relative_deadline == -0.01:
            return -0.01
        elif relative_deadline == 0.01:
            if self.curr_status['remaining_instances'] <= 1:
                return 0.01
            else:
                return -0.01
        else:
            absolute_deadline = _night_map[0] + datetime.timedelta(days=relative_deadline)
            d = 0
            for i in range(len(_night_map) - 1):
                if absolute_deadline >= _night_map[i] and \
                   absolute_deadline < _night_map[i+1]:
                    d = i
                    break
            else:
                d = len(_night_map) - 1            
        
            t = _night_index
            c = self.curr_status['remaining_instances']
            slack = (d - t + 1) - c
            if slack > 0:
                    return slack
            elif slack == 0:
                    return 0.01
            else: # no slack, and cannot finish even if start now
                    return -0.01
        
    # The (start_t, end_t) window for observation in the particular night
    def get_obsWindow(self, _night_index, _night_map):
        '''
        # 1. The observation window is the period of time during which
        # the object is no more than 60 degrees away from zenith
        # (i.e., straight overhead).
        # 2. You need to know the Local Sidereal Time (the Right Ascension
        # of a star directly overhead), and the latitude of your observatory
        # (which will give the Declination of a star directly overhead).
        '''
        # Need to plug-in third-party utility later
        # Now it's done by simulation

        #return (0, 360)

        begin = 0
        end = 360
        simu_fact1 = int(self.RA - 150)
        simu_fact2 = int(self.Dec)
        if simu_fact1 >= 0:
                begin = random.randint(0, simu_fact1)
        else:
                begin = random.randint(simu_fact1, -simu_fact1)
                if begin < 0:
                        begin = 0

        if simu_fact2 >= 0:
                end = 360 - random.randint(0, simu_fact2)
        else:
                end = 360 - random.randint(simu_fact2, -simu_fact2)
                if end > 360:
                        end = 360
        return (begin, end)

    def get_obsDuration(self):
        return self.obsD

    def get_obsGap(self):
        return self.obsG
        
    def get_obsTimes(self):
        return self.obsT

    def get_Priority(self):
        return self.Priority

    def __str__(self):
        output = "<Object " + self.Name + " (" + str(self.RA) + ", " + str(self.Dec) + ")>\n"
        output += "Type: " + self.Type + "\tRedshift: " + str(self.Redshift) + "\tMag: " + str(self.Mag) + "\tPhase: " + str(self.Phase)
        output += "\tPriority: " + str(self.Priority) + "\n"
        output += "<Observation constraints>\n"
        output += "Duration: " + str(self.obsD) + "\tGap: " + str(self.obsG) + "\tTimes: " + str(self.obsT) + "\n"
        output += "------------------------\n"
        return output

    def __lt__(self, other):
        if type(self) == type(other):
                return self.Redshift < other.Redshift

if __name__ == "__main__":

    import json
    
    json_f = open("sample_data.json")
    raw_data = json.load(json_f)
    json_f.close()
    
    #print(raw_data)

    for item in raw_data:
            dict_O = item[0]
            dict_C = item[1]
            supnova = SN(dict_O, dict_C)
            print(supnova)
'''
    dict_O = {
            "_RA" : 35.178,
            "_Dec" : -5.449,
            "_Type" : "Ia",
            "_Redshift" : 0.02,
            "_Mag" : 17.8,
            "_Phase" : 24, # ~24 days after peek
            "_Priority" : 0.85 # priority between 0 - 1
        }

    dict_C = {
            "_obsDuration" : 35, # 35 minutes
            "_obsGap" : 2, # 2 days gap between instances
            "_obsTimes" : 3
        }

    SN1 = SN(dict_O, dict_C)
    print(SN1)
    # assuming schedule SN1 in 5th night
    print (SN1.get_deadline(5))
    print (SN1.get_remaining_work(5))
    print (SN1.get_slack(5))
    SN1.update_status(5) # decided to schedule SN1
    print (SN1.get_deadline(6))
    print (SN1.get_remaining_work(6))
    print (SN1.get_slack(6))
'''
    
