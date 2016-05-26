import lxml.html as html
import csv
import os

files = [f for f in os.listdir('forms/') if f.endswith('.html')]

verbs = ['biec', 'chcieć', 'chorować', 'czytać', 'dawać', 'grać', 'kochać', 'leżeć', 'malować', 'nienawidzić', 'orać', 'otwierać', 'pisać', 'pracować', 'przywozić', 'pukać', 'rąbać', 'siedzieć', 'spać', 'spacerować', 'śpiewać', 'strzelać', 'szukać', 'widzieć', 'wisieć', 'zabijać', 'żądać', 'zamykać', 'zamawiać', 'znajdować', 'umierać', 'kłaść', 'trzymać', 'topnieć', 'gnić', 'tracić', 'palić', 'rugać', 'płakać', 'kipieć', 'chrapać']
allVerbs = {}
#allAtr = {'count': True}
allAttr = ['inf', 'praet', 'perf', 'imperf', 'sg', 'pl', 'f', 'n', 'm1', 'm2', 'm3', 'count']

#verbs.sort()

#for verb in verbs:
#    page = html.parse(verb+'.html')
#    tds = page.xpath("/html/body/table/tr/td[2]");
#    for td in tds:
#        print unicode(td.text_content());

for i in range(0,len(verbs)):
    verbs[i] = unicode(verbs[i], 'utf-8')
    
    
for verb in verbs:
    allVerbs[verb] = {}
    for attr in allAttr:
        allVerbs[verb][attr] = 0

#в forms должен быть список файлов из папки forms

for form in files:
    formFile = "forms/"+form
    if os.stat(formFile).st_size == 0:
        continue
    page = html.parse(formFile)#html дописывать не нужно, должно быть итак в списке файлов
    print "form - "+form
    tds = page.xpath("/html/body/table/tr/td[2]");
    for td in tds:
        #print unicode(td.text_content())
        strings = td.text_content().strip().split()
        attributes_string = strings[3].strip('[]')
        attributes = attributes_string.split(':')

        verb = attributes[0]
        
        if verb not in verbs:
            #print "not found - "+verb;
            continue
        #print "inf - "+verb;
#мы тут начинали с первого элемента, а не с нулевого, потому что в первом атрибуте лежит инфинитив, который в данном случае должен будет быть verb
        for i in range(1,len(attributes)):
            allVerbs[verb][attributes[i]]+=1
        
        allVerbs[verb]['count']+=1
            
#print allVerbs

with open ('verbs_infin_by_forms.csv','wb') as csvfile:
    fieldnames = ['verb'] + allAttr
    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
    
    writer.writeheader()
    for verb in verbs:
        #print allVerbs[verb]
        row = allVerbs[verb]
        row['verb'] = verb.encode('utf-8');
        writer.writerow(row)

