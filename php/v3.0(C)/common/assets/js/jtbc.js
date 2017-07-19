var jtbc = {
  htmlEncode: function(argStrers)
  {
    var strers = argStrers;
    strers = strers.replace(/(\&)/g, '&amp;');
    strers = strers.replace(/(\>)/g, '&gt;');
    strers = strers.replace(/(\<)/g, '&lt;');
    strers = strers.replace(/(\")/g, '&quot;');
    return strers;
  },
  isAbsoluteURL: function(argURL)
  {
    var bool = false;
    var url = argURL;
    if (url.substring(0, 1) == '/' || url.substring(0, 5) == 'http:' || url.substring(0, 6) == 'https:') bool = true;
    return bool;
  }
};