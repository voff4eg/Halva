var fs = require('fs');
var path = require('path');
var header = require('fs').readFileSync(path.join(__dirname , 'html', 'includes/header.html' ), 'utf8');
var pagehtml = require('fs').readFileSync(path.join(__dirname , 'html', 'mongo.html' ), 'utf8');

var html = "";

function create(request, response, mongourl) {
	function print_visits(req, res, mongourl){
		/* Connect to the DB and auth */
		require('mongodb/lib/mongodb').connect(mongourl, function(err, conn){
			conn.collection('items', function(err, coll){
				coll.find({}, {sort:[['_id','desc']]}, function(err, cursor){
					cursor.toArray(function(err, items){					
						for(i=0; i<items.length;i++){
							 html += JSON.stringify(items[i]) + "\n";
						}
						return html;
					});
				});
			});
		});
	}
  var locals = baseLocals ;
  locals.loggedIn = request.session.authorized ? request.session.authorized : false;
  locals.username = request.session.username ? request.session.username : 'no matter what';
//  console.log('at main: ', request.session.authorized, request.session.username);

  //var page = fn(locals);
  var page = header;
  js = print_visits(request, response, mongourl);
  page += js;
  page += pagehtml;
  page += footer;

  response.writeHead(200, {"Content-Type": "text/html"});
  response.write(page);
  response.end();
}

//---
exports.create = create ;