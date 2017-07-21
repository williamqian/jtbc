jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initList: function()
  {
    var tthis = this;
    var managerObj = tthis.obj.find('.manager');
    managerObj.find('input.id').click(function(){ tthis.parent.lib.highlightLine(this); });
    managerObj.find('input.idall').click(function(){ tthis.parent.lib.highlightLineAll(this); });
    managerObj.find('.pagi').find('a.go').click(function(){ tthis.parent.lib.loadPagiGoURL(this, managerObj); });
    managerObj.find('span.mainlink').each(function(){
      var thisObj = $(this);
      var editObj = thisObj.parent().parent().find('icon.edit');
      if (!editObj.hasClass('show-0'))
      {
        thisObj.addClass('hand').on('click', function(){ editObj.find('a.link').click(); });
      };
    });
    managerObj.find('icon.delete').click(function(){
      var thisObj = $(this);
      tthis.parent.lib.popupConfirm(thisObj.attr('confirm_text'), thisObj.attr('confirm_b2'), thisObj.attr('confirm_b3'), function(argObj){
        var myObj = argObj;
        var url = tthis.para['fileurl'] + '?type=action&action=delete&id=' + encodeURIComponent(thisObj.attr('rsid'));
        $.get(url, function(data){ tthis.parent.loadMainURLRefresh(); myObj.parent().find('button.b3').click(); });
      });
    });
    managerObj.find('div.batch').find('span.ok').click(function(){
      var thisObj = $(this);
      var batch = thisObj.parent().find('select.batch').val();
      if (batch != 'null')
      {
        tthis.parent.lib.popupConfirm(thisObj.attr('confirm_text'), thisObj.attr('confirm_b2'), thisObj.attr('confirm_b3'), function(argObj){
          var myObj = argObj;
          var ids = tthis.parent.lib.getCheckBoxValue(managerObj.find('input.id:checked'));
          var url = tthis.para['fileurl'] + '?type=action&action=batch';
          url += '&batch=' + encodeURIComponent(batch) + '&ids=' + encodeURIComponent(ids);
          $.get(url, function(data){ tthis.parent.loadMainURLRefresh(); myObj.parent().find('button.b3').click(); });
        });
      };
    });
  },
  initAdd: function()
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
          else if (dataObj.find('result').attr('status') == '1') managerObj.find('toplink').find('a.link').first().click();
        });
      };
    });
  },
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
    if (myModule == 'list') tthis.initList();
    else if (myModule == 'add') tthis.initAdd();
    else if (myModule == 'edit') tthis.initEdit();
  }
}.ready();