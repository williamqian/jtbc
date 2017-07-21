jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initList: function()
  {
    var tthis = this;
    var managerObj = tthis.obj.find('.manager');
    tthis.para['codemirror-timeout'] = setTimeout(function(){
      tthis.para['codemirror'] = CodeMirror.fromTextArea(document.getElementById('codemirror'), {mode: 'htmlmixed', lineNumbers: true, lineWrapping: true, styleActiveLine: true, theme: 'monokai', extraKeys: { 'F11': function(cm) { cm.setOption('fullScreen', !cm.getOption('fullScreen')); }, 'Esc': function(cm) { if (cm.getOption('fullScreen')) cm.setOption('fullScreen', false); }}});
    }, 50);
    managerObj.find('rightarea').find('select[name=\'node\']').on('change', function(){
      var thisObj = $(this);
      var url = tthis.para['fileurl'] + thisObj.attr('action') + '&node=' + encodeURIComponent(thisObj.val());
      tthis.parent.loadMainURL(url);
    });
    managerObj.find('.searchbox').find('input.search').on('click', function(){
      var thisObj = $(this);
      var keyword = thisObj.parent().find('input.keyword').val();
      var url = tthis.para['fileurl'] + thisObj.parent().attr('action') + '&symbol=' + encodeURIComponent(keyword);
      tthis.parent.loadMainURL(url);
    });
    managerObj.find('button.nodeedit').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        if (tthis.para['codemirror']) managerObj.find('#codemirror').val(tthis.para['codemirror'].getValue());
        var formObj = managerObj.find('form.nodeedit');
        var url = tthis.para['fileurl'] + formObj.attr('action');
        $.post(url, formObj.serialize(), function(data){
          var dataObj = $(data);
          thisObj.removeClass('lock');
          tthis.parent.lib.popupAlert(dataObj.find('result').attr('message'), formObj.attr('text-ok'), function(){});
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
  }
}.ready();