'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

'''
# The local scheduler implementing 1) best-first(greedy) beam search,
# 2) iterative dynamic programming, 3) random restart hill-climbing (local search)
# with dynamical weighting:
# w1 - w of value (priority)
# w2 - w of weight (duration)
# w3 - w of side-fact1 (window length)
# w4 - w of side-fact2 (total windows overlap length)
#
# ---------------------------
#
# Proposed heuristic/objective function:
# H(SN) = (w1 + 100 * value) / (w2 + weight) + (w3 + side_fact1) / (1 + w4 + side-fact2)
#
# _targeting_list = [SN1, SN2, ..., SNi, ..., SNn]
# _night_index, from 1st, 2nd, 3th, to nth night
# _night_capacity, how many minutes available for the night
'''

import heapq
import random

class L0Scheduler:
        def __init__(self, _candidate_list, _night_map, _night_index, _night_capacity):
                self.clist = _candidate_list
                self.nmap = _night_map
                self.ninde = _night_index
                self.ncapa = _night_capacity

                self.w1 = 0
                self.w2 = 0
                self.w3 = 0
                self.w4 = 0

                # alg 1, the beam length will be 10% the number of candidate supernovae
                self.cutoff_ratio1 = 0.2
                # alg 2, the number of iteration will be 50% the number of candidate supernovae
                self.cutoff_ratio2 = 0.5
                # alg 3, the time of random restarts will be 75% the number of candidate supernovae
                self.cutoff_ratio3 = 0.75

                self.filtered_set = []

        def set_weight(self, _w1, _w2, _w3, _w4):
                self.w1 = _w1
                self.w2 = _w2
                self.w3 = _w3
                self.w4 = _w4

        def set_cutoff_ratio1(self, _c1):
                self.cutoff_ratio1 = _c1

        def set_cutoff_ratio2(self, _c2):
                self.cutoff_ratio2 = _c2

        def set_cutoff_ratio3(self, _c3):
                self.cutoff_ratio3 = _c3

        def heuristic(self, _supnova, _tmp_set):
                value = _supnova.get_Priority()
                weight = _supnova.get_obsDuration(self.ninde, self.nmap)
                window = _supnova.get_obsWindow(self.ninde, self.nmap)
                side_fact1 = window[1] - window[0] # window length

                side_fact2 = 0 # total windows overlap length
                for supnova in _tmp_set:
                        com_window = supnova.get_obsWindow(self.ninde, self.nmap)
                        # the head of interval1 in between of interval2
                        if window[0] > com_window[0] and window[0] < com_window[1]:
                                side_fact2 += min(window[1], com_window[1]) - window[0]
                        elif window[1] > com_window[0] and window[1] < com_window[1]:
                                side_fact2 += window[1] - max(window[0], com_window[0])
                        else:
                                pass

                H = (self.w1 + (100 * value)) / (self.w2 + weight) + (self.w3 + side_fact1) / (1 + self.w4 + side_fact2)
                #output = "%s: %f" % (_supnova.Name, H)
                #print(output)
                return H

        def schedule(self, alg = 0):

                # special case, no candidate supernova globally scheduled this night
                if len(self.clist) == 0:
                        return []

                if alg == 0:
                        return self.brute_forcely()
                elif alg == 1:
                        return self.greedy_beam_search()
                elif alg == 2:
                        return self.iterative_DP()
                elif alg == 3:
                        return self.restart_hill_climbing()
                else:
                        pass

        def brute_forcely(self):
                
                count = len(self.clist)
                upper_bound = 2 ** count
                masking = 1

                global_set = []
                global_value = 0
                while masking < upper_bound:
                        fiter_set = []
                        for i in range(count):
                                if masking & (1 << i) > 0:
                                        fiter_set.append(self.clist[i])
                        masking += 1
                        #print(fiter_set)
                        if not self.validate_filtered_set(fiter_set):
                                continue
                        
                        local_capacity = 0
                        for supnova in fiter_set:
                                local_capacity += supnova.get_obsDuration(self.ninde, self.nmap)
                        if local_capacity > self.ncapa:
                                continue
                        
                        local_value = 0
                        for supnova in fiter_set:
                                local_value += supnova.get_Priority()
                        if local_value > global_value:
                                global_value = local_value
                                global_set = fiter_set
                        
                return global_set


        def greedy_beam_search(self):
                
                def order_by_H(_supnova, _tmp_set, _max_heap):
                                H = self.heuristic(_supnova, _tmp_set)
                                heapq.heappush(_max_heap, (-H, [_supnova, _tmp_set]))
                
                beam_width = int(len(self.clist) * self.cutoff_ratio1)
                if beam_width == 0:
                        beam_width = 1
                
                candi_res = []
                filte_res = []
                for i in range(beam_width):
                                candi_res.append([])
                
                tmp_heap = []
                for supnova in self.clist:
                                order_by_H(supnova, [], tmp_heap)
                for i in range(beam_width):
                        #if len(tmp_heap) == 0:
                        #       break
                        sup = heapq.heappop(tmp_heap)[1][0]
                        candi_res[i].append(sup)

                while len(filte_res) < beam_width:
                                tmp_heap = []
                                for state in candi_res:
                                                flag = 0
                                                successors = [sup for sup in self.clist if sup not in state]
                                                for sup in successors:
                                                                next_state = state[:]
                                                                next_state.append(sup)
                                                                capa = 0
                                                                for sup in next_state:
                                                                                capa += sup.get_obsDuration(self.ninde, self.nmap)
                                                                if capa > self.ncapa:
                                                                                continue
                                                                if not self.validate_filtered_set(next_state):
                                                                                continue                                                 
                                                                order_by_H(sup, state, tmp_heap)
                                                                flag = 1
                                                if not flag:
                                                                filte_res.append(state)
                                
                                candi_res = []
                                for i in range(beam_width - len(filte_res)):
                                                item = heapq.heappop(tmp_heap)[1]
                                                sup = item[0]
                                                state = item[1]
                                                next_state = state[:]
                                                next_state.append(sup)
                                                candi_res.append(next_state)
                
                max_value = 0
                max_index = 0
                for i in range(len(filte_res)):
                                state = filte_res[i]
                                tmp_val = 0
                                for supnova in state:
                                                tmp_val += supnova.get_Priority()
                                if tmp_val > max_value:
                                                max_value = tmp_val
                                                max_index = i
                
                self.filtered_set = filte_res[max_index]
                return self.filtered_set
        
        def iterative_DP(self):
                '''
                Build two-dimensional array V[index, weight],
                where V[0, w] = 0, V[i, w] is illegal with w < 0,
                V[i, w] means the maximum value gained from subset 1 - i,
                and the total weight less than w.
                V[i, w] = max(V[i - 1, w], v_i + V[i - 1, w - w_i])
                '''
        
                iteration_times = int(len(self.clist) * self.cutoff_ratio2)
                if iteration_times == 0:
                        iteration_times = 1
                candi_set = self.clist[:]
                filte_res = None
                
                while iteration_times:      
                                filte_res = []
                                
                                V = []
                                K = []
                                for i in range(len(candi_set) + 1):
                                                V.append([0] * (self.ncapa + 1))
                                                K.append([0] * (self.ncapa + 1))
                                
                                for i in range(len(candi_set)):
                                                tmp_value = candi_set[i].get_Priority()
                                                tmp_weight = candi_set[i].get_obsDuration(self.ninde, self.nmap)
                                                i = i + 1 
                                                for w in range(self.ncapa + 1):
                                                                if (tmp_weight <= w) and ((tmp_value + V[i-1][w-tmp_weight]) > V[i-1][w]):
                                                                                V[i][w] = tmp_value + V[i-1][w-tmp_weight]
                                                                                K[i][w] = 1
                                                                else:
                                                                                V[i][w] = V[i-1][w]
                                
                                C = self.ncapa
                                index = len(candi_set)
                                while index:
                                                if K[index][C] == 1:
                                                                filte_res.append(candi_set[index-1])
                                                                C -= candi_set[index-1].get_obsDuration(self.ninde, self.nmap)
                                                index -= 1
                                                
                                if self.validate_filtered_set(filte_res):
                                                self.filtered_set = filte_res
                                                return filte_res
                                else:
                                                min_heap = []
                                                for supnova in filte_res:
                                                                H = self.heuristic(supnova, filte_res)
                                                                heapq.heappush(min_heap, (H, supnova))
                                                supnova = heapq.heappop(min_heap)[1]
                                                for sup_index in range(len(candi_set)):
                                                                if candi_set[sup_index] is supnova:
                                                                                candi_set.pop(sup_index)
                                                                                break
                                                iteration_times -= 1
                
                while not self.validate_filtered_set(filte_res):
                                min_heap = []
                                for supnova in filte_res:
                                                H = self.heuristic(supnova, filte_res)
                                                heapq.heappush(min_heap, (H, supnova))
                                supnova = heapq.heappop(min_heap)[1]
                                for sup_index in range(len(filte_res)):
                                                if filte_res[sup_index] is supnova:
                                                                filte_res.pop(sup_index)
                                                                break
                
                self.filtered_set = filte_res
                return self.filtered_set

        def restart_hill_climbing(self):
                
                restart_nums = int(len(self.clist) * self.cutoff_ratio3)
                if restart_nums == 0:
                        restart_nums = 1
                filte_res = None
                global_max = 0
                
                while restart_nums:
                                candi_set = self.clist[:]
                                local_set = []
                                local_capacity = 0

                                while True:
                                        if len(candi_set) == 0:
                                                break
                                        
                                        rand_index = random.randrange(len(candi_set))
                                        if not self.validate_filtered_set(local_set + [candi_set[rand_index]]):
                                                break
                                        if local_capacity + candi_set[rand_index].get_obsDuration(self.ninde, self.nmap) >= self.ncapa:
                                                break

                                        local_set.append(candi_set[rand_index])
                                        local_capacity += candi_set[rand_index].get_obsDuration(self.ninde, self.nmap)
                                        candi_set.pop(rand_index)

                                successors = candi_set

                                # special case, no need to continue random local walk
                                if len(successors) == 0:
                                        return local_set
                                
                                # phase one
                                max_heap = []
                                for sup_index in range(len(successors)):
                                                H = self.heuristic(successors[sup_index], local_set)
                                                heapq.heappush(max_heap, (-H, sup_index))
                                while len(max_heap):
                                                sup_index = heapq.heappop(max_heap)[1]
                                                supnova = successors[sup_index]
                                                local_set.append(supnova)
                                                if not self.validate_filtered_set(local_set):
                                                                local_set.pop(-1)
                                                else:
                                                                successors[sup_index] = None

                                successors = [sup for sup in successors if sup is not None]
                                
                                # phase two
                                while True:
                                                flag = 0
                                                for sup1_index in range(len(local_set)):
                                                                for sup2_index in range(len(successors)):
                                                                                if local_set[sup1_index].get_Priority() < successors[sup2_index].get_Priority():
                                                                                                tmp_set = local_set[:]
                                                                                                tmp_set.pop(sup1_index)
                                                                                                tmp_set.append(successors[sup2_index])
                                                                                                if self.validate_filtered_set(tmp_set):
                                                                                                                successors.pop(sup2_index)
                                                                                                                successors.append(local_set[sup1_index])
                                                                                                                local_set = tmp_set
                                                                                                                flag = 1
                                                                                                                break
                                                                if flag:
                                                                                break
                                                if flag:
                                                                continue
                                                else:
                                                                break
                                
                                local_max = 0
                                for supnova in local_set:
                                                local_max += supnova.get_Priority()
                                if local_max > global_max:
                                                global_max = local_max
                                                filte_res = local_set
                                
                                restart_nums -= 1
                                
                self.filtered_set = filte_res
                return self.filtered_set        
                    
        '''
        In certain time interval, there are a set of tasks,
        each has (release time, WCET, deadline), the execution
        is non-preemptive, is the set of tasks schedulable?

        Such a problem seems to be NP-complete
        '''
        # incomplete greedy approach for validation
        def validate_filtered_set(self, _tmp_set):
                L1 = [] # tuples of task window
                L2 = [] # time of task execution
                for supnova in _tmp_set:
                        L1.append(supnova.get_obsWindow(self.ninde, self.nmap))
                        L2.append(supnova.get_obsDuration(self.ninde, self.nmap))

                last_finished = 0
                while len(L1) and len(L2):
                        min_index = None
                        min_value = None
                        for i in range(len(L1)):
                                if min_value is None or L1[i][0] < min_value:
                                        min_index = i
                                        min_value = L1[i][0]
                        start_time = max(last_finished, min_value)
                        deadline = L1[min_index][1]
                        WCET = L2[min_index]
                        if start_time + WCET > deadline:
                                return False
                        else:
                                last_finished = start_time + WCET
                                L1.pop(min_index)
                                L2.pop(min_index)
                return True

