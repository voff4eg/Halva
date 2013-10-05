var fs = require('fs');
var path = require('path');
var pagehtml = "var addressPoints = [";

var html = "";

function create(request, response, mongourl) {
  function print_visits(request, response, mongourl){
    /* Connect to the DB and auth */
    console.log(mongourl);
    require('mongodb').connect(mongourl, function(err, conn){
      conn.collection('items', function(err, coll){
        coll.find({}, {sort:[['_id','desc']]}, function(err, cursor){
          cursor.toArray(function(err, items){
            for(i=0; i<items.length;i++){
              var addressArray = new Array();
              addressArray[0] = items[i].cx;
              addressArray[1] = items[i].cy;
              addressArray[2] = items[i].name + " " + items[i].address;
              pagehtml += JSON.stringify(addressArray) + ",";
            }
            console.log(pagehtml);            
            pagehtml = pagehtml.substring(0,pagehtml.length-1);
            pagehtml += "]";
            console.log(pagehtml);
            response.writeHead(200, {"Content-Type": "application/javascript"});
            response.write(pagehtml);
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