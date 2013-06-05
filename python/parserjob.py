import os, sys
import json
import re
import MySQLdb
import time

import dumpscan

class IllegalArgumentException(Exception):
    pass

class Job(object):
    jobs_directory = "/data/project/dumpscan/public_html/r/"
    dumps_directory = "/public/datasets/public/"

    def __init__(self, job_number):
        self.job_number = int(job_number)
        self.job_dir = os.path.join(self.jobs_directory, str(self.job_number))
		
        self.jd = json.load(open(os.path.join(self.job_dir, "request")))
        self.load_job_description(json.load(open(os.path.join(self.job_dir, "request"))))
        
        self.ss_total_pages = None
        self.pages_processed = None
        self.starttime = None
    
    def update_status(self, status="running"):
        status = {'status': status,
                  'ss_total_pages': self.ss_total_pages,
                  'pages_processed': self.pages_processed,
                  'starttime': self.starttime}
        
        f = open(os.path.join(self.job_dir, "status"), "w")
        json.dump(status, f)
        f.flush()
        f.close()
        
    def load_job_description(self, desc):
        if desc["dump"] in os.listdir(self.dumps_directory):
            self.wiki = desc["dump"]
            path = os.path.join(self.dumps_directory, desc["dump"])
            dates = [int(x) for x in os.listdir(path)]
            date = str(max(dates))
            self.dumpfile = os.path.join(path, date, '{1}-{0}-pages-articles.xml.bz2'.format(date, desc["dump"]))
        else:
            raise IllegalArgumentException("Illegal dump specified")

        self.filters = []
        
        if desc.get("nsinclude", False):
            self.filters.append(lambda x: x.ns in [int(i) for i in desc["nsinclude"]])
        
        if desc.get("titlecontains", False):
            self.filters.append(lambda x: desc["titlecontains"] in x.title)
            
    def estimate_total_pages(self):
        try:
            dbname = self.wiki + "_p"
            host = self.wiki + ".labsdb"
            with MySQLdb.connect(db=dbname, host=host, read_default_file="~/replica.my.cnf") as c:
                c.execute("SELECT ss_total_pages FROM site_stats LIMIT 1;")
                self.ss_total_pages = c.fetchone()[0]
            return self.ss_total_pages
        except Exception, e:
            # we can also estimate the total number of pages from the dumpfile. This will be less precise, but is the
            # only option for wikis that have no replication yet.
            # We estimate using the 20130503 enwiki dump (9.8GB compressed) vs 20130604 enwiki ss_total_pages (30.3M),
            # resulting in a ~300 factor (which overestimates, but this is better for ETA predictions)
            return os.path.getsize(job.dumpfile) / 300 # estimated based on enwiki
        
    def run(self):
        dump = dumpscan.XmlDump(self.dumpfile)
        gen = dump.parse()

        ss_total_pages = self.estimate_total_pages()
        self.pages_processed = 0
        self.starttime = time.time()
        
        self.update_status()
        
        for self.pages_processed, page in enumerate(gen):
            if self.pages_processed % 1000 == 0:
                self.update_status()
                print ".",
            if all(map(lambda x: x(page), self.filters)):
                print page.title.encode('utf-8')
        
if __name__ == "__main__":
    job = Job(sys.argv[1])
    job.run()