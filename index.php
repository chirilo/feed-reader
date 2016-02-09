<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title>Feed Reader | Welcome</title>
	<link href="css/foundation.css" rel="stylesheet">
	<script src="js/vendor/modernizr.js">
	</script>
</head>
<body>
	<div class="row">
		<div class="large-12 columns">
			<div class="panel">
				<h1>Feed Template</h1>
			</div>
		</div>
	
	<?php
		// define script parameters
	  //$BLOGURL    = "http://sxp.microsoft.com/feeds/3.0/msdntn?tags=msit";
	$BLOGURL    = "http://feeds.bbci.co.uk/news/rss.xml?edition=int";
	  $NUMITEMS   = 2;
	  $TIMEFORMAT = "j F Y, g:ia";
	  $CACHEFILE  = "/tmp/" . md5($BLOGURL);
	  $CACHETIME  = 4; // hours

	  // download the feed iff a cached version is missing or too old
	  if(!file_exists($CACHEFILE) || ((time() - filemtime($CACHEFILE)) > 3600 * $CACHETIME)) {
	    if($feed_contents = http_get_contents($BLOGURL)) {
	      // write feed contents to cache file
	      $fp = fopen($CACHEFILE, 'w');
	      fwrite($fp, $feed_contents);
	      fclose($fp);
	    }
	  }

	  include "class.myrssparser.php";
	  $rss_parser = new myRSSParser($CACHEFILE);

	  // read feed data from cache file
	  $feeddata = $rss_parser->getRawOutput();
	  extract($feeddata['RSS']['CHANNEL'][0], EXTR_PREFIX_ALL, 'rss');

	  // display leading image
	  if(isset($rss_IMAGE[0]) && $rss_IMAGE[0]) {
	    extract($rss_IMAGE[0], EXTR_PREFIX_ALL, 'img');
	    echo "<p><a title=\"{$img_TITLE}\" href=\"{$img_LINK}\"><img src=\"{$img_URL}\" alt=\"\"></a></p>\n";
	  }

	  // display feed title
	  echo "<h4><a title=\"",htmlspecialchars($rss_DESCRIPTION),"\" href=\"{$rss_LINK}\" target=\"_blank\">";
	  echo htmlspecialchars($rss_TITLE);
	  echo "</a></h4>\n";

	  // display feed items
	  $count = 0;
	  foreach($rss_ITEM as $itemdata) {
	    echo "<p><b><a href=\"{$itemdata['LINK']}\" target=\"_blank\">";
	    echo htmlspecialchars(stripslashes($itemdata['TITLE']));
	    echo "</a></b><br>\n";
	    echo htmlspecialchars(stripslashes($itemdata['DESCRIPTION'])),"<br>\n";
	    echo "<i>",date($TIMEFORMAT, strtotime($itemdata['PUBDATE'])),"</i></p>\n\n";
	    if(++$count >= $NUMITEMS) break;
	  }

	  // display copyright information
	  echo "<p><small>&copy; {",htmlspecialchars($rss_COPYRIGHT),"}</small></p>\n";
	?>
	</div>
	<script src="js/vendor/jquery.js"></script>
    <script src="js/foundation.min.js"></script>
    <script>
      $(document).foundation();
    </script>
    <script type="text/javascript" src="ajaxrequest.js"></script>
    <script type="text/javascript">

	  var delay = 5000;

	  function updateRSS(index)
	  {
	    if(encodeURIComponent) {
	      var req = new AjaxRequest();
	      req.loadXMLDoc('updaterss.php', 'index=' + index);
	      setTimeout('updateRSS(' + (++index) + ')', delay);
	    }
	  }

	  setTimeout('updateRSS(1)', delay);

	</script>
	
</body>
</html>