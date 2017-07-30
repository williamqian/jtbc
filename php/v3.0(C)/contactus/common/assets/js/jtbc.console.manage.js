jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initEdit: function()
  {
    var tthis = this;
    tthis.parent.parent.editor.baseHref = tthis.obj.attr('folder');
    tthis.para['editor-content'] = tthis.parent.parent.editor.replace('content');
    tthis.parent.lib.initAttEvents(tthis.obj, function(argContent){ tthis.parent.parent.editor.insertHTML(tthis.para['editor-content'], 'content', argContent); });
    tthis.obj.find('.form_button').find('button.submit').on('before', function(){
      tthis.obj.find('textarea[name=\'content\']').val(tthis.parent.parent.editor.getHTML(tthis.para['editor-content'], 'content'));
    });
  },
  initCommon: function()
  {
    var tthis = this;
    tthis.obj = tthis.parent.obj.find('.manager');
    tthis.parent.para['current-main-path'] = tthis.parent.para['root'] + tthis.obj.attr('genre') + '/';
    tthis.parent.para['current-main-fileurl'] = tthis.para['fileurl'] = tthis.parent.para['current-main-path'] + tthis.obj.attr('filename');
  },
  ready: function()
  {
    var tthis = this;
    tthis.initCommon();
    var myModule = tthis.obj.attr('module');
    if (myModule == 'edit') tthis.initEdit();
  }
}.ready();
