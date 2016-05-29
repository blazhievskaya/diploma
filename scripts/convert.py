import lxml.html as html
import csv
import os

#Get all html files in forms dir
files = [f for f in os.listdir('forms/') if f.endswith('.html')]
#List of verbs and attributes
verbs = ['biec', 'chcieć', 'chorować', 'czytać', 'dawać', 'grać', 'kochać', 'leżeć', 'malować', 'nienawidzić', 'orać', 'otwierać', 'pisać', 'pracować', 'przywozić', 'pukać', 'rąbać', 'siedzieć', 'spać', 'spacerować', 'śpiewać', 'strzelać', 'szukać', 'widzieć', 'wisieć', 'zabijać', 'żądać', 'zamykać', 'zamawiać', 'znajdować', 'umierać', 'kłaść', 'trzymać', 'topnieć', 'gnić', 'tracić', 'palić', 'rugać', 'płakać', 'kipieć', 'chrapać']
allVerbs = {}
allAttr = ['inf', 'praet', 'perf', 'imperf', 'sg', 'pl', 'f', 'n', 'm1', 'm2', 'm3', 'count']

#convert verbs to utf-8
for i in range(0,len(verbs)):
    verbs[i] = unicode(verbs[i], 'utf-8')
    
#Verb attributes initialize 
for verb in verbs:
    allVerbs[verb] = {}
    for attr in allAttr:
        allVerbs[verb][attr] = 0

#Process by files
for form in files:
    formFile = "forms/"+form
    if os.stat(formFile).st_size == 0:
        continue
    page = html.parse(formFile)
    print "form - "+form
	#get second columns 
    tds = page.xpath("/html/body/table/tr/td[2]");
    for td in tds:        
		#split by space
        strings = td.text_content().strip().split()
		#pick out attributes string
        attributes_string = strings[3].strip('[]')
        attributes = attributes_string.split(':')
		#pick out infinitive 
        verb = attributes[0]
		
        #Check verbs to avoid redundant data   
        if verb not in verbs:
            continue
        #Remember attributes 
		for i in range(1,len(attributes)):
            allVerbs[verb][attributes[i]]+=1
			
        #Count records 
        allVerbs[verb]['count']+=1
            
#Write data to csv 

with open ('verbs_infin_by_forms.csv','wb') as csvfile:
    fieldnames = ['verb'] + allAttr
    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
    
    writer.writeheader()
    for verb in verbs:
        #print allVerbs[verb]
        row = allVerbs[verb]
        row['verb'] = verb.encode('utf-8');
        writer.writerow(row)

