chrome.tabs.onUpdated.addListener(proc);
 
function proc(tabId, changeInfo, tab){
    var temparr;
    var srl;
    var makelink;
    var apiurl = "https://tistory-api-dbjobs.c9users.io/example/sample.php";
    
    if( tab.url.indexOf("dogdrip.net") > -1 ){
        if( tab.url.split("?").length > 1 )
            srl = getQueryVariable(tab.url, "document_srl");
        else
            srl = tab.url.split("/")[3];
        makelink = "$('html').append(\"<a href='"+apiurl+"?srl="+srl+"' target='_blank' class='movemove'>blog</a>\"); $('.movemove').css('position','fixed').css('top','50%').css('left','50px').css('font-size','3em').css('color', '#000').css('z-index', '9999');";
    }

    if( changeInfo.status == "complete" ) {
		chrome.tabs.executeScript(tab.id, {file:"jquery.min.js"}, function(rr){
			chrome.tabs.executeScript(tab.id, {file: "func.js"}, function(r2){
				chrome.tabs.executeScript(tab.id, {code: makelink });
			});
		});
	}
}
 
function getQueryVariable(url, variable) {
    var query = url.split("?")[1];
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
            return pair[1];
        }
    } 
}