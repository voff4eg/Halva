/*line 330 center the balloon*/
var clientip = "";

$(document).ready(function() {
	
	$("#LocatorMap").each(function() {		
		$(this).attr("style","width:100%; height: " + ($(document).height() - 61) + "px; background-color:#EEE;");
	});

	$(".b-button").each(function() {
		new Button($(this));
	});
	
	$(".b-button_type-sign").click(function() {
		if($(this).attr("data-doctor")) {
			$(".b-popup-window[data-doctor=" + $(this).attr("data-doctor") + "]").popup();
		}
		else {
			$("#sign-popup").popup();
			_gaq.push(['_trackEvent', 'Нажата кнопка Записаться', 'на странице врача', 'id=' + $(this).closest(".b-doctor-page__card").attr("data-doctor")]);
		}
		return false;
	});
	
	$(".b-sign-in__link").click(function() {
		$("#login-popup").popup();
		return false;
	});
	
	$("[data-placeholder]:visible").placeholder();
	
	CreateIpScript();
	//getGeoIp();
	
});

function my_callback(json) {
	//alert("IP :" + json.IP + " nCOUNTRY: " + json.COUNTRY);
	window.ip = json.IP;
	getGeoIp();
}

function my_callback2(json) {
	// more information at http://api.easyjquery.com/test/demo-ip.php
	//alert("IP :" + json.IP + " nCOUNTRY: " + json.COUNTRY + " City: " + json.cityName + " regionName: " + json.regionName);
	window.ip = json.IP;
	getGeoIp();
}

function CreateIpScript(){
	EasyjQuery_Get_IP("my_callback2","full");
}

function getGeoIp(res) {
	//if(typeof geoip_latitude == 'function'){
		/*var latitude  = geoip_latitude(),
			longitude = geoip_longitude(),
			city	  = geoip_city();*/
		var latitude = '';
		var longitude = '';
		var city = '';	
		// Определяем все параметры
		// По идее можно определять - есть ли город и если он определен то не посылать запрос на сервер
		// Потестив j.maxmind были выявлены проблемы с городом, поэтому применяется решение на backend'e
		if(window.ip){
			$.ajax({
				url: '/geolocation',
				type: 'post',
				data: 'ip='+window.ip,			
				success: function(response){
					if(response){
						if(response.city == false){					
							// Если и на бакенде город не определился, определяем город через geocoder google maps					
							geoCode(response.lat, response.lng);
							latitude = '';
							longitude = '';
							city = '';
						}else{					
							var obj = JSON.parse(response);
							//latitude = obj.latitude;
							//longitude = obj.longitude;
							//city = obj.city;					
							InitCoord(obj.city, obj.latitude, obj.longitude);
						}
					}else{
						InitCoord("", "", "");			
					}
				}
			});
		}else{
			InitCoord("", "", "");
		}
	//}
	
	geoCode = function(lat, lng){
		var geocoder = new google.maps.Geocoder(),
		latlng = new google.maps.LatLng(lat, lng);
		geocoder.geocode({'latLng': latlng}, function(results, status) {		
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[1]) {
			  alert(results[1].formatted_address);
			} else {
			  alert("No results found");
			}
		  } else {
			alert("Geocoder failed due to: " + status);
		  }
		});
	}
	
	//window.userCoord.city = "Moscow";
	//alert("City: " + window.userCoord.city + "latitude: " + window.userCoord.latitude + "longitude: " + window.userCoord.longitude);
}

function InitCoord(city, lat, long){
	window.userCoord = {};
	if(city){
		if(city == "Moscow"){
			window.userCoord.city = city;
			if(lat){
				window.userCoord.latitude = lat;
				window.userCoord.zoom = 15;
			}else{
				window.userCoord.latitude = 55.7522;
				window.userCoord.zoom = 15;				
			}
			if(long){
				window.userCoord.longitude = long;
				window.userCoord.zoom = 15;
			}else{				
				window.userCoord.longitude = 37.6156;
				window.userCoord.zoom = 15;
			}
		}else{
			window.userCoord.city = "Moscow";
			window.userCoord.latitude = 55.7522;
			window.userCoord.longitude = 37.6156;
			window.userCoord.zoom = 15;			
		}
	}else{
		window.userCoord.city = "Moscow";
		window.userCoord.latitude = 55.7522;
		window.userCoord.longitude = 37.6156;
		window.userCoord.zoom = 15;
	}

	new DoctorsMap();
}