if __name__ == "__main__":

# experiment
        from Funcs.SN import SN
        import random
        import time

        chart = {}
        chart["time"] = {}
        chart["optimity"] = {}
        chart["time"][0.5] = []
        chart["time"][0.6] = []
        chart["time"][0.75] = []
        chart["time"][0.8] = []
        chart["time"][0.9] = []
        chart["optimity"][0.5] = []
        chart["optimity"][0.6] = []
        chart["optimity"][0.75] = []
        chart["optimity"][0.8] = []
        chart["optimity"][0.9] = []
        
        for i in range(100):

                candi_list = []
                candi_capa = 0
                
                while candi_capa <= 3 * 360:
                        dict_O = {}
                        dict_C = {}
                        dict_O['_Name'] = "default"
                        dict_O['_RA'] = random.randrange(100, 250)
                        dict_O['_Dec'] = random.randrange(-50, 50)
                        dict_O['_Type'] = "default"
                        dict_O['_Redshift'] = 0.01
                        dict_O['_Mag'] = 20.0
                        dict_O['_B-Peak'] = "1989-01-09"
                        dict_O['_Priority'] = round(random.uniform(0, 1), 3)
                        dict_C['_obsDuration'] = random.randrange(20, 80)
                        dict_C['_obsGap'] = 1
                        dict_C['_obsTimes'] = 3
                        supnova = SN(dict_O, dict_C)

                        candi_list.append(supnova)
                        candi_capa += supnova.get_obsDuration(self.ninde, self.nmap)
       
                for cutoff_ratio in [0.5, 0.6, 0.75, 0.8, 0.9]:
                        result_list = []
                        
                        S0 = L0Scheduler(candi_list, [], 0, 360)
                        S0.set_cutoff_ratio3(cutoff_ratio)
                        start_time = time.time()
                        result_list = S0.schedule(3)
                        elapsed_time = round(time.time() - start_time, 2)
                        chart["time"][cutoff_ratio].append(elapsed_time)
                        optimity_gain = 0
                        for sup in result_list:
                                optimity_gain += sup.get_Priority()
                        chart["optimity"][cutoff_ratio].append(optimity_gain)

        #print(chart)
        print("time varies:")
        for keys in sorted(chart["time"].keys()):
                print(str(keys) + ": " + str(sum(chart["time"][keys]) / len(chart["time"][keys])))

        print("\n")

        print("optimity varies:")
        for keys in sorted(chart["optimity"].keys()):
                print(str(keys) + ": " + str(sum(chart["optimity"][keys]) / len(chart["optimity"][keys])))
        
