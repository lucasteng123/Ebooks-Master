#!/usr/bin/python
import os

def count_lines(fname):
	with open(fname) as f:
		i=0
		for i, l in enumerate(f):
			if ("<pre>" in l):
				print("\n\n"+str(i)+': '+l+"\n\n")
			pass
	return i+1

def main():
	fileCounts = []
	fileCounts.append(['./lib/SiteDB.class','.php'])
	fileCounts.append(['./controllers','.controller.php'])
	fileCounts.append(['./scripts','.php'])
	fileCounts.append(['./templates','.template.php'])
	fileCounts.append(['./res/script','.js'])
	fileCounts.append(['./res/style','.less'])

	count = 0
	filesCount = 0

	for folder in fileCounts:
		directory = folder[0]
		extension = folder[1]
		iFilesCount = filesCount
		iCount = count
		for fname in os.listdir(directory):
			if fname.endswith(extension):
				thisCount = count_lines(directory+"/"+fname)
				count += thisCount
				filesCount += 1
				print(directory+"/"+fname+": " + str(thisCount) + " lines")
		filesInFolder = filesCount - iFilesCount
		countInFolder = count - iCount
		print(str(filesInFolder) + " files for "+extension+" in "+directory)
		print(str(countInFolder) + " lines for "+extension+" in "+directory)
		print("Average line-count: "+str(countInFolder / float(filesInFolder)))
		print("")

	print("")
	print("Total count is: "+str(count) + " lines")
	print("Files counted: "+str(filesCount))
	print("Overall average line-count: "+str(count / float(filesCount)))

if __name__ == "__main__":
	main();