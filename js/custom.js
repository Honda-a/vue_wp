$(document).ready(function () {
  
    // Button goto Top
		if ($(window).width() > 779) {
			$(window).scroll(function() {
					if($(window).scrollTop() != 0) {
							$('#gototop').fadeIn();
					} else {
							$('#gototop').fadeOut();
					}
			});
		}
    $('#gototop').click(function() {
        $('html, body').animate({scrollTop:0},500);
        return false;
    });
	

});

function is_url(str) {
	regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
	if (regexp.test(str)) {
		return true;
	} else {
		return false;
	}
}

function Get(yourUrl) {
	var Httpreq = new XMLHttpRequest()
	Httpreq.onreadystatechange = function () {
		if (this.readyState === this.DONE) {
		}
	}
	Httpreq.open("GET", yourUrl, false);
	Httpreq.send(null);
	return Httpreq.responseText;
}

function render(template, data) {
	const pattern = /\{(.*?)\}/g; // {property}
	return template.replace(pattern, (match, token) => data[token]);
}

const json_obj = JSON.parse(Get("http://wp.scan/js/messages.json"));
const APIURL = "http://wp.scan:5000/api/v1/";
const WPVERSION = "wp-version/";
const ACCESS = "access-check/";
const BRUTEFORCE = "brute-force/";
const HEADER = "header-data/";
const IMAGE = {
	O: "<img src='img/result/check02.png' alt='O'>",
	X: "<img src='img/result/check03.png' alt='X'>",
	T: "<img src='img/result/check04.png' alt='△'>"
};

$("input.url").change(function(){
	if (!/^https*:\/\//.test(this.value) && this.value) {
		this.value = "http://" + this.value;
	}
});

