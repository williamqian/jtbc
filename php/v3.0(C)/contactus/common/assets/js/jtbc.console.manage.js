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
    managerObj.find('.form_button').find('button.b2').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        var formObj = thisObj.parent().parent();
        formObj.find('textarea[name=\'content\']').val(tthis.parent.parent.editor.getHTML(tthis.para['editor-content'], 'content'));
        var url = tthis.para['fileurl'] + formObj.attr('action');
        $.post(url, formObj.serialize(), function(data){
          var dataObj = $(data);
          thisObj.removeClass('lock');
          if (dataObj.find('result').attr('status') == '0')
          {
            var msgObj = managerObj.find('.form_tips').html('').append('<ul></ul>').find('ul');
            var message = dataObj.find('result').attr('message').split('|');
            for (var i in message) msgObj.append('<li>' + message[i] + '</li>');
          }
          else if (dataObj.find('result').attr('status') == '1') managerObj.find('.form_tips').html('<em>' + dataObj.find('result').attr('message') + '</em>');
        });
      };
    });
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