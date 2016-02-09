<?php
  // define script parameters
  $BLOGURL    = "http://feeds.bbci.co.uk/news/rss.xml?edition=int";
  $TIMEFORMAT = "j F Y, g:ia";
  $CACHEFILE  = "/tmp/" . md5($BLOGURL);

  include "class.myrssparser.php";
  $rss_parser = new myRSSParser($CACHEFILE);

  // read feed data from cache file
  $feeddata = $rss_parser->getRawOutput();
  extract($feeddata['RSS']['CHANNEL'][0], EXTR_PREFIX_ALL, 'rss');

  $index = isset($_GET['index']) ? $_GET['index'] : 0;
  $index = (count($rss_ITEM) + $index) % count($rss_ITEM);

  $itemdata = $rss_ITEM[$index];

  // generate the HTML to be returned
  $retval = "<b><a href=\"{$itemdata['LINK']}\" target=\"_blank\">";
  $retval .= htmlspecialchars(stripslashes($itemdata['TITLE']));
  $retval .= "</a></b><br>\n";
  $retval .= htmlspecialchars(stripslashes($itemdata['DESCRIPTION'])) . "<br>\n";
  $retval .= "<i>" . date($TIMEFORMAT, strtotime($itemdata['PUBDATE'])) . "</i>";

  // create an XML file to be returned
  include "class.xmlresponse.php";
  $xml = new xmlResponse();
  $xml->start();

  if($retval) {
    $xml->command('setcontent',
      array('target' => 'rss_display'),
      array('content' => $retval)
    );
  }

  $xml->end();
?>