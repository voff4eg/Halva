

/**
 * The class solves a task of search nearest subway station for some
 * set mail address. The task is solved with help google api serivces.
 * The result is LatLng object with coordinate of nearest station. The
 * object has additional fields stationName and duration (in seconds).
 
 * @param String   human readable mail address of a building in Moscow
 * @param function callBack its arguments are latitude and logitude, success flag, errormessage.
 *                 This function is called when the answer from google is got.
 * @param String   google key

 * @throws String  Invalid address or the address hasn't near any subway station
 Daneel Yaitskov (Даниил Яицков)
 email: rtfm.rtfm.rtfm@gmail.com
 phone: +7-951-527-16-35
 26.07.2011  16:45:51
 */
function NearestSubway(address, callBack, googleMap, mode, googleKey)  {
    var that = this;
    // округ москвы
    this.district = null;
    if (!mode.match(/^(walking|driving)$/)) {
        // exception because async call is not called yet
        throw "mode can be either 'walking' or 'driving'";
    }
    /**
     * any error pass flow control this method
     */ 
    this.fault = function (message) {
        callBack(null, false, message);
    };
    /**
     * begin async request
     */
    this.run = function () {
        // human mail address to lat and lng        
        var gc = new google.maps.Geocoder();
        gc.geocode({'address': address},
                        function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                var acomps = results[0].address_components;
                                stop_seach:
                                for (var i in acomps) {
                                    var types = acomps[i].types;
                                    for (var j in types){
                                        if (types[j] == "administrative_area_level_2") {
                                            that.district = acomps[i].long_name;
                                            break stop_seach;
                                        }     
										if (types[j] == "sublocality") {
                                            that.sublocality = acomps[i].long_name;
                                            break stop_seach;
                                        }                
                                    }          
                                }
                                that.geoCoordinatesGot(results[0].geometry.location);
                            } else {
                                that.fault("Geocode Google Service made fault: " + status);
                            }                                 
                        });        
    };
    /**
     * search subway station nearby
     * @param LatLng coordinates of a client
     */
    this.geoCoordinatesGot = function (latlng) {        
        var request = {
          location: latlng,
          radius: 3000,
          sensor: false,
          types: ['subway_station']
        };
        var service = new google.maps.places.PlacesService(googleMap);
        service.search(request,
                       function (result, status) {
                           that.findNeighbourSubwayStations(result, status, latlng); }
                       );        
    };
    /**
     * geometric length between two geo points
     * @param LatLng
     * @param LatLng
     * @return float
     */
    this.distance = function(point1, point2) {
        return Math.sqrt(Math.pow(point1.lat() - point2.lat(), 2)
                         + Math.pow(point1.lng() - point2.lng(), 2));
    };
    /**
     * find nearest subway station with help Distance Matrix Service
     * @param Array<PlaceResult> found objects
     * @param PlacesServiceStatus status
     * @param LatLng point of client
     */
    this.findNeighbourSubwayStations = function (result, status, latlng) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            if (!result.length) {
                this.fault("Location '" + address +
                           "' hasn't any subway station in radius of 1800 m.");                
            } else {
                var nearestPoints = this.findFirstThreeBest(result,latlng);
                that.estimateTime(nearestPoints, latlng);
            }
        } else {
            this.fault("Place Search Google Service made fault: " + status);
        }
    };
    /**
     * @param Array<LatLng + distance field>
     * @return Integer array index
     */
    this.nearestLocation = function(points) {
        var j = 0;
        for (var i = 1; i < points.length; i++)
            if (points[i].distance < points[j].distance)
                j = i;
        return j;
    };
    /**
     * @param Array<PlaceResult> found objects
     * @return Array<LatLng>
     */
    this.findFirstThreeBest = function(result,latlng) {
        var nearestPoints = [];
        var points = [];
        for (var i = 0; i < result.length; i++) {
            var p = result[i].geometry.location;
            p.distance = that.distance(latlng, p);
            p.stationName = result[i].name;
            points.push(p);                    
        }
        var n = that.nearestLocation(points);
        nearestPoints.push(points[n]);
        points.slice(n,1);
        if (points.length) {
            n = that.nearestLocation(points);
            nearestPoints.push(points[n]);
            points.slice(n,1);
            if (points.length) {
                n = that.nearestLocation(points);
                nearestPoints.push(points[n]);
            }                        
        }
        return nearestPoints;
    };
    this.getMode = function () {
        if (mode === 'walking')
            return google.maps.TravelMode.WALKING;
        return google.maps.TravelMode.DRIVING;
    };
    this.estimateTime = function(points, latlng) {
        var service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix({
                origins: [latlng],
                    destinations: points,
                    travelMode: this.getMode(),
                    unitSystem: google.maps.UnitSystem.METRIC,
                    avoidHighways: false,
                    avoidTolls: false
                    },
            function (response, status) { that.matrixGot(response, status, points); });
    };
    /**
     * @param DistanceMatrixResponse
     * @param DistanceMatrixStatus
     * @param Array<LatLng>
     */
    this.matrixGot = function (response, status, points) {
        if (status != google.maps.DistanceMatrixStatus.OK) {
          this.falut('Distance Maxtirx Service fault: ' + status);
        } else {
          var origins = response.originAddresses;
          var destinations = response.destinationAddresses;
          var mini = null; // from orig
          var minj = null; // to  dest
          for (var i = 0; i < origins.length; i++) {
            var results = response.rows[i].elements;
            for (var j = 0; j < results.length; j++) {
                if (mini === null) {
                    mini = i;
                    minj = j;
                } else {
                    var x = results[j].duration.value;
                    var y = response.rows[mini].elements[minj].duration.value;                    
                    if (x < y) {
                        mini = i;
                        minj = j;
                    }
                }
            }
          }
          points[minj].stationName =  points[minj].stationName;
          points[minj].district =  that.district;
          points[minj].sublocality =  that.sublocality;
          points[minj].duration = response.rows[mini].elements[minj].duration.value;
          callBack(points[minj], true, '');
        }
    };
}
