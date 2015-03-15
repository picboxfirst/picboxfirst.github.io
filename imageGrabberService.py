import os
import sys
import time
from urllib import FancyURLopener
import urllib2
import json as simplejson
import mysql.connector
import re, requests, urllib

TAG_RE = re.compile(r'<[^>]+>')

def remove_tags(text):
    return TAG_RE.sub('', text)

def getsize(uri):
    file = urllib.urlopen(uri)
    size = file.headers.get("content-length")
    file.close()
    return size

cnx = mysql.connector.connect(user='root', password='123p1cb0x', database='123picbox')

keysearcRecords = cnx.cursor(buffered=True)
keysearcRecords.execute("SELECT * FROM keysearch where is_grabbed=0 limit 2")
updateKeysearch = cnx.cursor(buffered=True)

for row in keysearcRecords:

    # Define search term
    keysearch = row[1]
    searchTerm = keysearch + " HD"
    category_id = row[2]
    # Replace spaces ' ' in search term for '%20' in order to comply with request
    searchTerm = searchTerm.replace(' ','%20')

    # Set count to 0
    count= 0
    try :
        for i in range(0, 100):
            # Notice that the start changes for each iteration in order to request a new set of images for each loop
            url = ('https://ajax.googleapis.com/ajax/services/search/images?' + 'v=1.0&q='+searchTerm+'&start='+str(i*4)+'&userip=MyIP')
            print url
            request = urllib2.Request(url, None, {'Referer': 'testing'})
            response = urllib2.urlopen(request)

            # Get results using JSON
            results = simplejson.load(response)
            data = results['responseData']
            dataInfo = data['results']  

            # Iterate for each result and get unescaped url
            for myUrl in dataInfo:
                print(".")
                try:
                    resp = requests.head(myUrl['unescapedUrl'])
                    resp.status_code
                    
                    if int(resp.status_code) == 200 and getsize(myUrl['unescapedUrl']) :
                        if int(myUrl['width']) >= 1000 or int(myUrl['height']) >= 1000 :
                            count = count + 1
                            url = myUrl['unescapedUrl'].replace('www.', '')
                            title = remove_tags(myUrl['content'])
                            content = remove_tags(myUrl['title'])
                            width = myUrl['width'] 
                            height = myUrl['height']
                            cursor = cnx.cursor()
                            check_one = "SELECT COUNT(1) FROM images WHERE Original =" + "'" + url +"'"
                            cursor.execute(check_one)
                            if cursor.fetchone()[0]:
                                print "exist"
                                print myUrl['width']
                            else:
                                add_images = ("INSERT INTO images "
                                  "(image_id, keysearch, title, content, category_id, Original, Original_Width, Original_Height, batch) "
                                  "VALUES (%(image_id)s, %(keysearch)s, %(title)s, %(content)s, %(category_id)s, %(Original)s, %(Original_Width)s, %(Original_Height)s, %(batch)s)")

                                data_images = {
                                    'image_id' : int(time.time()),
                                    'keysearch' : keysearch,
                                    'title': title,
                                    'content': content,
                                    'category_id': category_id,
                                    'Original' : url,
                                    'Original_Width' : width,
                                    'Original_Height' : height,
                                    'batch' : 3
                                }

                                cursorInsert = cnx.cursor()
                                cursorInsert.execute(add_images, data_images)
                                cnx.commit()
                                cursorInsert.close()
                                print "success insert"
                            cursor.close()
                    else:
                        print resp.status_code
                except ValueError:
                    print "error cuy"
            # Sleep for one second to prevent IP blocking from Google
            time.sleep(1)
    except Exception:
        pass
    updateKeysearch.execute("UPDATE keysearch SET is_grabbed='%s' WHERE id = '%s'"% (1, row[0]))
    cnx.commit()
updateKeysearch.close()
keysearcRecords.close()