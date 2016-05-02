'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

'''
# Input: a Config object with information of targeting list, night info, etc
# Output: a dict of date => a list of SNs, basically the planning suggestion for user
'''

import sys
import json
import datetime
import random

from Funcs.Config import Config
from Funcs.SN import SN

from L0Scheduler import L0Scheduler
from L1Scheduler import L1Scheduler
from L0Iterator import L0Iterator
from L1Iterator import L1Iterator

def main_loop(config):

    result = {}
    
    # Step 1: get local/global weights for scheduling
    if config.train is True:
        I0 = L0Iterator(config.local_log, 0)
        local_w = I0.get_weights()
        I0 = L0Iterator(config.global_log, 1)
        global_w = I0.get_weights()
    else:
        local_w = {}
        global_w = {}
        local_w["w1"] = random.randrange(31)
        local_w["w2"] = random.randrange(31)
        local_w["w3"] = random.randrange(31)
        local_w["w4"] = random.randrange(31)
        global_w["w5"] = random.randrange(11)
        global_w["w6"] = random.randrange(11)
        global_w["w7"] = random.randrange(11)
    #'''
    print(sorted(local_w.items()))
    print(sorted(global_w.items()))
    raw_input()
    #'''
    # Step 2: get capacity distribution for each night
    I1 = L1Iterator(config.target_list, config.night_map, config.number_nights, config.number_hours, config.strategy)
    capacity_n = I1.iterator()
    if capacity_n == []:
        return {}
    #'''
    print(capacity_n)
    raw_input()
    #'''
    # Step 3 & 4, iterate to schedule each night
    res = {}
    for n in range(config.number_nights):
        S1 = L1Scheduler(config.target_list, config.night_map, n, capacity_n[n])
        S1.set_weight(global_w["w5"], global_w["w6"], global_w["w7"])
        candi_set = S1.schedule()
        
        S0 = L0Scheduler(candi_set, config.night_map, n, capacity_n[n])
        S0.set_weight(local_w["w1"], local_w["w2"], local_w["w3"], local_w["w4"])
        filter_set = S0.schedule(config.algorithm)
        
        res[str(config.night_map[n])] = filter_set

        # updating SN info in targeting list
        for supnova in filter_set:
                index = config.target_list.index(supnova)
                config.target_list[index].update_status(n)
                        
    result["result"] = res
    result["local_w"] = local_w
    result["global_w"] = global_w

    return result


def print_result1(res):
    print("\n\n########################################\n\n")
    
    for key in sorted(res.keys()):
            line = "%s:\t" % key
            for sup in res[key]:
                    line += "%s, " % sup.Name
            if len(res[key]) == 0:
                    print(line + "Null")
            else:
                    print(line[:-2])
                    
    print("\n\n########################################\n\n")

def print_result2(res):
    print("\n\n########################################\n\n")

    # preprocessing
    date_str_list = sorted(res.keys())
    inverted_table = {}
    for date_str in date_str_list:
        for sup in res[date_str]:
            if inverted_table.has_key(sup.Name):
                inverted_table[sup.Name].append(date_str + '_')
            else:
                inverted_table[sup.Name] = []
                inverted_table[sup.Name].append(str(sup.Redshift))
                inverted_table[sup.Name].append(sup.BPeak)
                inverted_table[sup.Name].append(str(sup.obsD))
                inverted_table[sup.Name].append(date_str + '_')

    line = "Name\tz\tB-Peak\t"
    for date_str in date_str_list:
        line += date_str + "\t"
    line = line[:-1]
    print(line)

    for sup_str in sorted(inverted_table.keys()):

        obsD = inverted_table[sup_str][2]
        
        line = sup_str + "\t"
        line += inverted_table[sup_str][0] + "\t"
        line += inverted_table[sup_str][1] + "\t"
        for date_str in date_str_list:
            if date_str + '_' in inverted_table[sup_str]:
                line += obsD + "\t"
            else:
                line += "-\t"
        line = line[:-1]
        print(line)
    
    print("\n\n########################################\n\n")


def print_status(res):
    print("\n\n########################################\n\n")

    miss_deadline = []
    incomplete = []

    date_str_list = sorted(res.keys())
    supnova_table = {}
    for date_str in date_str_list:
        for sup in res[date_str]:
            if supnova_table.has_key(sup.Name):
                pass
            else:
                supnova_table[sup.Name] = sup
                
    for supname in sorted(supnova_table.keys()):
        if supnova_table[supname].curr_status['missed_deadline']:
            miss_deadline.append(supnova_table[supname])
        if supnova_table[supname].curr_status['remaining_instances'] > 0:
            incomplete.append(supnova_table[supname])

    print("%d SN Observation missed deadline! Listed as following:\n" % len(miss_deadline))
    the_str = ""
    for sup in miss_deadline:
        the_str += sup.Name + ", "
    the_str = the_str[:-2] + "\n"
    print(the_str)

    print("%d SN Observation didn't finish completely! Listed as following:\n" % len(incomplete))
    the_str = ""
    for sup in incomplete:
        the_str += sup.Name + ", "
    the_str = the_str[:-2] + "\n"
    print(the_str)

    all_SN = len(supnova_table)
    outer_SN = [sn.Name for sn in miss_deadline]
    for sn in incomplete:
        if sn.Name not in outer_SN:
            outer_SN.append(sn)

    percent = (1 - (len(outer_SN) / float(all_SN))) * 100
    print("%.2f%% SN Observations scheduled successfully!" % percent)

    print("\n\n########################################\n\n")