get_security_data = function (url, bruteForce) {

	$.getJSON(APIURL + WPVERSION + url, function (data) {
		var response = data["response"];

		if (response) {
			$("#wpversion div.ico").remove(".ball-pulse");
			var update_status = response["update_status"] ? response["update_status"] : "defualt";
			var img_key = json_obj["wp-version"][update_status]["image"];
			var message = json_obj["wp-version"][update_status]["message"];

			$("#wpversion div.ico").html(IMAGE[img_key]);
			$("#wpversion div.btm").empty().html(render(message, response));

		}

	}).fail(function (data) {
		$("#wpversion div.ico").remove(".ball-pulse");
		var img_key = json_obj["wp-version"]["defualt"]["image"];
		var message = json_obj["wp-version"]["defualt"]["message"]

		$("#wpversion div.ico").html(IMAGE[img_key]);
		$("#wpversion div.btm").empty().html(render(message, data));
	});

	$.getJSON(APIURL + HEADER + url, function (data) {
		var response = data["response"];
		
		if (response["PHP-version"] !== "NA") {
			$("#phpversion div.ico").remove(".ball-pulse");

				var update_status = response["PHP-version"]["update_status"];
				var img_key = json_obj["php-version"][update_status]["image"];
				var message = json_obj["php-version"][update_status]["message"];

				$("#phpversion div.ico").html(IMAGE[img_key]);
				$("#phpversion div.btm").empty().html(render(message, response["PHP-version"]));


		} else {
			var img_key = json_obj["php-version"]["defualt"]["image"];
			var message = json_obj["php-version"]["defualt"]["message"];

			$("#phpversion div.ico").html(IMAGE[img_key]);
			$("#phpversion div.btm").empty().html(message);
		}

		var ssl = String(response["ssl"]);
		
		var img_key = json_obj["ssl"][ssl]["image"];
		var message = json_obj["ssl"][ssl]["message"];

		$("#ssl div.ico").remove(".ball-pulse");
		$("#ssl div.ico").html(IMAGE[img_key]);
		$("#ssl div.btm").empty().html(message);



		if (response["server"] == "NA") {
			var img_key = json_obj["server"]["true"]["image"];
			var message = json_obj["server"]["true"]["message"];

			$("#server div.ico").remove(".ball-pulse");
			$("#server div.ico").html(IMAGE[img_key]);
			$("#server div.btm").empty().html(message);

		} else {
			var img_key = json_obj["server"]["false"]["image"];
			var message = json_obj["server"]["false"]["message"];

			$("#server div.ico").remove(".ball-pulse");
			$("#server div.ico").html(IMAGE[img_key]);
			$("#server div.btm").empty().html(message);

		}
	}).fail(function (data) {
		var img_key = json_obj["server"]["false"]["image"];
		var message = json_obj["server"]["false"]["message"];

		$("#server div.ico").remove(".ball-pulse");
		$("#server div.ico").html(IMAGE[img_key]);
		$("#server div.btm").empty().html(message);
	});
	
	$.getJSON(APIURL + ACCESS + url, function (data) {

			var access_status = data.response["access"]
			var access_img_key = json_obj["access"][access_status]["image"];
			var access_message = json_obj["access"][access_status]["message"];

			var api_status = data.response["api"]
			var api_img_key = json_obj["wpapi"][api_status]["image"];
			var api_message = json_obj["wpapi"][api_status]["message"];

			$("#access div.ico").remove(".ball-pulse");
			$("#access div.ico").html(IMAGE[access_img_key]);
			$("#access div.btm").empty().html(access_message);

			$("#wpapi div.ico").remove(".ball-pulse");
			$("#wpapi div.ico").html(IMAGE[api_img_key]);
			$("#wpapi div.btm").empty().html(api_message);

	}).fail(function (data) {
		$("#access div.ico").remove(".ball-pulse");
		var img_key = json_obj["access"]["defualt"]["image"];
		var message = json_obj["access"]["defualt"]["message"]

		$("#wpapi div.ico").html(IMAGE[img_key]);
		$("#wpapi div.btm").empty().html(render(message, data));
	});
	
	if ( bruteForce == true ){

		$.getJSON(APIURL + BRUTEFORCE + url, function (data) {

			var countermeasure = data.response["countermeasure"]
			var BF_img_key = json_obj["BF"][countermeasure]["image"];
			var BF_message = json_obj["BF"][countermeasure]["message"];

			$("#bruteforce div.ico").remove(".ball-pulse");
			$("#bruteforce div.ico").html(IMAGE[BF_img_key]);
			$("#bruteforce div.btm").empty().html(BF_message);

		}).fail(function (data) {
			$("#bruteforce div.ico").remove(".ball-pulse");
			var BF_img_key = json_obj["BF"]["ture"]["image"];
			var BF_message = json_obj["BF"]["ture"]["message"];

			$("#bruteforce div.ico").html(IMAGE[img_key]);
			$("#bruteforce div.btm").empty().html(render(message, data));
		});

	}

}

$(".btn").click(function(){
	var url = $("input.url").val();
	var bruteForce = $("#bf").prop("checked");

	if ( !url ) {
		alert("URLを入力してください。");
		return;
	} else if ( !is_url(url) ) {
		alert("正しいURLを入力してください。");
		return;
	}
	
	$(".top_sec01").fadeOut(1000, function(){
		$(this).removeClass("top_sec01").addClass("result_sec01");
		$(this).load("/result .result_sec01 .container", function () {
			$("input.url").val(url);
		}).fadeIn(200);
	});

	$("#result").fadeOut(1500, function () {
		$(this).load("/result .result_sec02",
		function () {
			// result
			$('.tgl').click(function () {
				$(this).next().stop().slideToggle();
				$(this).toggleClass('active');
			});

			get_security_data(url, bruteForce);

				$("input.reload").click(function () {

					url = $("input.url").val();
					bruteForce = $("#bf").prop("checked");

					if (!url) {
						alert("URLを入力してください。");
						return;
					} else if (!is_url(url)) {
						alert("正しいURLを入力してください。");
						return;
					}
 
					if (bruteForce == true) {
						
						$("div.ico").empty();
						$("div.ico").html('<div class="ball-pulse"><div></div><div></div><div></div></div>');
					}else{
						var bf_html = $("#bruteforce div.ico").html();
						$("div.ico").empty();
						$("div.ico").html('<div class="ball-pulse"><div></div><div></div><div></div></div>');
						$("#bruteforce div.ico").html(bf_html);
					}

					get_security_data(url);

				});

		}).fadeIn(700);
	});
});
