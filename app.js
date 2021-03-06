var path = require('path'),
url = require('url'),
http = require('http'),
//var port = (process.env.VMC_APP_PORT || 8000);
port = (process.env.VMC_APP_PORT || 1337),
host = (process.env.VCAP_APP_HOST || 'localhost'),
fs = require('fs'),
header = require('fs').readFileSync(path.join(__dirname , 'pages/html', 'includes/header.html' ), 'utf8'),
pagehtml = require('fs').readFileSync(path.join(__dirname , 'pages/html', 'mongo.html' ), 'utf8'),
page = header,
connect = require('connect'),
cookieSessions = require('cookie-sessions'),
//var app = connect.createServer( cookieSessions({secret:'another secret'}) );
express = require("express"),
app = express();

if(process.env.VCAP_SERVICES){
  var env = JSON.parse(process.env.VCAP_SERVICES);
  var mongo = env['mongodb-1.8'][0]['credentials'];
}
else{
  var mongo = {
    "hostname":"localhost",
    "port":27017,
    "username":"",
    "password":"",
    "name":"",
	//"db":"test"
    "db":"halvadb"
  }
}

var generate_mongo_url = function(obj){
  obj.hostname = (obj.hostname || 'localhost');
  obj.port = (obj.port || 27017);
  obj.db = (obj.db || 'halvadb');

  if(obj.username && obj.password){
    return "mongodb://" + obj.username + ":" + obj.password + "@" + obj.hostname + ":" + obj.port + "/" + obj.db;
  }
  else{
    return "mongodb://" + obj.hostname + ":" + obj.port + "/" + obj.db;
  }
}

var mongourl = generate_mongo_url(mongo);

var addressObject = [];

require('mongodb').connect(mongourl, function(err, conn){
    conn.collection('items', function(err, coll){
        coll.find({}, {sort:[['_id','desc']]}, function(err, cursor){
            cursor.toArray(function(err, items){
                for(i=0; i<items.length;i++){
                    if(items[i].cx != null && items[i].cy != null && (items[i].name + " " + items[i].address) != null){
                        addressObject[i] = {
                            'cx' : items[i].cx,
                            'cy' : items[i].cy,
                            'name' : items[i].name + ' ' + items[i].address
                        }
                    }
                }
            });
        });
    });
});

//console.log(mongourl);
app.set('view engine', 'jade');
app.set('views', path.join(__dirname, 'pages/jade'));
app.use(connect.logger('dev')) ;
app.use(connect.bodyParser()) ;

//--- next two lines enable sessions support
app.use(connect.cookieParser());
app.use(connect.session({ secret: 'your secret here'} ));
//note: next two lines are from connect documentation and they seem to be wrong
//app.use(connect.cookieParser('secret string')) ;
//app.use(connect.session({ key: 'sid', cookie: { secure: true }})) ;
//--- routing

//---
//app.use(connect.favicon()) ;
//app.use(connect.static( path.join(__dirname, 'static')) ) ; //note, we give static files to everyone without any check
app.use('/img',express.static(path.join(__dirname, 'static/images')));
app.use('/js',express.static(path.join(__dirname, 'static/js')));
app.use('/css',express.static(path.join(__dirname, 'static/css')));

//--- finally
var wifiPageGen = require('./pages/wifiPage.js');
app.use('/wifi',function(req, res){
  wifiPageGen.create(req, res, mongourl);
});

var mainPageGen = require('./pages/mainPage.js');
app.use('/', function(req, res){
    res.render('mainPage',{ addressObject:addressObject });
  //mainPageGen.create( req, res, mongourl);
});

//app.listen(port, host);
app.listen(port);
