import os, time, psycopg2
from slugify import slugify
from PIL import Image, ExifTags
import exifread
import datetime
import time
import sys

import json
config = {'vm': {'originals': '/var/www/html/album/originelen',
                      'thumbs': '/var/www/html/album/thumbs'},
          'werk': {'originals': 'C:/xampp/htdocs/album/originelen',
                   'thumbs': 'C:/xampp/htdocs/album/thumbs'}}
print sys.argv[1:]
try:
    originals = config[sys.argv[1:][0]]['originals']
    thumbs = config[sys.argv[1:][0]]['thumbs']
except:
    print 'exit ', sys.argv[1:]
    print config
    sys.exit()
    


#Define our connection string
conn_string = "host='localhost' dbname='album' user='album' password='album'"

# print the connection string we will use to connect
print "Connecting to database\n	->%s" % (conn_string)

# get a connection, if a connect cannot be made an exception will be raised here
conn = psycopg2.connect(conn_string)

# conn.cursor will return a cursor object, you can use this cursor to perform queries
cursor = conn.cursor()
print "Connected!\n"



#########
# Video #
#########
from hachoir_core.error import HachoirError
from hachoir_core.cmd_line import unicodeFilename
from hachoir_parser import createParser
from hachoir_core.tools import makePrintable
from hachoir_metadata import extractMetadata
from hachoir_core.i18n import getTerminalCharset

import os, time
from dateutil import tz

import datetime

# Get metadata for video file
def metadata_for_video(filename):
    filename, realname = unicodeFilename(filename), filename
    parser = createParser(filename, realname)
    if not parser:
        print "Unable to parse file"
        exit(1)
    try:
        metadata = extractMetadata(parser)
    except HachoirError, err:
        print "Metadata extraction error: %s" % unicode(err)
        metadata = None
    if not metadata:
        print "Unable to extract metadata"
        exit(1)

    return metadata




def created_date_video(filename):
    try:
        meta = metadata_for_video(filename)
        m = str(meta)
        start_ = m.find('Creation date')
        start__ = m.find(':', start_)
        end_ = m.find('\n', start_)
        created_time = m[start__+2: end_]
        utc = datetime.datetime.strptime(created_time, '%Y-%m-%d %H:%M:%S')

        from_zone = tz.tzutc()
        to_zone = tz.tzlocal()
        utc = utc.replace(tzinfo = from_zone)
        created_date = utc.astimezone(to_zone)
    except:
        try:
            # return the creation date from the os            
            created_date = datetime.datetime.fromtimestamp(os.path.getmtime(filename))
            print 'Cannot extract datetime from video - returning the createdate from OS for %s' % filename
        except:
            # return the current date
            print 'Cannot extract datetime from video %s - returning current date' % filename
            created_date = datetime.datetime.now()

    return created_date
#############
# End Video #
#############

def generate_thumb(original, target, size):
    image=Image.open(original)
    if hasattr(image, '_getexif'): # only present in JPEGs
        for orientation in ExifTags.TAGS.keys(): 
            if ExifTags.TAGS[orientation]=='Orientation':
                break 
        e = image._getexif()       # returns None if no EXIF data
        if e is not None:
            exif=dict(e.items())
            try:
                orientation = exif[orientation] 
                if orientation == 3:   image = image.transpose(Image.ROTATE_180)
                elif orientation == 6: image = image.transpose(Image.ROTATE_270)
                elif orientation == 8: image = image.transpose(Image.ROTATE_90)

            except:
                print 'Cannot get orientation for %s' % original

    image.thumbnail(size, Image.ANTIALIAS)
    image.save(target)
    image.close()

    return target


def get_date_created(media):
    if media.lower().endswith('.jpg'):    
        ## Reading EXIF data
        # Open image file for reading (binary mode)
        ff = open(media, 'rb')
        
        # Return Exif tags
        tags = exifread.process_file(ff)

        ff.close()
        # Grabbing tags to store with the photo
        try:
            date_time = str(tags['EXIF DateTimeOriginal'])
            date_created = datetime.datetime.strptime(date_time, "%Y:%m:%d %H:%M:%S")

        except:
            date_created = ''
        ## Done reading EXIF
    else:
        date_created = ''
        
    if media.lower().endswith('.mp4'):    
        date_created = created_date_video(media)

    return date_created


