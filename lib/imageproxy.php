<?php
function KKDEV_data_uri($contents , $mime)
{
  $base64   = base64_encode($contents);
  return ('data:' . $mime . ';base64,' . $base64);
}


function KKDEV_inlineify($data,$mimetype){
$dataurl=KKDEV_data_uri($data,$mimetype);
return '<img alt="Prefetched Image" src="'.$dataurl.'" />';
}



function KKDEV_ptrf($match){
  static $KKDEV_working_item_rets="";
  if($match[0]=="###MAGICinitCall###"){
    $KKDEV_working_item_rets=$match[1];
    return "";
  }


  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $match[1]);
  curl_setopt($ch, CURLOPT_REFERER, $KKDEV_working_item_rets);
  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,10);
  curl_setopt($ch, CURLOPT_TIMEOUT, 400);
  $ch_rst = curl_exec($ch);
  $ch_rstd=$ch_rst;

  $minetype=curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  $parts = explode(";", $minetype);

  $minetype=$parts[0];

  $inlinef= KKDEV_inlineify($ch_rstd,$minetype);

  curl_close($ch);

  return $inlinef;




  //return $match[0];
}

function KKDEV_parse($item){
  $magic_callarg=array();
  $magic_callarg[]="###MAGICinitCall###";
  $magic_callarg[]=$item['url'];
  KKDEV_ptrf($magic_callarg);
  $res=preg_replace_callback('<img (?:src=\"((?:\w|\d|[!@#$%^&*()_+-=?])*)\"|alt=\S*\"|target=[^"]*\"|[ ])*(?:\/)?>',"KKDEV_ptrf",$item["content"]);
  return $res;
}

?>
