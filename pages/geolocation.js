var http = require('http');
var path = require('path');
//var jadePath = path.join(__dirname , 'jade', 'geolocation.jade' );
//var str = require('fs').readFileSync(jadePath, 'utf8') 

//var fn = jade.compile(str, { filename: jadePath, pretty: true });

var baseLocals = {currentPage:'/geolocation'};

/*function GetInfo(req,res,content){
    ///var ip = req.connection.remoteAddress;
    var ip = "81.177.1.114";
    console.log("IP: " + ip);
    var options = {
        host: 'geoip.pidgets.com',
        port: 80,
        path: '/?ip=' + ip + '&format=json',
        method: 'POST'
      }; 
    var content = "";
    http.get(options, function(res) {
        res.on('data', function(chunk) {
            console.log("Got response: " + chunk);
            content = JSON.parse(chunk);
            return content;
            //console.log("result: " + content.city);        
        });        
    }).on('error', function(e) {
        console.log("Got error: " + e.message);
        return false;
    });
  }*/

function create( res, content) {
  //var content = "";
  //var str = GetInfo(req,res);
    console.log("Got content: " + content.city); 
    res.writeHead(200, {"Content-Type": "text/html"});
    res.write(JSON.stringify(content));
    res.end(); 
    /*str.on('end',function(res){
        res.writeHead(200, {"Content-Type": "text/html"});
        res.write("as");
        res.end();
    });*/
 

  /*var str = http.request(options, function(res) {
    console.log('STATUS: ' + res.statusCode);
    console.log('HEADERS: ' + JSON.stringify(res.headers));
    res.setEncoding('utf8');
    res.on('data', function (chunk) {
      console.log('BODY: ' + chunk);
    });
  });*/

  //var locals = baseLocals ;

  //var page = fn(locals);
  //var page = str;

}

function processPost(request, response) {
  //console.log('processing POST request: ', request.body.ip);

  /*var nextLocation = '/login' ;

  if ((request.body.login==='Thor')&&(request.body.password==='111')) {
    request.session.authorized = true;
    request.session.username = request.body.login;

    if ( typeof(request.session.redirectedFrom) === 'string') {
      nextLocation = request.session.redirectedFrom ;
      delete request.session.redirectedFrom ;
    }
    else {
      nextLocation = '/' ; //always redirect to main page
    }

    delete request.session.enteredWrongPassword;
    console.log('Thor is here!');
  }
  else {
    request.session.enteredWrongPassword = true;
    nextLocation = '/login';
  }*/
  var content = "";
  //var ip = "81.177.1.114";
  var ip = request.body.ip;
  var options = {
      host: 'geoip.pidgets.com',
      port: 80,
      path: '/?ip=' + ip + '&format=json',
      method: 'POST'
    }; 
  http.get(options, function(request) {
      var content = "";
      request.on('data', function(chunk) {
          console.log("Got response: " + chunk);
          content = JSON.parse(chunk);                        
      });
      request.on('end', function() {
          console.log("result: " + JSON.stringify(content));
          create(response, content) ;            
      });        
  }).on('error', function(e) {
      console.log("Got error: " + e.message);
      return false;
  });
}

//---
//exports.GetInfo = GetInfo;
exports.create = create ;
exports.processPost = processPost ;