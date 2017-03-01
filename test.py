class Employee:
   'Common base class for all employees'
   empCount = 0

   def __init__(self, name, salary):
      self.name = name
      self.salary = salary
      Employee.empCount += 1

   def displayCount(self):
     print "Total Employee %d" % Employee.empCount

   def displayEmployee(self):
      print "Name : ", self.name,  ", Salary: ", self.salary

print Employee('namreh', 1).displayEmployee()


class video:
    from hachoir_core.error import HachoirError
    from hachoir_core.cmd_line import unicodeFilename
    from hachoir_parser import createParser
    from hachoir_core.tools import makePrintable
    from hachoir_metadata import extractMetadata
    from hachoir_core.i18n import getTerminalCharset

    import os, time
    from dateutil import tz
    import datetime

    def __init__(self, filename):
        self.filename = filename

    # Get metadata for video file
    def metadata_for_video(self):
        print unicode(self.filename)
        filename, realname = self.unicodeFilename(unicode(self.filename)), self.filename
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




    def created_date_video(self):
        meta = self.metadata_for_video()
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
        try:
            print 1
        except:
            try:
                # return the creation date from the os            
                created_date = datetime.datetime.fromtimestamp(os.path.getmtime(filename))
                print 'Cannot extract datetime from video - returning the createdate from OS for %s' % self.filename
            except:
                # return the current date
                print 'Cannot extract datetime from video %s - returning current date' % self.filename
                created_date = datetime.datetime.now()

        return created_date



pathname = 'c:/xampp/htdocs/album/originelen/20170126_080638.mp4'
print video(pathname).created_date_video()

pathname = 'c:/xampp/htdocs/album/originelen/test.txt'
print created_date_video(pathname)

print created_date_video('')



