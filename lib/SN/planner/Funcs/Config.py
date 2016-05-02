'''
# SNeT - Computer Assisted Supernovae Tracking
# M.S. Thesis of Di Bao
# 19-JUN-2013
'''

class Config:
	def __init__(self):
		self.target_list = []
		self.night_map = []
		self.number_nights = -1
		self.number_hours = -1
		self.strategy = -1
		self.algorithm = -1
		self.train = -1
		self.local_log = None
		self.global_log = None
		
	def set_TL(self, _TL):
		self.target_list = _TL

	def set_NM(self, _NM):
                self.night_map = _NM
	
	def set_NN(self, _NN):
		self.number_nights = _NN
		
	def set_NH(self, _NH):
		self.number_hours = _NH
		
	def set_SG(self, _SG):
		self.strategy = _SG

        def set_alg(self, _ALG):
                self.algorithm = _ALG

        def set_tra(self, _TRA):
                self.train = _TRA

	def set_log(self, _localF, _globalF):
                self.local_log = _localF
                self.global_log = _globalF
