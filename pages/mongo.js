var fs = require('fs');
var path = require('path');
var header = require('fs').readFileSync(path.join(__dirname , 'html', 'includes/header.html' ), 'utf8');
var pagehtml = require('fs').readFileSync(path.join(__dirname , 'html', 'mongo.html' ), 'utf8');

var html = "";

function create(request, response, mongourl) {
	function print_visits(request, response, mongourl){
		/* Connect to the DB and auth */
		console.log(mongourl);
		require('mongodb').connect(mongourl, function(err, conn){
			conn.collection('items', function(err, coll){
				coll.find({}, {sort:[['_id','desc']]}, function(err, cursor){
					cursor.toArray(function(err, items){											
						for(i=0; i<20;i++){							
							 html += JSON.stringify(items[i]) + "\n";
							 console.log(JSON.stringify(items[i]));
						}
						var page = header;
						var js = print_visits(request, response, mongourl);
						page += js;
						console.log(request.body);
						if(request.body.address.length > 0){
							page += "<script>var query='" + request.body.address + "'</script>";
						}
						page += pagehtml;
						response.writeHead(200, {"Content-Type": "text/html"});
					    response.write(page);
				        response.end();				 		
					});
				});
			});
		});
	}
	print_visits(request, response, mongourl);
}

//---
exports.create = create ;