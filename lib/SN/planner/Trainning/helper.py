L = []
f = open("sample_global_w.txt")
for line in f:
    tmp = line.split()
    L.append(int(tmp[0]))

print(L)

print("10: %.2f" % (sum(L[:10]) / 10.0))
print("20: %.2f" % (sum(L[:20]) / 20.0))
print("50: %.2f" % (sum(L[:50]) / 50.0))
print("100: %.2f" % (sum(L[:100]) / 100.0))
print("200: %.2f" % (sum(L[:200]) / 200.0))
