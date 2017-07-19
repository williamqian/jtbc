var jtbc = window.jtbc || {};
jtbc.editor = {
  baseHref: null,
  replace: function(argStr)
  {
    var str = argStr;
    return CKEDITOR.replace(str);
  },
  getHTML: function(argObj)
  {
    var myObj = argObj;
    return myObj.getData();
  },
  insertHTML: function(argObj, argName, argContent)
  {
    var myObj = argObj;
    var myContent = argContent;
    return myObj.insertHtml(myContent);
  }
};