'''
#########################################
# Here begins the driver program
# Interact with client
#########################################
'''
if len(sys.argv) < 2:
        sFile1 = "./Input/sample_input_part1.json"
        sFile2 = "./Input/sample_input_part2.json" # sip2exp.json
        lFile_local = "./Trainning/sample_local_w.txt"
        lFile_global = "./Trainning/sample_global_w.txt"
        
        gStrategy = 0
        gAlgorithm = 2
        gTrainning = False
else:
        '''
        # python main.py -f F1 F2 F3 F4 -s [0|1|2] -alg [0|1|2|3] -t [0|1]
        '''
        if len(sys.argv) != 12:
                print("python main.py -f F1 F2 F3 F4 -s [0|1|2] -alg [0|1|2|3] -t [0|1]")
                sys.exit(1)
        else:
                sFile1 = sys.argv[2]
                sFile2 = sys.argv[3]
                lFile_local = sys.argv[4]
                lFile_global = sys.argv[5]

                gStrategy = sys.argv[7]
                gAlgorithm = sys.argv[9]
                gTrainning = sys.argv[11]

# loading json input part1
_TL = []
json_f = open(sFile1)
raw_data = json.load(json_f)

for d in raw_data:
        dict_O = d[0]
        dict_C = d[1]
        supnova = SN(dict_O, dict_C)
        _TL.append(supnova)
        
json_f.close()
# end of loading

# loading json input part2
_NM = []
json_f2 = open(sFile2)
raw_data2 = json.load(json_f2)

for date_str in raw_data2["LNights"]:
    date_L = date_str.split('-')
    date = datetime.date(int(date_L[0]), int(date_L[1]), int(date_L[2]))
    _NM.append(date)

_NN = raw_data2["NNights"]
_NH = raw_data2["NHours"]

json_f2.close()
# end of loading

_SG = gStrategy
_ALG = gAlgorithm
_TRA = gTrainning

_localF = lFile_local
_globalF = lFile_global


config = Config()
config.set_TL(_TL)
config.set_NM(_NM)
config.set_NN(_NN)
config.set_NH(_NH)
config.set_SG(_SG)
config.set_alg(_ALG)
config.set_tra(_TRA)
config.set_log(_localF, _globalF)

'''
print(config.target_list)
print(config.night_map)
print(config.number_nights)
print(config.number_hours)
print(config.strategy)
print(config.algorithm)
print(config.local_log)
print(config.global_log)
sys.exit(0)
'''

#for i in range(200):
while True:
        result = main_loop(config)
        if result == {}:
                print("Cannot provide observation plan suggestion, sorry....")
                print("Please adjust input and try again...")
                break
        
        print_result1(result["result"])
        raw_input()
        print_result2(result["result"])
        raw_input()
        print_status(result["result"])
        raw_input()

        # reset SN info in targeting list
        for supnova in config.target_list:
                supnova.reset_status()

        # get user's feedback
        #'''
        comment = raw_input("Are you satisfied with the observation plan suggestion?\nPlease input [yes|Y] or [no|N]\n")
        if comment == "yes" or comment == "Y":
                flag = 1
        elif comment == "no" or comment == "N":
                flag = 0
        else:
                print("Invalid input...")
                break
        #'''
        '''
        for sup in result["result"]["2013-03-21"]:
            if sup.Name == "2014E":
                flag = 0
                break
        else:
            flag = 1
        #print(flag)
        #raw_input()
        '''
        
        # record trainning data
        if config.train is True:
            fh1 = open(config.local_log, "a")
            output_str = "%d\t%d\t%d\t%d\t%d\n" % (flag, result["local_w"]["w1"], result["local_w"]["w2"], result["local_w"]["w3"], result["local_w"]["w4"])
            fh1.write(output_str)
            fh1.close()

            fh2 = open(config.global_log, "a")
            output_str = "%d\t%d\t%d\t%d\n" % (flag, result["global_w"]["w5"], result["global_w"]["w6"], result["global_w"]["w7"])
            fh2.write(output_str)
            fh2.close()

        #'''
        # iterate through next plan suggestion
        continues = raw_input("Would you like to see more observation plan suggestions?\nPlease input [yes|Y] or [no|N]\n")
        if continues == "yes" or continues == "Y":
                continue
        elif continues == "no" or continues == "N":
                break
        else:
                print("Invalid input...")
                break
        #'''
