jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initEdit: function()
  {
    var tthis = this;
    var managerObj = tthis.obj.find('.manager');
    tthis.parent.parent.editor.baseHref = managerObj.attr('folder');
    tthis.para['editor-content'] = tthis.parent.parent.editor.replace('content');
    tthis.parent.lib.initAttEvents(managerObj, function(argContent){ tthis.parent.parent.editor.insertHTML(tthis.para['editor-content'], 'content', argContent); });
  },
  initCommon: function()
  {
    var tthis = this;
    tthis.obj = $('.console');
    var managerObj = tthis.obj.find('.manager');
    tthis.parent.para['current-main-path'] = tthis.parent.para['root'] + managerObj.attr('genre') + '/';
    tthis.parent.para['current-main-fileurl'] = tthis.para['fileurl'] = tthis.parent.para['current-main-path'] + managerObj.attr('filename');
  },
  ready: function()
  {
    var tthis = this;
    tthis.initCommon();
    var myModule = tthis.obj.find('.manager').attr('module');
    if (myModule == 'edit') tthis.initEdit();
  }
}.ready();