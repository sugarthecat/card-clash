#! C:\Users\TJ\AppData\Local\Microsoft\WindowsApps\python.exe
import mysql.connector
import os
print("Content-type: text\n")
args = os.environ["QUERY_STRING"].split("&")
for i in range(len(args)):
    args[i] = args[i].split("=")
if len(args) < 2 or len(args[0]) < 2 or len(args[1]) < 2:
    print("{\"error\": not enough arguments}")
    exit()
username = args[0][1]
password = args[1][1]
if not (username.isalnum() and password.isalnum()):
    print("{\"error\": Username and password are not alphanumeric}")
    exit()

try:
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="cardclash"
    )
    cursor = db.cursor()
    cursor.execute(
        "SELECT EXISTS(SELECT * FROM users WHERE username = \""+username+"\");")
    result = cursor.fetchall()
    print(result[0][0])
except Exception as e:
    print(e)