for subdir, dirs, files in os.walk(originals):
    for file in files:
        # if file is a jpg, mp4 and not some thing created by the mac (eg. starting with .) then process the file
        if (file.lower().endswith('.mp4') or file.lower().endswith('.jpg') or file.lower().endswith('.mov')) and not file.startswith('.'):
            start_time = time.time()
            # Check if we need to add the directory as a new album
            current_subdir = subdir[len(originals):len(subdir)].replace("\\", "/").replace("'", "''")
            if current_subdir == '':
                current_subdir = '/'
            sql = "select id from albums where subdir = '%s';" % current_subdir
            cursor.execute(sql)
            records = cursor.fetchall()
            if len(records) == 0:
                slug = slugify(unicode(current_subdir))
                print slug
                print "going to add %s as %s" % (current_subdir, slug)
                sql = "insert into albums (name, subdir, slug) values ('%s', '%s', '%s');" % (slug, current_subdir, slug)
                cursor.execute(sql)
                conn.commit()
                sql = "select id from albums where slug = '%s'" % slug
                cursor.execute(sql)
                records = cursor.fetchall()
                current_subdir_id = records[0][0]
                print "added %s as id %s" % (current_subdir, current_subdir_id)
            else:
                current_subdir_id = records[0][0]

            # Check if we need to add the media
            f = os.path.join(subdir, file)                        
            filename = f[len(originals):len(f)].replace("\\", "/").replace("'", "''")
            sql = "select * from media where original = '%s';" % (filename)
            cursor.execute(sql)
            records = cursor.fetchall()

            
            if len(records) == 0:
                # We need to add media
                print 'Adding file %s' % file
                start = time.time()

                date_created = get_date_created(f)
                # In case we do not find a valid date from EXIF - return null in database. Deal with it later in SQL.
                if date_created == '':
                    sql = "insert into media (album_id, media, original) values (%s, '%s', '%s')" % (current_subdir_id, filename.lower()[-3:], filename)
                else:
                    sql = "insert into media (album_id, media, original, createddate) values (%s, '%s', '%s', '%s')" % (current_subdir_id, filename.lower()[-3:], filename, date_created)
                cursor.execute(sql)
                conn.commit()
                sql = "select id from media where original = '%s';" % (filename)
                cursor.execute(sql)
                records = cursor.fetchall()
                media_id = records[0][0]


                # Creating thumbnails for jpeg
                if file.lower().endswith('.jpg'):
                    small = generate_thumb(f.replace("\\", "/"), '%s/%s_small.jpg' % (thumbs, media_id), (640, 480))
                    large = generate_thumb(f.replace("\\", "/"), '%s/%s_large.jpg' % (thumbs, media_id), (1920, 1080))

                    sql = "update media set thumb_small = '%s_small.jpg' where id = %s" % (media_id, media_id)
                    cursor.execute(sql)
                    sql = "update media set thumb_large = '%s_large.jpg' where id = %s" % (media_id, media_id)
                    cursor.execute(sql)
                    conn.commit()
                
                print 'Added %s with id %s. Took %s' % (filename, media_id, time.time() - start)

# removing duplicate fronts (if any)
sql = '''
update media set isfront = false where id in
(
	select id from
	(
	select b.id, row_number() over (partition by a.id order by 1) rn from 
	albums a left join media b on a.id = b.album_id and b.isfront = true and media = 'jpg'
	) t
	where rn > 1
)
;
'''
cursor.execute(sql)
conn.commit()

# creating a front for albums not having a front
sql = '''
update media set isfront = true where id in 
( select id from
(
select b.id, row_number() over (partition by a.id order by 1) as rn from
(select a.id from 
albums a left join media b on a.id = b.album_id and b.isfront = true and media = 'jpg') a
join media b on a.id = b.album_id
) t
where rn = 1
)
;
'''
cursor.execute(sql)
conn.commit()

conn.close()
