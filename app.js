var path = require('path') ;
var url = require('url');
var http = require('http');
//var port = (process.env.VMC_APP_PORT || 8000);
var port = (process.env.VMC_APP_PORT || 1337);
var host = (process.env.VCAP_APP_HOST || 'localhost');
var fs = require('fs');
var header = require('fs').readFileSync(path.join(__dirname , 'pages/html', 'includes/header.html' ), 'utf8');
var pagehtml = require('fs').readFileSync(path.join(__dirname , 'pages/html', 'mongo.html' ), 'utf8');
var page = header;

var connect = require('connect') ;
var cookieSessions = require('cookie-sessions');

//var app = connect.createServer( cookieSessions({secret:'another secret'}) );
var express = require("express");
var app = express();

/*if(process.env.VCAP_SERVICES){
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
}*/

/*var generate_mongo_url = function(obj){
  obj.hostname = (obj.hostname || 'localhost');
  obj.port = (obj.port || 27017);
  obj.db = (obj.db || 'test');

  if(obj.username && obj.password){
    return "mongodb://" + obj.username + ":" + obj.password + "@" + obj.hostname + ":" + obj.port + "/" + obj.db;
  }
  else{
    return "mongodb://" + obj.hostname + ":" + obj.port + "/" + obj.db;
  }
}

var mongourl = generate_mongo_url(mongo);*/


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
app.use(connect.favicon()) ;
app.use(connect.static( path.join(__dirname, 'static')) ) ; //note, we give static files to everyone without any check

/*//---
var siteUrls = [
  {pattern:'^/login/?$', restricted: false}
, {pattern:'^/logout/?$', restricted: true}
, {pattern:'^/geolocation/?$', restricted: false}
, {pattern:'^/mongo/?$', restricted: false}
, {pattern:'^/insert/?$', restricted: false}
, {pattern:'^/$', restricted: false}
, {pattern:'^/single/\\w+/?$', restricted: true}
];*/

/*function authorizeUrls(urls) {
  function authorize(req, res, next) {
    var requestedUrl = url.parse(req.url).pathname;
    for (var ui in urls) {
      var pattern = urls[ui].pattern;
      var restricted = urls[ui].restricted;
      if (requestedUrl.match(pattern)) {
        if (restricted) {
          if (req.session.authorized) {
            next();
            return;
          }
          else{
            req.session.redirectedFrom = requestedUrl;
            res.writeHead(303, {'Location': '/login'});
            res.end();
            return;
          }
        }
        else {
          next();
          return;
        }
      }
    }

    // we get here only if requested url is wrong
    console.log('common 404 for ', req.url);
    res.end('404: there is no ' + req.url + ' here');
  }
  return authorize ;
}


app.use('/', authorizeUrls(siteUrls));*/

/*function print_visits(request, response){
	require('mongodb').connect(mongourl, function(err, conn){
		conn.collection('items', function(err, coll){
			coll.find({}, {sort:[['_id','desc']]}, function(err, cursor){
				cursor.toArray(function(err, items){										
					var html = "<script>var doctorsJSON = [";
					for(i=0; i<items.length;i++){							
						 //html += JSON.stringify(items[i]) + "\n";
						 //console.log(items[i]);
						 html += '{"name":' + JSON.stringify(items[i].name) + ',' + '"address":' + JSON.stringify(items[i].address) + ',' + '"href":"","pic":"\/images\/wifi.png","coord":[' + JSON.stringify(items[i].cx) + ',' + JSON.stringify(items[i].cy) + ']}';
						 if(i != items.length -1){
							html += ',';
						 }
					}
					html += ']</script>';
					for (var key in request.body) {
					  var val = request.body[key];
					  //document.write(key+' = '+val);
					  console.log("REQUEST " + key+" = "+val);
					}				
					//if(request.body.address.length > 0){
						page += "<script>var query='" + request.body.address + "'</script>";
					//}
					page += html;
					page += pagehtml;
					response.writeHead(200, {"Content-Type": "text/html"});
					response.write(page);
					response.end();				 		
				});
			});
		});
	});
}*/

//--- finally
var mainPageGen = require('./pages/mainPage.js');
app.use('/', function(req, res){
  mainPageGen.create(req, res);
});



app.listen(port, host);
//app.listen(port);