<script type="text/javascript">
function AjaxRequest() {
  var req = false;
  var method = "GET";
  var nocache = false;
  var initialHandler = false;
  var callbackTarget = false;
  var callbackParams = [];
  this.loadXMLDoc = function(url, params) {
    try {
      req = new XMLHttpRequest();
      if (!initialHandler) initialHandler = processReqChange;
      req.onreadystatechange = initialHandler;
      if (nocache) {
        params += (params != '') ? '&' + (new Date()).getTime() : (new Date()).getTime();
      }
      if (method == "POST") {
        req.open("POST", url, true);
        req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        req.send(params);
      } else {
        if (params) url += "?" + params;
        req.open(method, url, true);
        req.send(null);
      }
      return true;
    } catch (e) {
      return false;
    }
  }
  this.setMethod = function(newmethod) {
    method = newmethod.toUpperCase();
  }
  this.nocache = function() {
    nocache = true;
  }
  this.setHandler = function(fn) {
    initialHandler = fn;
  }
  this.setCallback = function(fn) {
    callbackTarget = fn;
  }
  this.getResponse = function() {
    return req;
  }
  var getNodeValue = function(parent, tagName) {
    var node = parent.getElementsByTagName(tagName)[0];
    return (node && node.firstChild) ? node.firstChild.nodeValue : false;
  }
  var processReqChange = function() {
    if (req.readyState != 4 || req.status != 200) return;
    if (req.responseXML == null) {
      window.console.log(
        "Invalid XML response - please check the Ajax response data for invalid characters or formatting"
      );
    }
    var response = req.responseXML.documentElement;
    var commands = response.getElementsByTagName('command');
    for (var i = 0; i < commands.length; i++) {
      method = commands[i].getAttribute('method');
      switch (method) {
        case 'alert':
          var message = getNodeValue(commands[i], 'message');
          window.alert(message);
          break;
        case 'setvalue':
          var target = getNodeValue(commands[i], 'target');
          var value = getNodeValue(commands[i], 'value');
          if (target && value !== false) {
            document.getElementById(target).value = value;
          }
          break;
        case 'setdefault':
          var target = getNodeValue(commands[i], 'target');
          if (target) {
            document.getElementById(target).value = document.getElementById(target)
              .defaultValue;
          }
          break;
        case 'focus':
          var target = getNodeValue(commands[i], 'target');
          if (target) {
            document.getElementById(target).focus();
          }
          break;
        case 'setcontent':
          var target = getNodeValue(commands[i], 'target');
          var content = getNodeValue(commands[i], 'content');
          var append = getNodeValue(commands[i], 'append');
          if (target && (content !== false)) {
            var el = document.getElementById(target);
            if (append !== false) {
              var newcontent = document.createElement("div");
              newcontent.innerHTML = content;
              while (newcontent.firstChild) {
                el.appendChild(newcontent.firstChild);
              }
            } else {
              el.innerHTML = content;
            }
          }
          break;
        case 'setstyle':
          var target = getNodeValue(commands[i], 'target');
          var property = getNodeValue(commands[i], 'property');
          var value = getNodeValue(commands[i], 'value');
          if (target && property && (value !== false)) {
            document.getElementById(target).style[property] = value;
          }
          break;
        case 'setproperty':
          var target = getNodeValue(commands[i], 'target');
          var property = getNodeValue(commands[i], 'property');
          var value = getNodeValue(commands[i], 'value');
          if (value == "true") value = true;
          if (value == "false") value = false;
          if (target && document.getElementById(target)) {
            document.getElementById(target)[property] = value;
          }
          break;
        case 'callback':
          var idx = 1;
          var param = getNodeValue(commands[i], "param" + idx++);
          while (param) {
            callbackParams.push(param);
            param = getNodeValue(commands[i], "param" + idx++);
          }
          break;
        default:
          window.console.log("Unrecognised method '" + method +
            "' in processReqChange()");
      }
    }
    if (callbackTarget) {
      callbackTarget.apply(null, callbackParams);
    }
  }
}
</script>