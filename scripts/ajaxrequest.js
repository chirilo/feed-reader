<script type="text/javascript">

  var delay = 5000;

  function updateRSS(index)
  {
    if(encodeURIComponent) {
      var req = new AjaxRequest();
      req.loadXMLDoc('../updaterss.php', 'index=' + index);
      setTimeout('updateRSS(' + (++index) + ')', delay);
    }
  }

  setTimeout('updateRSS(1)', delay);

</script>