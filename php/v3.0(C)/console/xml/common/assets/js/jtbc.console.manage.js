jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  initList: function()
  {
    var tthis = this;
    tthis.parent.lib.initSearchBoxEvents(tthis.obj);
    tthis.para['codemirror-timeout'] = setTimeout(function(){
      tthis.para['codemirror'] = CodeMirror.fromTextArea(document.getElementById('codemirror'), {mode: 'htmlmixed', lineNumbers: true, lineWrapping: true, styleActiveLine: true, theme: 'monokai', extraKeys: { 'F11': function(cm) { cm.setOption('fullScreen', !cm.getOption('fullScreen')); }, 'Esc': function(cm) { if (cm.getOption('fullScreen')) cm.setOption('fullScreen', false); }}});
    }, 50);
    tthis.obj.find('rightarea').find('select[name=\'node\']').on('change', function(){
      var thisObj = $(this);
      var url = tthis.para['fileurl'] + thisObj.attr('action') + '&node=' + encodeURIComponent(thisObj.val());
      tthis.parent.loadMainURL(url);
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