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
    managerObj.find('rightarea').find('select[name=\'genre\']').on('change', function(){
      var thisObj = $(this);
      var url = tthis.para['fileurl'] + '?type=list&genre=' + encodeURIComponent(thisObj.val());
      tthis.parent.loadMainURL(url);
    });
    managerObj.find('button.add').click(function(){
      var thisObj = $(this);
      if (thisObj.attr('loading') != 'true')
      {
        thisObj.attr('loading', 'true');
        var url = tthis.para['fileurl'] + '?type=add';
        $.get(url, function(data){
          var dataObj = $(data);
          if (dataObj.find('result').attr('status') == '1')
          {
            var pageObj = tthis.parent.lib.popupPage(dataObj.find('result').text());
            var tinyformObj = pageObj.find('.tinyform');
            tinyformObj.find('button.b2').click(function(){
              var thisObj = $(this);
              if (!thisObj.hasClass('lock'))
              {
                thisObj.addClass('lock');
                var formObj = thisObj.parent().parent();
                formObj.find('input[name=\'genre\']').val(managerObj.attr('current-genre'));
                formObj.find('input[name=\'fid\']').val(managerObj.attr('current-fid'));
                var url = tthis.para['fileurl'] + formObj.attr('action');
                $.post(url, formObj.serialize(), function(data){
                  var dataObj = $(data);
                  thisObj.removeClass('lock');
                  formObj.find('span.tips').addClass('h').html(dataObj.find('result').attr('message').split('|')[0]);
                  if (dataObj.find('result').attr('status') == '1')
                  {
                    tthis.parent.loadMainURLRefresh();
                    tinyformObj.parent().parent().find('span.close').trigger('click');
                  };
                });
              };
            });
          };
          thisObj.attr('loading', 'false');
        });
      };
    });
    managerObj.find('icon.edit').click(function(){
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
            var tinyformObj = pageObj.find('.tinyform');
            tinyformObj.find('button.b2').click(function(){
              var thisObj = $(this);
              if (!thisObj.hasClass('lock'))
              {
                thisObj.addClass('lock');
                var formObj = thisObj.parent().parent();
                var url = tthis.para['fileurl'] + formObj.attr('action');
                $.post(url, formObj.serialize(), function(data){
                  var dataObj = $(data);
                  thisObj.removeClass('lock');
                  formObj.find('span.tips').addClass('h').html(dataObj.find('result').attr('message').split('|')[0]);
                  if (dataObj.find('result').attr('status') == '1')
                  {
                    tthis.parent.loadMainURLRefresh();
                    tinyformObj.parent().parent().find('span.close').trigger('click');
                  };
                });
              };
            });
          };
          thisObj.attr('loading', 'false');
        });
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
    tthis.parent.lib.dragSort(managerObj.find('.tableL tbody'), 'tr', 'td.sort', function(){}, function(){
      var ids = tthis.parent.lib.getCheckBoxValue(managerObj.find('input.id'));
      var url = tthis.para['fileurl'] + '?type=action&action=sort&ids=' + encodeURIComponent(ids);
      $.get(url, function(data){});
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
  }
}.ready();