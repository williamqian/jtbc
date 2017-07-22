jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initList: function()
  {
    var tthis = this;
    tthis.obj.find('span.mainlink').click(function(){
      var thisObj = $(this);
      var fileURL = thisObj.attr('fileurl');
      if (!tthis.parent.parent.isAbsoluteURL(fileURL)) fileURL = tthis.obj.attr('folder') + fileURL;
      tthis.parent.lib.previewAtt(thisObj.attr('filetype'), thisObj.attr('filename'), fileURL, tthis.obj.attr('text-preview-link'), '0');
    });
    tthis.obj.find('button.add').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock')) tthis.obj.find('.upload').trigger('click');
    });
    tthis.obj.find('.upload').on('change', function(){
      var thisObj = $(this);
      var url = tthis.para['fileurl'] + '?type=action&action=add';
      if (thisObj.attr('uploading') != 'true')
      {
        thisObj.attr('uploading', 'true');
        tthis.obj.find('button.add').addClass('lock');
        tthis.parent.lib.fileUp(this, tthis.obj.find('.fileup'), url, function(){ if (tthis.obj.find('.fileup').find('.item.error').length == 0) tthis.parent.loadMainURLRefresh(); });
      };
    });
    tthis.parent.lib.initBatchSwitchEvents(tthis.obj);
  },
  initEdit: function()
  {
    var tthis = this;
    tthis.obj.find('button.replace').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock')) tthis.obj.find('.upload').trigger('click');
    });
    tthis.obj.find('.upload').on('change', function(){
      var thisObj = $(this);
      var btnObj = tthis.obj.find('button.replace');
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
    if (myModule == 'list') tthis.initList();
    else if (myModule == 'edit') tthis.initEdit();
  }
}.ready();
