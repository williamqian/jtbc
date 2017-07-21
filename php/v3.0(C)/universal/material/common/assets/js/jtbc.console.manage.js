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
    managerObj.find('span.mainlink').click(function(){
      var thisObj = $(this);
      var fileURL = thisObj.attr('fileurl');
      if (!(fileURL.substring(0, 1) == '/' || fileURL.substring(0, 5) == 'http:' || fileURL.substring(0, 6) == 'https:')) fileURL = managerObj.attr('folder') + fileURL;
      tthis.parent.lib.previewAtt(thisObj.attr('filetype'), thisObj.attr('filename'), fileURL, managerObj.attr('text-preview-link'), '0');
    });
    managerObj.find('button.add').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock')) managerObj.find('.upload').trigger('click');
    });
    managerObj.find('.upload').on('change', function(){
      var thisObj = $(this);
      var url = tthis.para['fileurl'] + '?type=action&action=add';
      if (thisObj.attr('uploading') != 'true')
      {
        thisObj.attr('uploading', 'true');
        managerObj.find('button.add').addClass('lock');
        tthis.parent.lib.fileUp(this, managerObj.find('.fileup'), url, function(){ if (managerObj.find('.fileup').find('.item.error').length == 0) tthis.parent.loadMainURLRefresh(); });
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
  initEdit: function()
  {
    var tthis = this;
    var managerObj = tthis.obj.find('.manager');
    managerObj.find('button.replace').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock')) managerObj.find('.upload').trigger('click');
    });
    managerObj.find('.upload').on('change', function(){
      var thisObj = $(this);
      var btnObj = managerObj.find('button.replace');
      var url = tthis.para['fileurl'] + '?type=action&action=replace&id=' + thisObj.attr('rsid');
      if (thisObj.attr('uploading') != 'true')
      {
        thisObj.attr('uploading', 'true');
        btnObj.addClass('lock').html(btnObj.attr('uploading'));
        tthis.parent.lib.fileUpSingle(this, url, function(result){
          if (result.find('result').attr('status') == '1') tthis.parent.loadMainURLRefresh();
          else
          {
            btnObj.removeClass('lock').html(btnObj.attr('text'));
            tthis.parent.lib.popupAlert(result.find('result').attr('message'), thisObj.attr('text-ok'), function(){});
          };
        });
      };
    });
    managerObj.find('.form_button').find('button.b2').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        var formObj = thisObj.parent().parent();
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
    else if (myModule == 'edit') tthis.initEdit();
  }
}.ready();