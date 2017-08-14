jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initList: function()
  {
    var tthis = this;
    tthis.parent.lib.initSearchBoxEvents(tthis.obj);
    if (tthis.obj.find('.CodeMirrorContent').length == 1)
    {
      tthis.para['codemirror-timeout'] = setTimeout(function(){
        tthis.para['codemirror'] = CodeMirror.fromTextArea(document.getElementById('codemirror'), {mode: 'htmlmixed', lineNumbers: true, lineWrapping: true, styleActiveLine: true, theme: 'monokai', extraKeys: { 'F11': function(cm) { cm.setOption('fullScreen', !cm.getOption('fullScreen')); }, 'Esc': function(cm) { if (cm.getOption('fullScreen')) cm.setOption('fullScreen', false); }}});
      }, 50);
    };
    tthis.obj.find('rightarea').find('select[name=\'node\']').on('change', function(){
      var thisObj = $(this);
      var url = tthis.para['fileurl'] + thisObj.attr('action') + '&node=' + encodeURIComponent(thisObj.val());
      tthis.parent.loadMainURL(url);
    });
    tthis.obj.find('span.nodeadd').click(function(){
      var thisObj = $(this);
      if (thisObj.attr('loading') != 'true')
      {
        thisObj.attr('loading', 'true');
        var url = tthis.para['fileurl'] + '?type=add&symbol=' + encodeURIComponent(thisObj.attr('symbol'));
        $.get(url, function(data){
          var dataObj = $(data);
          if (dataObj.find('result').attr('status') == '1')
          {
            var pageObj = tthis.parent.lib.popupPage(dataObj.find('result').text());
            pageObj.find('.tinyform').find('button.submit').attr('message', 'custom').on('message', function(){
              pageObj.find('span.tips').addClass('h').html($(this).attr('msg').split('|')[0]);
            }).attr('done', 'custom').on('done', function(){
              tthis.parent.loadMainURL(tthis.para['fileurl'] + '?type=list&symbol=' + encodeURIComponent(thisObj.attr('symbol')) + '&node=' + encodeURIComponent(pageObj.find('input[name=\'nodename\']').val()));
              pageObj.find('span.close').trigger('click');
            });
          };
          thisObj.attr('loading', 'false');
        });
      };
    });
    tthis.obj.find('button.nodeedit').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        if (tthis.para['codemirror']) tthis.obj.find('#codemirror').val(tthis.para['codemirror'].getValue());
        var formObj = tthis.obj.find('form.nodeedit');
        var url = tthis.para['fileurl'] + formObj.attr('action');
        $.post(url, formObj.serialize(), function(data){
          var dataObj = $(data);
          thisObj.removeClass('lock');
          tthis.parent.lib.popupAlert(dataObj.find('result').attr('message'), formObj.attr('text-ok'), function(){});
        });
      };
    });
    tthis.obj.find('button.nodedelete').click(function(){
      var thisObj = $(this);
      if (thisObj.attr('loading') != 'true')
      {
        var pageObj = tthis.parent.lib.popupConfirm(thisObj.attr('confirm_text'), thisObj.attr('confirm_b2'), thisObj.attr('confirm_b3'), function(argObj){
          var btnObj = argObj;
          var postData = 'symbol=' + encodeURIComponent(thisObj.attr('symbol')) + '&nodename=' + encodeURIComponent(thisObj.attr('nodename'));
          var url = tthis.para['fileurl'] + '?type=action&action=delete';
          thisObj.attr('loading', 'true');
          $.post(url, postData, function(data){
            var dataObj = $(data);
            thisObj.attr('loading', 'false');
            if (dataObj.find('result').attr('status') == '0')
            {
              btnObj.parent().find('button.b2').removeClass('lock');
              tthis.parent.lib.popupMiniAlert(dataObj.find('result').attr('message').split('|')[0]);
            }
            else if (dataObj.find('result').attr('status') == '1')
            {
              tthis.parent.loadMainURLRefresh();
              btnObj.parent().find('button.b3').click();
            };
          });
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
  }
}.ready();