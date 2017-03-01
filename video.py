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




