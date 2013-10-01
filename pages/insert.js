var path = require('path') ;
var fs = require('fs');
var wf = require('fs').readFileSync(path.join(__dirname , 'wf2mongo.txt' ), 'utf8');
var obj = JSON.parse(wf);

function insert2file(items){
      var object_to_insert = [];
	  for(i=0; i<items.length;i++){
		object_to_insert[i] = { 'name': items[i].name,
						'address': items[i].address,
						'type': "wifi",
						'icon': "/images/icons/wifi.png",
						'create_date': new Date(),
						'update_date': new Date(),
						'description': "",
						'cx': items[i].cx,
						'cy': items[i].cy,
						'author': "wifi4free"                               
					  };             
	  }
	  fs.writeFile('wf2mongo.txt', JSON.stringify(object_to_insert), function (err) {
		if (err) throw err;
		console.log('It\'s saved!');
	  });
}

function create(req,res,mongourl) {
  require('mongodb').connect(mongourl, function(err, conn){
    conn.collection('items', function(err, coll){      
      /*coll.find({}, {sort:[['_id','desc']]}, function(err, cursor){
        
        cursor.toArray(function(err, items){                    
          insert2file(items);		  
        });
      });*/
	  coll.remove({});
	  for(i=0; i<obj.length;i++){    
		  object_to_insert = { 'name': obj[i].name,
								'address': obj[i].address,
								'type': "wifi",
								'icon': "/images/icons/wifi.png",
								'create_date': new Date(),
								'update_date': new Date(),
								'description': "",
								'cx': obj[i].cx,
								'cy': obj[i].cy,
								'author': "wifi4free"                               
							  };         
		  coll.insert( object_to_insert, {safe:true}, function(err){
			if (err) throw err;
			console.log(JSON.stringify(object_to_insert));                   
		  });
	  }	 
    });
  });  
  res.writeHead(200, {"Content-Type": "text/html"});
  res.write("worked");
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

exports.create = create ;