function DoctorsMap() {
	var self = this;
	var num = doctorsJSON.length;//num of doctors to show
	
	init();
	
	function init() {
		setPreloader();
		
		getUserLocation();
		loadMap();
		initSearch();
		mapBlockEvents();
		resultBlockEvents();
		moreButtonsEvents();
		
		removePreloader();
	}
	
	function showDoctorsArray() {
		var win = window.open('','win','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
		
		var text = "";
		
		for(var i = 0; i < doctorsJSON.length; i++) {
			
			text += doctorsJSON[i].id + " ";
			if(doctorsJSON[i].coord) {
				text += doctorsJSON[i].coord.join(" ");
			}
			text += "<br>";
		}
		
		
		win.document.write('<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /><title>geo</title></head><body>'+text+'</body></html>');
	}
	
	function moreButtonsEvents() {
		self.$moreButtons = $(".b-locator__map__more__button, #block-locator-result .b-get-more a")
		self.$moreButtons.click(function() {
			if($(this).parent().is(".b-get-more")) {
				_gaq.push(['_trackEvent', 'Нажата кнопка Показать еще', 'на карте', '']);
			}
			else {
				_gaq.push(['_trackEvent', 'Нажата кнопка Показать еще', 'на блоке Врачи поблизости', '']);
			}
			increaseRange();
			if(self.arrayPosition.isFinished) {
				self.$moreButtons.hide();
			}
			showResult(doctorsJSON, self.arrayPosition);
			return false;
		});
	}
		
	function getUserLocation() {
		ymaps.ready(function() {
			ymaps
				.geocode(userCoord.city)
				.then(function (res) {
					var counter = 0;
					res.geoObjects.each(function(obj) {						
						if(counter == 0) {
							self.userLocation = obj.properties.get('name');
						}
						counter++;
					});
				});
		});
	}
	
	function setPreloader() {
		$(".b-locator__service").addClass("i-preloaded");
		$(".b-locator__map .i-decor-block-shadow").before('<div class="b-locator-preloader"></div>');
	}
	
	function removePreloader() {
		var loadedID = setInterval(function() {
			if(self.loaded) {
				$(".b-locator__service").removeClass("i-preloaded");
				$(".b-locator__map .b-locator-preloader").remove();
				clearInterval(loadedID);
			}
		}, 500);
	}
	
	function mapBlockEvents() {
		$("#LocatorMap")
			.delegate(".b-button_type-sign", "click", function() {
				popupSign($(this));
				_gaq.push(['_trackEvent', 'Нажата кнопка Записаться', 'на карте', 'id=' + $(this).attr("data-doctor")]);
				return false;
			});
	}
	
	function resultBlockEvents() {
		$("#block-locator-result")
			.delegate(".b-button_type-sign", "click", function() {
				popupSign($(this));
				_gaq.push(['_trackEvent', 'Нажата кнопка Записаться', 'на блоке Врачи поблизости', 'id=' + $(this).attr("data-doctor")]);
				return false;
			});
	}
	
	function popupSign($elem) {
		if($elem.attr("data-doctor")) {
			$(".b-popup-window[data-doctor=" + $elem.attr("data-doctor") + "]").popup();
		}
		else {
			$("#sign-popup").popup();
		}
	}
	
	function initSearch() {	
		$("#LocatorForm").submit(function () {
			var search_query = $(this).find("#search-input").val();
			
			_gaq.push(['_trackEvent', 'Отправка запроса Уточните свой адрес', 'из города ' + self.userLocation, search_query]);
			
			searchHandler(search_query);
			self.map.setZoom(13, {duration: 500});
			self.$moreButtons.show();			
			return false;
		});
		
		$("#LocatorForm #search-input").unbind("focus").focus(function() {
			if($(this).closest(".i-placeholder").is("div")) {
				$(this).closest(".i-placeholder").removeClass("i-placeholder");
				$(this).val(self.userLocation + ", ");
			}
		});
	}
	
	function increaseRange() {
		self.arrayPosition[0] = self.arrayPosition[0] + num;
		
		if((self.arrayPosition[1] + num) > doctorsJSON.length) {
			self.arrayPosition[1] = doctorsJSON.length;
			self.arrayPosition.isFinished = true;
		}
		else {
			self.arrayPosition[1] = self.arrayPosition[1] + num;
		}
	}
	
	function searchHandler(search_query) {

		ymaps.geocode(search_query, {results: 1}).then(function (res) {
			self.map.coord = getMapCoord(res);
			clearResult();
			//move map center
			self.map.panTo(self.map.coord, {
				duration: 500
			});
			
			calculateDistance(res);
			sortDoctorsByDistance();
			
			self.arrayPosition = [0, num];			
			showResult(doctorsJSON, self.arrayPosition);
			setUserMark();
			
			self.loaded = true;
		});
	}
	
	function setUserMark() {
		removeUserMark();
		getUserMark();
		self.map.geoObjects.add(self.user);
	}
	
	function getUserMark() {
		self.user = new ymaps.Placemark(
			self.map.coord,
			{
				iconContent: '<div class="b-locator__map__user-mark"></div>'
			},
			{
				iconLayout:"default#imageWithContent",
				iconImageHref: '/images/spacer.gif',
				iconImageSize: [1, 1]
			}
		);
	}
	
	function removeUserMark() {
		if(self.user) {
			self.map.geoObjects.remove(self.user);
		}
	}
	
	function showResult(array, range) {
		showOnMap();
		//showOnPage();
		
		function showOnMap() {
			for(var i = range[0]; i < range[1]; i++) {
				if(array[i].geo) {
					self.map.geoObjects.add(array[i].geo);					
				}
			}
		}
		
		function showOnPage() {
			var $block = $(".b-locator__result__content");
			
			var $resultBlock = $('<div class="b-locator__result__block"></div>');
			
			for(var i = range[0]; i < range[1]; i++) {
				$resultBlock.append('<div class="b-locator__result__item b-doctor-vcard"><div class="b-doctor-vcard__pic"><a href="' + array[i].href + '"><img src="' + array[i].pic + '" width="110" height="110" alt="' + array[i].name + '" class="b-doctor-vcard__pic__img"></a><div class="b-doctor-vcard__more"><a href="' + array[i].href + '" class="b-doctor-vcard__more__link">Подробнее</a></div></div><div class="b-doctor-vcard__info"><div class="b-doctor-vcard__infotext"><div class="b-doctor-vcard__name"><a href="' + array[i].href + '">' + array[i].name + '</a></div><div class="b-doctor-vcard__text"><div class="b-doctor-vcard__address">' + array[i].address + '</div></div></div><div class="b-doctor-vcard__sign"><button class="b-button b-button_type-sign b-button_theme-1-S-sign" data-doctor="' + array[i].id + '"></button></div></div><div class="i-clearfix"></div></div>');
			}
			
			$resultBlock.append($('<div class="i-clearfix"></div>'));
			
			$block.find(".b-get-more").before($resultBlock);
		}
		
		$(".b-locator__result").addClass("i-shown");
	}
	
	function clearResult() {
		clearOnMap();
		clearOnPage();
		
		function clearOnMap() {
			while(self.map.geoObjects.getIterator().getNext()) {
				self.map.geoObjects.remove(self.map.geoObjects.getIterator().getNext());
			}
		}
		
		function clearOnPage() {
			var $block = $(".b-locator__result__content");
			
			$block.find(".b-locator__result__block").each(function() {
				$(this).remove();
			});
		}
		
		$(".b-locator__result").removeClass("i-shown");
	}
	
	function getMapCoord(res) {
		return res.geoObjects.get(0).geometry.getCoordinates();
	}
	
	function calculateDistance(res) {
		for(var i = 0; i < doctorsJSON.length; i++) {
			if(doctorsJSON[i].coord) {
				doctorsJSON[i].distance = Math.sqrt(Math.pow((self.map.coord[0] - doctorsJSON[i].coord[0]), 2) + Math.pow((self.map.coord[1] - doctorsJSON[i].coord[1]), 2));
			}
		}
	}
	
	function sortDoctorsByDistance() {
		doctorsJSON.sort(sortFunction);
		
		function sortFunction(a, b) {
			if(b.distance == undefined || a.distance < b.distance)
				return -1;
			if(a.distance == undefined || a.distance > b.distance)
				return 1;
			
			return 0;
			
		}
	}
	
	function loadMap() {
		ymaps.ready(function() {
			createMap();
			mapEvents();
			
			addDoctorsGeo();
			
			setTimeout(function() {
				showAllDoctorsWithDelay();
				searchHandler(userCoord.city);
				//showDoctorsArray();
			}, 1000);
		});
	}
	
	function showAllDoctorsWithDelay() {
		var portion = doctorsJSON.length;
		var range = [0, portion];
		
		function increaseRange() {
			range[0] = range[0] + portion;
			
			if((range[1] + portion) > doctorsJSON.length) {
				range[1] = doctorsJSON.length;
				range.isFinished = true;
			}
			else {
				range[1] = range[1] + portion;
			}
		}
		
		var loadDoctors = setInterval(function() {
			showDoctors();
			checkClearInterval();
		}, 0);
		
		function showDoctors() {
			for(var i = range[0]; i < range[1]; i++) {
				if(doctorsJSON[i].geo) {
					self.map.geoObjects.add(doctorsJSON[i].geo);
				}
			}
			increaseRange();
		}
		
		function checkClearInterval() {
			if(range.isFinished || !doctorsJSON[range[0]].geo) {
				clearInterval(loadDoctors);
			}
		}
		
	}
	
	function mapEvents() {
		self.map.events.add('click', function () {
			self.map.balloon.close();
		});
		
		self.map.events.add('balloonopen', function (obj) {
			/*console.log(obj.originalEvent.balloon.getPosition());
			console.log(userCoord.latitude);
			console.log(userCoord.longitude);
			/*self.map.panTo(obj.originalEvent.balloon.getPosition(), {
				duration: 200
			});*/
		});
	}
	
	function createMap() {
		self.map =  new ymaps.Map('LocatorMap', {
			center: [userCoord.latitude, userCoord.longitude],
			zoom: userCoord.zoom
		});
		
		self.map.controls.add('zoomControl');

		//var myGeocoder = ymaps.geocode([userCoord.latitude,userCoord.longitude]);
		var myGeocoder = ymaps.geocode([userCoord.latitude,userCoord.longitude]);
		myGeocoder.then(
		    function (res) {
		    	var nearest = res.geoObjects.get(0);
		    	console.log(nearest);
        		var name = nearest.properties.get('text');
        		showUserAddress(name);
		        //alert('Координаты объекта :' + name);
		    },
		    function (err) {
		        alert('Ошибка');
		    }
		);		
	}

	function showUserAddress(str){
		$("#address").each(function() {		
			$(this).append(str);
		});
	}
	
	function addDoctorsGeo() {
		
		for(var i = 0; i < doctorsJSON.length; i++) {
			/*(function(i) {
				ymaps.geocode(doctorsJSON[i].address, {results: 1}).then(function(res) {
					
					var firstGeoObject = res.geoObjects.get(0);
					
					if(firstGeoObject) {
						doctorsJSON[i].geo = new ymaps.Placemark(
							firstGeoObject.geometry.getCoordinates(),
							{
								// Контент балуна
								balloonContent: '<div class="b-locator__map__doctors__item b-map-infopoint"><div class="b-map-infopoint__content"><div class="b-map-infopoint__person"><div class="b-map-infopoint__person__pic"><a href="' + doctorsJSON[i].href + '"><img src="' + doctorsJSON[i].pic + '" width="65" height="65" alt="' + doctorsJSON[i].name + '" class="b-map-infopoint__person__pic__img"></a></div><div class="b-map-infopoint__person__info"><div class="b-map-infopoint__name"><a href="' + doctorsJSON[i].href + '">' + doctorsJSON[i].name + '</a></div><div class="b-map-infopoint__occupation">' + doctorsJSON[i].work_position + '</div></div><div class="i-clearfix"></div></div><div class="b-map-infopoint__address"><div class="b-map-infopoint__address__heading">Адрес:</div>' + doctorsJSON[i].address + '</div><div class="b-map-infopoint__links"><button class="b-button b-button_type-sign b-button_theme-1-S-sign" data-doctor="' + doctorsJSON[i].id + '"></button><a href="' + doctorsJSON[i].href + '" class="b-map-infopoint__more">Подробнее</a></div><div class="b-map-infopoint__pointer"></div></div></div>',
								iconContent: '<div class="b-locator__map__doctors__item b-map-point"><div class="b-map-point__content"><img src="' + doctorsJSON[i].pic + '" width="28" height="28" alt="' + doctorsJSON[i].name + '" class="b-map-point__pic"></div></div>'
							},
							{
								// Указываем, что макет иконки будет с контентом
								iconLayout:"default#imageWithContent",
								iconImageHref: '/images/spacer.gif',
								iconImageSize: [1, 1],
								balloonLayout: "default#imageWithContent",
								balloonImageHref: '/images/spacer.gif',
								balloonImageSize: [1, 1],
								// Балун не имеет тени
								balloonShadow: false
							}
						);
						doctorsJSON[i].coord = firstGeoObject.geometry.getCoordinates();
					}
					
				});
			})(i);*/
			
			//read coords directly from database (without connection to yandex maps)
			if(doctorsJSON[i].coord) {
				doctorsJSON[i].geo = new ymaps.Placemark(
					doctorsJSON[i].coord,
					{
						// Контент балуна
						balloonContent: '<div class="b-locator__map__doctors__item b-map-infopoint"><div class="b-map-infopoint__content"><div class="b-map-infopoint__person"><div class="b-map-infopoint__person__pic"><a href="' + doctorsJSON[i].href + '"><img src="' + doctorsJSON[i].pic + '" width="65" height="65" alt="' + doctorsJSON[i].name + '" class="b-map-infopoint__person__pic__img"></a></div><div class="b-map-infopoint__person__info"><div class="b-map-infopoint__name"><a href="' + doctorsJSON[i].href + '">' + doctorsJSON[i].name + '</a></div></div><div class="i-clearfix"></div></div><div class="b-map-infopoint__address"><div class="b-map-infopoint__address__heading">Адрес:</div>' + doctorsJSON[i].address + '</div><div class="b-map-infopoint__links"><button class="b-button b-button_type-sign b-button_theme-1-S-sign" data-doctor="' + doctorsJSON[i].id + '"></button><a href="' + doctorsJSON[i].href + '" class="b-map-infopoint__more">Подробнее</a></div><div class="b-map-infopoint__pointer"></div></div></div>',
						iconContent: '<div class="b-locator__map__doctors__item b-map-point"><div class="b-map-point__content"><img src="' + doctorsJSON[i].pic + '" width="28" height="28" alt="' + doctorsJSON[i].name + '" class="b-map-point__pic"></div></div>'
					},
					{
						// Указываем, что макет иконки будет с контентом
						iconLayout:"default#imageWithContent",
						iconImageHref: '/images/spacer.gif',
						iconImageSize: [1, 1],
						balloonLayout: "default#imageWithContent",
						balloonImageHref: '/images/spacer.gif',
						balloonImageSize: [1, 1],
						// Балун не имеет тени
						balloonShadow: false
					}
				);
			}
		
		}
	}
	
}

/*------------------------------------------------------*/

function Button($elem) {
	var self = this;
	self.$elem = $elem;
	
	init();
	
	function init() {
		elemEvents();
	}
	
	function elemEvents() {
		self.$elem
		.hover(
			function() {
				$(this).addClass("i-button-hover");
			},
			function() {
				$(this).removeClass("i-button-hover");
			}
		)
		.mousedown(
			function() {
				$(this).addClass("i-button-active");
			}
		)
		.mouseup(
			function() {
				$(this).removeClass("i-button-active");
			}
		);
	}
}

(function($) {
	var defaults = {
		opaco:true,
		valign:"center",
		align:"center",
		after:function(thisElem){
			$(thisElem).find("[data-placeholder]").placeholder();
		},
		closeElem:".b-popup-window__close, .b-popup-window__ok .b-button"
		//close:function(thisElem){} overwrites default function
	};
	
	$.fn.popup = function(params) {
		var options = $.extend({}, defaults, params);
		$(this).each(function() {
			var $this=$(this);
			if(!$this.is(":visible")) {
				var topPx="20px", leftPx="50%", marginLeft=0, outerHeight=$this.outerHeight(), winHeight=$(window).height();
				switch(options.valign) {
					case "top": topPx = $(window).scrollTop() + 20 + "px"; break;
					case "center":
						if (winHeight > outerHeight) {topPx = winHeight/2 + $(window).scrollTop() - outerHeight/2 - 20 + "px";}
						else {topPx = $(window).scrollTop() + 20 + "px";}
						break;
					case "bottom": topPx = $(window).scrollTop()+winHeight-outerHeight-20 + "px";break;
				}
				switch(options.align) {
					case "left": leftPx = "0px"; break;
					case "center":
						leftPx = "50%";
						marginLeft = -$this.outerWidth()/2+"px";
						break;
					case "right": leftPx = $(document).width()-$this.outerWidth()+"px";break;
				}
				if(options.opaco === true) {
					$this.before('<div id="opaco"></div>');
					$("#opaco").css({width:"100%", height:$(document).height()+"px"}).show();
					var closeElem = $("#opaco");
					if(options.closeElem){closeElem=$("#opaco, "+options.closeElem+"");}
					
					var closeFunc = function() {$this.popup(options);};
					if(options.close){closeFunc=function(){options.close(this);};}
					closeElem.click(function(e) {
						closeFunc();
						e.preventDefault();
					});
				}
				$this.show().css({marginLeft:marginLeft, left:leftPx}).animate({top:topPx}, 500, function() {
					if(options.after) {options.after(this);}
				});
			}
			else {
				$this.hide();
				$("#opaco").remove();
				if(options.closeElem){
					$(""+options.closeElem+"").unbind("click");}
			}
			if(options.after) options.after($this);
		});
		return this;
	};
})(jQuery);

(function($) {
	var defaults = {
		//text:"",
		//color:"#aaaaaa"
	};
	$.fn.placeholder = function(params) {
		
		var options = $.extend({}, defaults, params);
		
		$(this).each(function() {
			
			var self = this;
			self.$elem = $(this),
			self.$formField = self.$elem.closest(".b-form-field");
			self.placeholderText = options.text || self.$elem.attr("data-placeholder");
			
			init();
			
			function init() {
				if(!self.$placeholder) {
					createPlaceholder();
				}
				turnOn();
				handleEvents();
			}
			
			function turnOn() {
				if(self.$elem.val() == "") {
					self.$formField.addClass("i-placeholder");
				}
			}
			
			function handleEvents() {
				self.$placeholderText.click(function() {
					self.$elem.focus();
				});
				
				self.$elem
					.focus(function() {
						onFocus();
					})
					.blur(function() {
						onBlur();
					});
				
			}
			
			function createPlaceholder() {
				self.$placeholder = $('<div class="b-form-field__placeholder"><div class="b-form-field__placeholder__text">' + self.placeholderText + '</div></div>');
				self.$placeholderText = self.$placeholder.find(".b-form-field__placeholder__text");
				
				self.$elem.before(self.$placeholder);
				
				setPlaceholderSize();
			}
			
			function setPlaceholderSize() {
				self.$placeholderText
					.css(
						{
							width: self.$elem.outerWidth() + "px",
							height: self.$elem.outerHeight() + "px",
						}
					);
			}
			
			function onFocus() {
				self.$formField.removeClass("i-placeholder");
			}
			
			function onBlur() {
				if (self.$elem.val() == "") {
					self.$formField.addClass("i-placeholder");
				}
			}
			
		});
		return this;
	};
})(jQuery);
