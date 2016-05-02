#!/usr/bin/env python

'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

'''
# Input: a Config object with information of targeting list, night info, etc
# Output: a dict of date => a list of SNs, basically the planning suggestion for user
'''

'''
# Running the planner as python CGI script
'''
import cgi
import cgitb; cgitb.enable() # for troubleshooting

import sys
import json
import datetime
import random

from Funcs.Config import Config
from Funcs.SN import SN
from Funcs.MySQL import MySQL

from L0Scheduler import L0Scheduler
from L1Scheduler import L1Scheduler
from L0Iterator import L0Iterator
from L1Iterator import L1Iterator



def main_loop(config, userID):

    result = {}
    
    # Step 1: get local/global weights for scheduling
    if config.train is True:
        I0 = L0Iterator(userID, 0)
        local_w = I0.get_weights()
        I0 = L0Iterator(userID, 1)
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
    '''
    print(sorted(local_w.items()))
    print(sorted(global_w.items()))
    raw_input()
    '''
    # Step 2: get capacity distribution for each night
    I1 = L1Iterator(config.target_list, config.night_map, config.number_nights, config.night_capa_map, config.strategy)
    capacity_n = I1.iterator()
    if capacity_n == []:
        return {}
    '''
    print(capacity_n)
    #raw_input()
    '''
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

def error_handler(error_msg):
    print "Content-type: text/plain"
    print
    print '{"error": "%s"}' % error_msg
    sys.exit(0)    

'''
#########################################
# Here starts the main program
# 1) request new plan  2) feedback current plan
#########################################
'''

_Form = cgi.FieldStorage()
_State = _Form.getvalue("state")
_Json_str = _Form.getvalue("json_str")

if (_State is None) or (_Json_str is None):
    error_handler("invalid request input...")

try:
    _Json_obj = json.loads(_Json_str)
except:
    error_handler("invalid JSON object...")

'''
#####################################
Our planner should provide the following 4 services,
as the response of client-side request, with "state" number 0 - 3:
[State 0] - the most common one, generating plan suggestion for a list of SN.
[State 1] - client-side send back user feedback for weights configuration.
[State 2] - partial planning, need extra information about each SN's state.
[State 3] - user plan validation, iterate through user's plan to check the feasiblity and performance.
#####################################
'''	

#'''	
try:
#'''
	if int(_State) == 0:
			_SG = int(_Json_obj["strategy"])
			_ALG = int(_Json_obj["algorithm"])
			_TRA = int(_Json_obj["trainning"])
			_userID = int(_Json_obj["user_id"])
			
			_TL = []
			for d in _Json_obj["data1"]:
					dict_O = d[0]
					dict_C = d[1]
					supnova = SN(dict_O, dict_C)
					_TL.append(supnova)
	
			_NM = []
			_NCM = [] # night capacity distribution map
			for dict_obj in sorted(_Json_obj["data2"]["LNights"]):
					for key in dict_obj.keys():
						date_L = key.split('-')
						date = datetime.date(int(date_L[0]), int(date_L[1]), int(date_L[2]))
						_NM.append(date)
						value = dict_obj[key]
						value_L = list(value)
						# half-night scenario
						if len(value_L) == 2:
							_NCM.append((int(value_L[0]) / 2) * 60)
						else:
							_NCM.append(int(value_L[0]) * 60)
			_NN = _Json_obj["data2"]["NNights"]
			#_NH = _Json_obj["data2"]["NHours"]
	
			config = Config()
			config.set_TL(_TL)
			config.set_NM(_NM)
			config.set_NN(_NN)
			config.set_NCM(_NCM)
			config.set_SG(_SG)
			config.set_alg(_ALG)
			config.set_tra(_TRA)
			#config.set_log(_localF, _globalF)
			
			print "Content-type: text/plain"
			print
	
			result = main_loop(config, _userID)
	
			'''
			for sup in _TL:
					print sup
			'''
			if result == {}:
					#print "Cannot provide observation plan suggestion, sorry...."
					#print "Please adjust input and try again..."
					print '{"error": "cannot provide observation plan, please adjust input and try again..."}'
			else:
					res_json = {}
					res_json["local_w"] = result["local_w"]
					res_json["global_w"] = result["global_w"]
					res_json["plan"] = {}
	
					for key in sorted(result["result"].keys()):
							res_json["plan"][key] = []
													
							for sup in _TL:
								sup_obj = {}
								sup_obj["name"] = sup.Name
								sup_obj["ra"] = sup.RA
								sup_obj["dec"] = sup.Dec
	
								date_L = key.split('-')
								the_date = datetime.date(int(date_L[0]), int(date_L[1]), int(date_L[2]))
								ninde = _NM.index(the_date)
								nmap = _NM
								sup_obj["duration"] = sup.get_obsDuration(ninde, nmap)
	
								if sup in result["result"][key]:
									sup_obj["flag"] = 1
								else:
									sup_obj["flag"] = 0
	
								res_json["plan"][key].append(sup_obj)                                      
	
					date_str_list = sorted(result["result"].keys())
					
					supnova_table = {}
					for date_str in date_str_list:
							for sup in result["result"][date_str]:
									if supnova_table.has_key(sup.Name):
											pass
									else:
											supnova_table[sup.Name] = sup
											
					miss_deadline = []
					incomplete = []
					for supname in sorted(supnova_table.keys()):
							sup_obj = {}
							sup_obj["name"] = supnova_table[supname].Name
							sup_obj["ra"] = supnova_table[supname].RA
							sup_obj["dec"] = supnova_table[supname].Dec
							if supnova_table[supname].curr_status['missed_deadline']:
									miss_deadline.append(sup_obj)
							if supnova_table[supname].curr_status['remaining_instances'] > 0:
									incomplete.append(sup_obj)
	
					res_json["miss_deadline"] = miss_deadline
					res_json["incomplete"] = incomplete
	
					all_SN = len(supnova_table)
					outer_SN = [sn["name"] for sn in miss_deadline]
					for sn in incomplete:
							if sn["name"] not in outer_SN:
									outer_SN.append(sn["name"])
	
					percent = (1 - (len(outer_SN) / float(all_SN))) * 100
					res_json["percentage"] = percent
	
					print json.dumps(res_json)
	
	elif int(_State) == 1:
	
			_local = _Json_obj["local_w"]
			_global = _Json_obj["global_w"]
			_feedback = _Json_obj["feedback"]
			_userID = _Json_obj["user_id"]
			
			db = MySQL()
			db.connect()
			# Return value True/False
			val = db.insert(_userID, _local, _global, _feedback)
			db.close()
	
			print "Content-type: text/plain"
			print
			if val is True:
					print '{}'
			else:
					print '{"error": "cannot insert feedback into training database..."}'
			
	else:
			error_handler("invalid request state...")
#'''
except:
    error_handler("unknown error occured...")
#'''
