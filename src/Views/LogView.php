<?php

namespace DumpScan\Views;

use HtmlObject\Element;

class LogView {

	public function getHtml() {
		return Element::create( 'html',
			Element::create( 'head',
				Element::create( 'title', 'Dump Scanning Tool' )
			)
			.
			Element::create( 'body',
				$this->getBody()
			)
		);
	}

	public function getBody() {
		$html = '';
		$html .= Element::create( 'pre', Element::create( 'div', 'Loading...', array( 'id' => 'feed' ) ) );
		$html .= Element::create( 'script', $this->getScript(), array( 'type' => 'text/javascript' ) );
		return $html;
	}

	public function getScript() {
		return '
var refreshtime=10;
function tc()
{
asyncAjax("GET","index.php?action=log&imascript",display,{});
setTimeout(tc,refreshtime);
}
function display(xhr,cdat)
{
 if(xhr.readyState==4 && xhr.status==200)
 {
   document.getElementById("feed").innerHTML=xhr.responseText;
 }
}
function asyncAjax(method,url,callback,callbackData)
{
    var xmlhttp=new XMLHttpRequest();
    //xmlhttp.cdat=callbackData;
    var cb=callback;
    callback=function()
    {
        var xhr=xmlhttp;
        //xhr.cdat=callbackData;
        var cdat2=callbackData;
        cb(xhr,cdat2);
        return;
    }
    xmlhttp.open(method,url,true);
    xmlhttp.onreadystatechange=callback;
    if(method=="POST"){
            xmlhttp.setRequestHeader(\'Content-Type\',\'application/x-www-form-urlencoded\');
            xmlhttp.send(qs);
    }
    else
    {
            xmlhttp.send(null);
    }
}
tc();
';
	}

} 