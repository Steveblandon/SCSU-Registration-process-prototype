from DataScraper import DataScraper
from MySqlAccess import MySqlAccess
from datetime import datetime

url = "file:///C:/wamp64/www/CSC334/Mr. Spider/htmlCopy.txt"
scraper = DataScraper()
scraper.start("datadisplaytable", url)
length = len(scraper.data)
cData = []
for i in range(length):
    if len(scraper.data[i]) == 0:
        continue
    #grab subject, courseNo, and sectionNo
    record = scraper.data[i][2:5]
    #skip empty records and labs
    if len(record) == 0:
        continue
    if record[0] == "\xa0":
        continue
    #grab credits, title, days, time
    record += scraper.data[i][6:10]
    if record[-1] == "TBA":
        continue
    elif record[-1] == "Time":
        record[-1] = "StartTime"
        record.append("EndTime")
    else:
        #times are in hh:mm p - hh:mm p format, change to military format to more easily use in sql
        hyphenInd = record[-1].find('-')
        endTime = record[-1][hyphenInd+1:-1] + 'm'
        endTime = datetime.strftime(datetime.strptime(endTime,'%I:%M %p'),'%H:%M')
        record.append(endTime)
        startTime = record[-2][0:hyphenInd]
        startTime = datetime.strftime(datetime.strptime(startTime,'%I:%M %p'),'%H:%M')
        record[-2] = startTime
    #get instructor, date, and location
    record += scraper.data[i][16:17]
    record += scraper.data[i][-4:-2]
    if record[-3].strip() != "Instructor":
        # instructor field is in the format 'professor name (', reformatting it to remove the paranthesis
        record[-3] = record[-3][0:-2]
    cData.append(record)


#the following code assumes that the sql table 'courses' has the same structure of this data (column count and type)
sql = MySqlAccess("localhost","root","","courses_db")
print("The following queries could not be entered:")
print("---------------------------------------------")
for record in cData:
    if record[0] == "Subj":
        continue
    query = "INSERT INTO courses VALUES(";
    for item in record:
        query += "'" + item + "',"
    query = query[0:-1] + ")"
    try:
        sql.query(query)
    except:
        print(query)
sql.conn.close()
