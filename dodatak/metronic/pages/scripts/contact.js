var Contact = function () {

    return {
        //main function to initiate the module
        init: function () {
			var map;
			$(document).ready(function(){
			  map = new GMaps({
				div: '#gmapbg',
				lat: 37.027667,
				lng: 37.307552
			  });
			   var marker = map.addMarker({
					lat: 37.027667,
					lng: 37.307552,
		            title: 'Loop, Inc.',
		            infoWindow: {
		                content: ""
		            }
		        });

			   marker.infoWindow.open(map, marker);
			});
        }
    };

}();

jQuery(document).ready(function() {
   Contact.init();
});

