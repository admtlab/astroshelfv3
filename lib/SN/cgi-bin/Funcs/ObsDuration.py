#!/usr/bin/env python

# Date: 2013-10-01
# Author: Michael Wood-Vasey <wmwv@pitt.edu>
#
# Purpose:  Calculate the estimated observing time for WIYN+WHIRC observations
#
def lcTemplate(f, obsphase):
    """
    Input: 
      filter:   ['J','H','Ks','K']
      phase :   days from time of B-band maximum light
    Output:
      mag   :   magnitude with respect to B-band maximum light
   
    Note: Loading the file every time will get slow even with cached in memory. 
      Should create a stateful part that maintains this already 
       loaded for multiple invocations
    """
    import numpy

    lcfile='./Funcs/SNIa_%1.1s_template.bin.dat' % f   # Maps 'Ks' to 'K'
    slope={'J' : 0.0667, 'H' : 0.0437, 'Ks' : 0.0420}  # mag/day
    slope['K'] = slope['Ks']

    (phase, mag, magerr) = numpy.loadtxt(lcfile, unpack=True)
    # Templates start at -10 days, so we can just take the phase (in days) and add 10 to get the index
    newindex=int(obsphase)+10
    if newindex > len(phase):
       obsmag = mag[-1]+slope[f]*(newindex-len(phase))
    elif newindex < 0:
       obsmag = 25
    else:
       obsmag=mag[newindex]

    return obsmag

def timeRequired(f, obsmag):
    # Store cutoffs
    time=[   9,    18,   25,   50, 100]
    # H_limit = J_limit+0.7 mag.  K_limit = J_limit-1.4mag
    maglimits={'J' : [20.0, 20.5, 21.25, 21.4, 21.6, 22  , 22.4] ,
               'H' : [19.0, 19.8, 20.55, 20.7, 20.9, 21.3, 21.7] ,
               'Ks': [18.5, 19.1, 19.85, 20.0, 20.2, 20.6, 21.0] }
    maglimits['K'] = maglimits['Ks']
    # Stupid loop, but with 5 elements overheads dominate efficiency
    for (m, t) in zip(maglimits[f], time):
       if obsmag < m:  break
    return t
    
# Simple approximate for LCDM.  Valid for z<0.3
def distmod(z):
   import math
   c= 300000 # km/s
   H0 = 72 # km/s/Mpc
   OM = 0.28
   OL = 0.72
   dL=(c/H0) * (z + (1./2)*(OL - OM/2 + 1)*z**2) # Mpc
   mu = +5*math.log10(dL) + 25 # Because dL is in Mpc
   return mu

def obsDuration(z, phase, overhead=10, timemin=10, timemax=120):
   """ObsDuration
    Inputs: 
       Redshift
       Phase (in days with respect to B-peak (time of B maximum light)
    Optional
       overhead: to switch to a new target in minutes
       min:  Minimum observation duration, including overheads
       max:  Maximum observation duration, including overheads
    Output:
       Observation Duration in minutes (covers J+H+[Ks] + overheads)
   """
   Mabs = {'J':-18.3, 'H':-18.1, 'Ks':-18.3}  # mag
   Mabs['K'] = Mabs['Ks']
   filters = ['J','H']
   if z < 0.03:  filters.append('Ks') # Observe in K if nearby
   mu=distmod(z)
   #print "Mu: " , mu

   obstime=0.
   for f in filters:
      obsmag = Mabs[f] + lcTemplate(f,int(phase)) + mu
      #print obsmag
      #print timeRequired(f, obsmag)
      obstime += timeRequired(f, obsmag)

   totaltime=obstime+overhead
   if totaltime < timemin:  totaltime=timemin
   if totaltime > timemax:  totaltime=timemax
   return totaltime


if __name__=="__main__":
  import sys
  z     = float(sys.argv[1])
  phase = float(sys.argv[2])

  duration=obsDuration(z, phase) 
  print "A z=%5.3f SNeIa at %+6.1f days will require %4.0f minutes of observation." % (z, phase, duration)
