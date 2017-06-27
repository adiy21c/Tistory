var el_list = [];
var el_list2 = [];
var timer;
$(document).ready(function(){
	makeMenu();

	$(document).on("click", "#submitdiv #title", function(){
		event_fire("subject");
	});
	$(document).on("click", "#submitdiv textarea", function(){
		event_fire("content");
	});
	$(document).on("click", "#submitdiv #senddata", function(){
		senddata('write');
	});
});

function makeMenu(){
	//chrome.storage.sync.set({ "list": "" }, function () { console.log("reset"); });
	var dname = document.location.origin.toString();
	var elname = "";
	var elname2 = "";

	$("body").append("<div id='submitdiv'><span id='hidesubmitdiv'>close</span><input type='text' style='width:198px; font-size:1em; padding:0; margin:0;'  id='titlename' /><input type='text' id='title' style='width:198px; font-size:1em; padding:0; margin:0;' value='"+document.title+"'><br><input type='text' id='elname' value='' style='width:198px; font-size:1em; padding:0; margin:0;' /></br><textarea style='width:196px;height:300px;margin:0; padding:0; font-size:1em;'></textarea><br><span id='senddata' style='width:85px;display:inline-block;margin:0 auto;border:1px solid black;padding:5px;text-align:center;font-size:1.4em;'>post</span></div>");
	$("#submitdiv").css("width", "200px").css("background-color", "#fff").css("position", "fixed").css("left", "0").css("bottom", "0").css("border", "1px solid #000").css("padding","10px").css("z-index", "9999");

	$("body").on("click", "#hidesubmitdiv", function(){
		$("#submitdiv").hide();
	});

	chrome.storage.sync.get("list", function( items ){
		console.log( items );
		if( items.list.length > 0 ){
			el_list = JSON.parse(items.list);
			var getname = "";

			el_list.forEach( function f(value, index) {
				var temp = JSON.parse(value);
				
				if( Object.keys(temp).toString() == dname ){
					elname = Object.values(temp).toString();
				}
			})
		}
		
		if( elname != "" ){
			$("#elname").val(elname);
			$("#submitdiv textarea").val( $(elname).html() );
		}
	});

	chrome.storage.sync.get("tlist", function( items ) {
		if( items.tlist.length > 0 ){
			el_list2 = JSON.parse(items.tlist);
			var getname = "";

			el_list2.forEach( function f(value, index) {
				var temp = JSON.parse(value);
				
				if( Object.keys(temp).toString() == dname ){
					elname2 = Object.values(temp).toString();
				}
			})
		}
		
		if( elname2 != "" ){
			$("#titlename").val(elname2);
			$("#submitdiv #title").val( $(elname2).text() );
		}
	});
}

function makeDiv(temp){
	$(temp).append("<div id='poverlay' style='position:absolute;left:0;top:0;background-color:#0000c8;opacity:0.4;width:100%;height:100%;z-index:10000;'></div>");
}

function event_fire(type){
	if( type == "subject" ){
		$("#submitdiv #title").val( "제목이 될 문장을 선택하세요." );
	} else {
		$("#submitdiv textarea").val( "본문으로 들어갈 부분을 선택하세요." );
	}

	$("body").on("mousemove", function(){
		$("#poverlay").detach();
		clearTimeout(timer);
		var depth = $(":hover").length-1;
		var temp = $(":hover")[$(":hover").length-1];

		timer = setTimeout( function(){makeDiv(temp)}, 150 );
	});

	$(document).one("click", function(e){
		e.preventDefault();
		var temp = $(":hover")[$(":hover").length-2];
		if( type == "subject" ){
			$("#submitdiv #title").val( $(temp).text() );
		} else {
			$(temp).find("#poverlay").detach();
			$("#submitdiv textarea").val( $(temp).html() );
		}
		$("body").off("mousemove");
		$("#poverlay").detach();
	});
}

function senddata(){
	if( $("#submitdiv #elname").val() != "" ){
		$("#submitdiv textarea").val( $($("#submitdiv #elname").val()).html() );
		if($("#submitdiv textarea").val() != "" ){
			var dname = document.location.origin.toString();//.replace(/\./gi, "");
			var flag = true;
			el_list.forEach(function f(value, index){
				if( value.indexOf( dname ) > -1) {
					flag = false;
				}
			});

			if( flag == true ) { 
				el_list.push("{ \""+dname+"\": \"" +$("#submitdiv #elname").val()+"\"}");
			} else {
				el_list.splice( el_list.indexOf(dname) );
				el_list.push("{ \""+dname+"\": \"" +$("#submitdiv #elname").val()+"\"}");
			}
			
			var jsonstring = JSON.stringify(el_list);
			chrome.storage.sync.set({ "list": jsonstring }, function () { console.log("save elname"); });
		}
	}

	if( $("#submitdiv #titlename").val() != "" ){
		$("#submitdiv #title").val( $($("#submitdiv #titlename").val()).text() );
		if($("#submitdiv #title").val() != "" ){
			var dname = document.location.origin.toString();//.replace(/\./gi, "");
			var flag = true;
			el_list2.forEach(function f(value, index){
				if( value.indexOf( dname ) > -1) {
					flag = false;
				}
			});

			if( flag == true ) { 
				el_list2.push("{ \""+dname+"\": \"" +$("#submitdiv #titlename").val()+"\"}");
			} else {
				el_list2.splice( el_list2.indexOf(dname) );
				el_list2.push("{ \""+dname+"\": \"" +$("#submitdiv #titlename").val()+"\"}");
			}

			jsonstring = JSON.stringify(el_list2);
			chrome.storage.sync.set({ "tlist": jsonstring }, function () { console.log("save elname2"); });
		}
	}
	
	$("body").append("<div id='screen'></div>");
	$("#screen").css("width", "100%").css("height", "100%").css("left", "0").css("top", "0").css("background-color", "#000").css("opacity", "0.65").css("position", "fixed").css("z-index", "11111");

	var ajaxurl = "https://tistory-api-dbjobs.c9users.io/example/sample2.php";

	$.ajax({
		url: ajaxurl,
		type:"post",
		data: {
			title: $("#submitdiv #title").val(),
			content: $("#submitdiv textarea").val(),
			referer: location.href
		},
		success: function(data){
			if( data.status == "ok" )
				alert("포스팅 완료.");
			else
				alert("error");
			$("#screen").detach();
		}
	});
}