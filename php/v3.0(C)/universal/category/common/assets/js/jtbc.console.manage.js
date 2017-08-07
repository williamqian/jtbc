jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initList: function()
  {
    var tthis = this;
    tthis.obj.find('rightarea').find('select[name=\'genre\']').on('change', function(){
      var thisObj = $(this);
      var url = tthis.para['fileurl'] + '?type=list&genre=' + encodeURIComponent(thisObj.val());
      tthis.parent.loadMainURL(url);
    });
    tthis.obj.find('button.add').click(function(){
      var thisObj = $(this);
      if (thisObj.attr('loading') != 'true')
      {
        thisObj.attr('loading', 'true');
        var url = tthis.para['fileurl'] + '?type=add&genre=' + encodeURIComponent(tthis.obj.attr('current-genre')) + '&fid=' + encodeURIComponent(tthis.obj.attr('current-fid'));
        $.get(url, function(data){
          var dataObj = $(data);
          if (dataObj.find('result').attr('status') == '1')
          {
            var pageObj = tthis.parent.lib.popupPage(dataObj.find('result').text());
            tthis.parent.lib.initUpFileEvents(pageObj);
            pageObj.find('.tinyform').find('button.submit').attr('message', 'custom').on('message', function(){
              pageObj.find('span.tips').addClass('h').html($(this).attr('msg').split('|')[0]);
            }).attr('done', 'custom').on('done', function(){
              pageObj.find('span.tips').addClass('h').html($(this).attr('msg').split('|')[0]);
              tthis.parent.loadMainURLRefresh();
              pageObj.find('span.close').trigger('click');
            });
          };
          thisObj.attr('loading', 'false');
        });
      };
    });
    tthis.obj.find('icon.edit').click(function(){
      var thisObj = $(this);
      if (thisObj.attr('loading') != 'true')
      {
        thisObj.attr('loading', 'true');
        var url = tthis.para['fileurl'] + '?type=edit&id=' + thisObj.attr('rsid');
        $.get(url, function(data){
          var dataObj = $(data);
          if (dataObj.find('result').attr('status') == '1')
          {
            var pageObj = tthis.parent.lib.popupPage(dataObj.find('result').text());
            tthis.parent.lib.initUpFileEvents(pageObj);
            pageObj.find('.tinyform').find('button.submit').attr('message', 'custom').on('message', function(){
              pageObj.find('span.tips').addClass('h').html($(this).attr('msg').split('|')[0]);
            }).attr('done', 'custom').on('done', function(){
              pageObj.find('span.tips').addClass('h').html($(this).attr('msg').split('|')[0]);
              tthis.parent.loadMainURLRefresh();
              pageObj.find('span.close').trigger('click');
            });
          };
          thisObj.attr('loading', 'false');
        });
      };
    });
    tthis.parent.lib.initBatchSwitchEvents(tthis.obj);
    tthis.parent.lib.dragSort(tthis.obj.find('.tableL tbody'), 'tr', 'td.sort', function(){}, function(){
      var ids = tthis.parent.lib.getCheckBoxValue(tthis.obj.find('input.id'));
      var url = tthis.para['fileurl'] + '?type=action&action=sort&ids=' + encodeURIComponent(ids);
      $.get(url, function(data){});
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
  }
}.ready();