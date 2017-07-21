jtbc.console.manage = {
  obj: null,
  parent: jtbc.console,
  para: [],
  bindSelectPopedomEvents: function()
  {
    var tthis = this;
    var managerObj = tthis.obj.find('.manager');
    var inputPopedomObj = managerObj.find('input[name=\'popedom\']');
    inputPopedomObj.on('update', function(){
      var popedomValue = '';
      var thisObj = $(this);
      managerObj.find('.popedom').find('input.genre').each(function(){
        var that = this;
        var thisObj = $(this);
        if (that.checked)
        {
          popedomValue += thisObj.val();
          popedomValue += ':';
          thisObj.parent().nextAll().each(function(){
            if ($(this).is('label')) $(this).find('input.genre_popedom').each(function(){ if (this.checked) popedomValue += this.value + ','; });
          });
          popedomValue += ':';
          thisObj.parent().parent().find('input.genre_category').each(function(){ popedomValue += $(this).val() + ','; });
          popedomValue += '|';
        };
      });
      thisObj.val(popedomValue);
    });
    managerObj.find('.popedom').find('input.genre').on('elder', function(){
      var that = this;
      var thisObj = $(this);
      if (that.checked)
      {
        if (thisObj.val().indexOf('/') != -1)
        {
          var genreArray = thisObj.val().split('/');
          for (var i = 0; i < genreArray.length; i ++)
          {
            var genreText = genreArray[i];
            if (i > 0)
            {
              for (var k = (i - 1); k >= 0; k --) genreText = genreArray[k] + '/' + genreText;
            };
            if (genreText != thisObj.val()) managerObj.find('.popedom').find('input[value=\'' + genreText + '\']').each(function(){ this.checked = true; });
          };
        };
      };
    });
    managerObj.find('.popedom').find('input.genre').click(function(){
      var that = this;
      var thisObj = $(this);
      if (that.checked)
      {
        thisObj.trigger('elder');
        thisObj.parent().parent().find('input.genre_popedom').each(function(){ this.checked = true; });
        thisObj.parent().parent().find('ul').find('input[type=\'checkbox\']').each(function(){ this.checked = true; });
      }
      else
      {
        thisObj.parent().parent().find('input.genre_popedom').each(function(){ this.checked = false; });
        thisObj.parent().parent().find('ul').find('input[type=\'checkbox\']').each(function(){ this.checked = false; });
      };
      inputPopedomObj.trigger('update');
    });
    managerObj.find('.popedom').find('input.genre_popedom').click(function(){
      var that = this;
      var thisObj = $(this);
      if (that.checked)
      {
        thisObj.parent().parent().find('input.genre').each(function(){ this.checked = true; $(this).trigger('elder'); });
      };
      inputPopedomObj.trigger('update');
    });
    managerObj.find('span.category').click(function(){
      var thisObj = $(this);
      var genre = thisObj.parent().find('input.genre').val();
      var genreCategory = thisObj.find('input.genre_category').val();
      var lang = tthis.parent.lib.getCheckBoxValue(managerObj.find('input[name=\'lang-select\']:checked'));
      if (thisObj.attr('loading') != 'true')
      {
        thisObj.attr('loading', 'true');
        var url = tthis.para['fileurl'] + '?type=category&genre=' + encodeURIComponent(genre);
        $.get(url, function(data){
          var dataObj = $(data);
          if (dataObj.find('result').attr('status') == '1')
          {
            thisObj.attr('loading', 'false');
            var pageObj = tthis.parent.lib.popupPage(dataObj.find('result').text());
            pageObj.find('.tab').find('.option label').each(function(){ if ($.inArray($(this).attr('val'), lang.split(',')) == -1) $(this).addClass('hide'); });
            pageObj.find('.tab').find('.option label:not(.hide)').eq(0).trigger('click');
            pageObj.find('input[name=\'category\']').each(function(){ if ($.inArray($(this).val(), genreCategory.split(',')) != -1) this.checked = true; });
            pageObj.find('input[name=\'category\']').on('click', function(){
              var that = this;
              var thisObj = $(this);
              if (!that.checked)
              {
                thisObj.parent().parent().find('input[name=\'category\']').each(function(){ this.checked = false; });
              }
              else
              {
                thisObj.parent().parent().find('input[name=\'category\']').each(function(){ this.checked = true; });
                pageObj.find('.popedom_category').find('li').has(thisObj).find('input[name=\'category\']').eq(0).each(function(){ this.checked = true; });
                pageObj.find('.popedom_category').find('dd').has(thisObj).find('input[name=\'category\']').eq(0).each(function(){ this.checked = true; });
              };
            });
            pageObj.find('button.b2').on('click', function(){
              pageObj.find('span.close').trigger('click');
              managerObj.find('.genre_category').val(tthis.parent.lib.getCheckBoxValue(pageObj.find('input[name=\'category\']:checked')));
              inputPopedomObj.trigger('update');
            });
          };
        });
      };
    });
    inputPopedomObj.trigger('update');
  },
  bindSelectLangEvents: function()
  {
    var tthis = this;
    var managerObj = tthis.obj.find('.manager');
    managerObj.find('input[name=\'lang-select\']').on('click', function(){
      var that = this;
      var thisObj = $(this);
      if (!that.checked)
      {
        if (managerObj.find('input[name=\'lang-select\']:checked').length == 0)
        {
          that.checked = true;
          tthis.parent.lib.popupAlert(managerObj.attr('text-lang-1'), managerObj.attr('text-lang-ok'), function(){});
        };
      };
    });
    managerObj.find('input[name=\'lang\']').on('update', function(){
      managerObj.find('input[name=\'lang\']').val(tthis.parent.lib.getCheckBoxValue(managerObj.find('input[name=\'lang-select\']:checked')));
    });
  },
  initList: function()
  {
    var tthis = this;
    var managerObj = tthis.obj.find('.manager');
    managerObj.find('input.id').click(function(){ tthis.parent.lib.highlightLine(this); });
    managerObj.find('input.idall').click(function(){ tthis.parent.lib.highlightLineAll(this); });
    managerObj.find('.pagi').find('a.go').click(function(){ tthis.parent.lib.loadPagiGoURL(this, managerObj); });
    managerObj.find('icon.delete').click(function(){
      var thisObj = $(this);
      tthis.parent.lib.popupConfirm(thisObj.attr('confirm_text'), thisObj.attr('confirm_b2'), thisObj.attr('confirm_b3'), function(argObj){
        var myObj = argObj;
        var url = tthis.para['fileurl'] + '?type=action&action=delete&id=' + encodeURIComponent(thisObj.attr('rsid'));
        $.get(url, function(data){ tthis.parent.loadMainURLRefresh(); myObj.parent().find('button.b3').click(); });
      });
    });
    managerObj.find('span.mainlink').each(function(){
      var thisObj = $(this);
      var editObj = thisObj.parent().parent().find('icon.edit');
      if (!editObj.hasClass('show-0'))
      {
        thisObj.addClass('hand').on('click', function(){ editObj.find('a.link').click(); });
      };
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
    tthis.bindSelectPopedomEvents();
    tthis.bindSelectLangEvents();
    managerObj.find('.form_button').find('button.b2').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        var formObj = thisObj.parent().parent();
        managerObj.find('input[name=\'lang\']').trigger('update');
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
    tthis.bindSelectPopedomEvents();
    tthis.bindSelectLangEvents();
    managerObj.find('.form_button').find('button.b2').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        var formObj = thisObj.parent().parent();
        managerObj.find('input[name=\'lang\']').trigger('update');
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