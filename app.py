import os
import json
import glob

from flask import Flask, request, render_template
from redis import StrictRedis

import config

app = Flask(__name__, template_folder=config.TEMPLATE_PATH)
redis = StrictRedis(host=config.REDIS_HOST, db=config.REDIS_DB)
dumps_list = glob.glob('/public/datasets/public/*')

def ensure_wiki(wiki):
    return os.path.exists(os.path.join('/public/datasets/public/', wiki))

def make_key(*key_parts):
    return config.REDIS_PREFIX + "_" + '.'.join(key_parts)

def get_next_id():
    return redis.incr(make_key('nextid'))

@app.route('/index')
def index():
    return render_template('index.html', dumps=dumps_list, namespaces=config.NAMESPACES)

@app.route('/scan', methods=['POST'])
def submit():
    wiki = request.form['dump']
    if not ensure_wiki(wiki):
        app.abort(404)
    id = unicode(get_next_id())
    dir_name = os.path.join(config.BASE_PATH, id)
    os.makedirs(dir_name)
    with open(os.path.join(dir_name, 'status'), 'w') as f:
        f.write(json.dumps({"status": "pending"}))
    with open(os.path.join(dir_name, 'request'), 'w') as f:
        f.write(json.dumps(request.form))

    return "Running %s, see http://tools.wmflabs.org/dumpscan/%s" % (id, id)

if __name__ == '__main__':
    app.run()