'''
        chart = {}
        chart["alg0"] = {}
        chart["alg1"] = {}
        chart["alg2"] = {}
        chart["alg3"] = {}

        for k in [1, 1.25, 1.5, 1.75, 2, 2.25, 2.5]:

                list_time0 = []
                list_time1 = []
                list_time2 = []
                list_time3 = []
                list_optimity0 = []
                list_optimity1 = []
                list_optimity2 = []
                list_optimity3 = []

                for i in range(10):
                        
                        candi_list = []
                        candi_capa = 0
                        result_list = []
                        
                        while candi_capa <= k * 360:
                                dict_O = {}
                                dict_C = {}
                                dict_O['_Name'] = "default"
                                dict_O['_RA'] = random.randrange(100, 250)
                                dict_O['_Dec'] = random.randrange(-50, 50)
                                dict_O['_Type'] = "default"
                                dict_O['_Redshift'] = 0.01
                                dict_O['_Mag'] = 20.0
                                dict_O['_B-Peak'] = "1989-01-09"
                                dict_O['_Priority'] = round(random.uniform(0, 1), 3)
                                dict_C['_obsDuration'] = random.randrange(20, 80)
                                dict_C['_obsGap'] = 1
                                dict_C['_obsTimes'] = 3
                                supnova = SN(dict_O, dict_C)

                                candi_list.append(supnova)
                                candi_capa += supnova.get_obsDuration()

                        S0 = L0Scheduler(candi_list, [], 0, 360)
                        for j in range(4):
                                exec("list_time = list_time" + str(j))
                                exec("list_optimity = list_optimity" + str(j))
                                start_time = time.time()
                                result_list = S0.schedule(j)
                                elapsed_time = round(time.time() - start_time, 2)
                                list_time.append(elapsed_time)
                                optimity_gain = 0
                                for sup in result_list:
                                        optimity_gain += sup.get_Priority()
                                list_optimity.append(optimity_gain)

                chart["alg0"][k] = {}
                chart["alg0"][k]["time"] = sum(list_time0) / len(list_time0)
                chart["alg0"][k]["optimity"] = sum(list_optimity0) / len(list_optimity0)

                chart["alg1"][k] = {}
                chart["alg1"][k]["time"] = sum(list_time1) / len(list_time1)
                chart["alg1"][k]["optimity"] = sum(list_optimity1) / len(list_optimity1)

                chart["alg2"][k] = {}
                chart["alg2"][k]["time"] = sum(list_time2) / len(list_time2)
                chart["alg2"][k]["optimity"] = sum(list_optimity2) / len(list_optimity2)

                chart["alg3"][k] = {}
                chart["alg3"][k]["time"] = sum(list_time3) / len(list_time3)
                chart["alg3"][k]["optimity"] = sum(list_optimity3) / len(list_optimity3)

        _str = ""
        for k in [1, 1.25, 1.5, 1.75, 2, 2.25, 2.5]:
                _str += str(k) + "\t"
        print(_str)
        for alg in sorted(chart.keys()):
                _str = ""
                for _k in sorted(chart[alg].keys()):
                        _str += str(chart[alg][_k]["time"]) + "\t"
                print(_str + "\t" + "(" + alg + ")")

        print("\n")

        _str = ""
        for k in [1, 1.25, 1.5, 1.75, 2, 2.25, 2.5]:
                _str += str(k) + "\t"
        print(_str)
        for alg in sorted(chart.keys()):
                _str = ""
                for _k in sorted(chart[alg].keys()):
                        _str += str(chart[alg][_k]["optimity"]) + "\t"
                print(_str + "\t" + "(" + alg + ")")

'''

# test code
'''
    import json
    from SN import SN
    
    json_f = open("sample_data1.json")
    raw_data = json.load(json_f)
    json_f.close()
    
    #print(raw_data)
    candi_list = []
    filte_list = []
                
    for item in raw_data:
            dict_O = item[0]
            dict_C = item[1]
            supnova = SN(dict_O, dict_C)
            #print(supnova)
            candi_list.append(supnova)

    S0 = L0Scheduler(candi_list, 0, 360)
    filte_list = S0.schedule(0)
    #filte_list = S0.schedule(1)
    #filte_list = S0.schedule(2)
    #filte_list = S0.schedule(3)

    for sup in filte_list:
        print(sup)
'''
