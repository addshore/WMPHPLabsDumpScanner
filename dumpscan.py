#!/data/project/dumpscan/dumpscan/bin/python
import site
site.addsitedir("/data/project/dumpscan/dumpscan/lib/python2.7/site-packages")

from wsgiref.handlers import CGIHandler
from werkzeug.debug import DebuggedApplication
from app import app

app.debug = True
CGIHandler().run(DebuggedApplication(app))
