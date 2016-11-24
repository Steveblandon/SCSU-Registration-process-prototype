from DataScraper import DataScraper
from MySqlAccess import MySqlAccess

url = "file:///C:/Users/steve/Workshop/CSC%20334%20project/htmlCopy.txt"
scraper = DataScraper()
scraper.start("datadisplaytable", url)
length = len(scraper.data)
cData = []
for i in range(length):
    if len(scraper.data[i]) == 0:
        continue
    record = scraper.data[i][2:5]
    if len(record) == 0:
        continue
    if record[0] == "\xa0":
        continue
    record += scraper.data[i][6:10]
    record += scraper.data[i][16:17]
    record += scraper.data[i][-4:-2]
    if record[-3].strip() != "Instructor":
        record[-3] = record[-3][0:-2]
    cData.append(record)

scraper.data = cData
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
