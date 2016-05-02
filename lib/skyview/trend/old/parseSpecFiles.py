import cgi
import cgitb
cgitb.enable()  # for troubleshooting
import math
import numpy
import pyfits
import os
import sys
import string

# Get a list of all the filenames
path = sys.path[0] + r"/proc/fits"
file_list = os.listdir(path)
for filename in file_list:
  #filename = sys.argv[1] #'http://das.sdss.org/spectro/1d_26/2224/1d/spSpec-53815-2224-598.fit';
  num = filename.count(".fit")
  if (num >= 1):
    # 500 Standard wavelengths at step = 5 starting at 4500
    waves_std = range(4500, 4500 + 500*5,5)

    new_path = sys.path[0] + r"/proc/parsed"
    # Iterate over the files and get the fluxes at the wavelengths
    hdulist = pyfits.open(path + r"/" + filename)
    filename = os.path.basename(filename)
    fluxes = []
    waves = []
    primaryHeader = hdulist[0].header
    redshift = primaryHeader['z']
    coeff0 = primaryHeader['coeff0']
    coeff1 = primaryHeader['coeff1']
    fluxes = hdulist[0].data[0]

    # Create an array of the wavelength values
    for i in range(len(fluxes)):
      waves.append(math.pow(10, coeff0 + coeff1*i))

    normfluxes = []  
    total = sum(fluxes)
    # Normalize the fluxes
    for flux_value in fluxes:
      normfluxes.append(flux_value/total)

    # Get the interpolation function
    #funct = scipy.interpolate.interp1d(waves, normfluxes, "linear", 0, True, False, 0)
    # instead:
    # Get the interpolated fluxes
    interpfluxes = []
    interpfluxes = numpy.interp(waves_std, waves, normfluxes)

    new_file = open(new_path + r"/" + filename, "w")
    new_file.write(str(redshift) + "\n")
    # Get the values of the fluxes at the standard wavelengths
    #normfluxes = []
    min_flux = interpfluxes[0]
    max_flux = interpfluxes[0]
    for flux_value in interpfluxes:
      # Update min and max
      if (flux_value > max_flux):
        max_flux = flux_value
      if (flux_value < min_flux):
        min_flux = flux_value

    for flux_value in interpfluxes:
      flux_value = ((flux_value - min_flux) * 255) / (max_flux - min_flux)
      flux_value = int(round(flux_value))
      new_file.write(str(flux_value) + "\n")

    hdulist.close()
    new_file.close()