var fs = require('fs'),
jade = require('jade'),
path = require('path'),
jadePath = path.join(__dirname , 'jade', 'mainPage.jade'),
str = fs.readFileSync(jadePath, 'utf8'),
addressObject = [];

var baseLocals = {currentPage:'/'};

function create(request, response, mongourl) {

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

    var locals = baseLocals ;
    locals.loggedIn = request.session.authorized ? request.session.authorized : false;
    locals.username = request.session.username ? request.session.username : 'no matter what';

    //var page = fn({address: addressObject, d: 'a'});
    //var fn = jade.compile(str, { filename: jadePath, pretty: true})
    //fn.call({address : {'val' : 'This is a Test', 'asdasd' : 'asdfsad'}}, addressObject);
    //var page = fn({}});
   /* console.log(addressObject.cx);
    console.log(addressObject.cy);
    console.log(page);*/

    /*response.writeHead(200, {'Content-Type': 'text/html'});
    response.write("2");
    response.end();*/
    response.render('mainPage',{ addressObject:addressObject });
}
//---
exports.create = create;