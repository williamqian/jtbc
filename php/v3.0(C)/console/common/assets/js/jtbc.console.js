var jtbc = window.jtbc || {};
jtbc.console = {
  para: [],
  parent: jtbc,
  manageURL: 'manage.php',
  managerapiURL: 'managerapi.php',
  materialFolder: 'universal/material/',
  bindCommonEvents: function(argObj)
  {
    var obj = argObj;
    obj.find('.alonetips').each(function(){
      var thisObj = $(this);
      if (thisObj.parent().find(this.tagName).length == thisObj.parent().find(this.tagName + '.hide').length) thisObj.removeClass('hide');
    });
    obj.find('.pitchon').each(function(){
      var thisObj = $(this);
      var pitchon = thisObj.attr('upitchon') || thisObj.attr('pitchon');
      if (pitchon) thisObj.find(pitchon).addClass('on');
    });
    obj.find('.switch').each(function(){
      var thisObj = $(this);
      if (thisObj.attr('bind') != '1')
      {
        thisObj.attr('bind', '1');
        thisObj.find('b').on('mouseup', function(){
          thisObj.addClass('switch-1');
          thisObj.find('input.val').val('1');
        });
        thisObj.find('u').on('mouseup', function(){
          thisObj.removeClass('switch-1');
          thisObj.find('input.val').val('0');
        });
      };
    });
    obj.find('.singleselect').each(function(){
      var thisObj = $(this);
      thisObj.find(thisObj.attr('childtag')).click(function(){
        var childObj = $(this);
        childObj.parent().find(this.tagName).removeClass('on').eq(childObj.index()).addClass('on');
      });
    });
    obj.find('select.select').each(function(){
      var thisObj = $(this);
      thisObj.val(thisObj.attr('val'));
    });
  },
  insertHTML: function(argObj, argHTML)
  {
    var tthis = this;
    var obj = argObj;
    var html = argHTML;
    obj.html(html).find('dfn').each(function(){
      var myObj = $(this);
      if (myObj.attr('url')) obj.append('<script type="text/javascript" src="' + tthis.para['root'] + myObj.attr('url') + '"></script>');
      else if (myObj.attr('cssurl')) obj.append('<link rel="stylesheet" href="' + tthis.para['root'] + myObj.attr('cssurl') + '" />');
      else if (myObj.attr('call')) eval(myObj.attr('call'));
    });
    tthis.bindCommonEvents(obj);
  },
  rsetWidthAndHeight: function()
  {
    var myObj = $('.console');
    var windowHeight = $(window).height();
    myObj.find('.container').css({'height': (windowHeight - myObj.find('.topbar').height()) + 'px'});
  },
  loadHTML: function()
  {
    var tthis = this;
    var consoleObj = $('#console');
    tthis.para['root'] = consoleObj.attr('root');
    $.get(tthis.manageURL, function(data){
      var dataObj = $(data);
      if (dataObj.find('result').attr('status') == '1') tthis.insertHTML(consoleObj, dataObj.find('result').text());
    });
  },
  loadMainURL: function(argUrl)
  {
    var tthis = this;
    var myURL = argUrl;
    var myObj = $('.console');
    var mainObj = myObj.find('.container').find('.main');
    var loadedCallBack = function()
    {
      mainObj.find('nav').find('u').click(function(){
        var thisObj = $(this);
        var myLink = thisObj.attr('link');
        var myGenre = thisObj.attr('genre');
        if (myLink)
        {
          if (myGenre == '|self|') tthis.loadMainURL(myLink);
          else tthis.loadMainURL(tthis.para['root'] + thisObj.attr('genre') + '/' + thisObj.attr('link'));
        };
      });
      mainObj.find('a.link').click(function(){
        var thisObj = $(this);
        var managerObj = mainObj.find('.manager');
        if (thisObj.attr('link')) tthis.loadMainURL(tthis.para['current-main-fileurl'] + thisObj.attr('link'));
      });
      myObj.find('nav').on('selectleftmenu', function(){
        var thisObj = $(this);
        var selGenre = thisObj.attr('genre');
        myObj.find('.leftmenu').find('li').removeClass('on').find('span.tit').removeClass('on');
        if (selGenre)
        {
          var selGenreObj = myObj.find('.leftmenu').find('span[genre=\'' + selGenre + '\']');
          if (selGenreObj.length == 1)
          {
            if (!myObj.find('.leftmenu').hasClass('min'))
            {
              selGenreObj.addClass('on');
              myObj.find('.leftmenu').find('li').has(selGenreObj).addClass('on').find('span.tit').addClass('open');
              myObj.find('.leftmenu').find('dl').has(selGenreObj).addClass('open').find('span.tit').addClass('open');
            };
          };
        };
      }).trigger('selectleftmenu');
    };
    if (tthis.para['load-main-url-lock'] != true)
    {
      tthis.para['load-main-url-lock'] = true;
      tthis.para['load-main-url-waiting'] = setTimeout(function(){
        mainObj.parent().find('.waiting').addClass('on');
      }, 500);
      $.get(myURL, function(data){
        var dataObj = $(data);
        clearTimeout(tthis.para['load-main-url-waiting']);
        mainObj.parent().find('.waiting').removeClass('on');
        if (dataObj.find('result').attr('status') == '1')
        {
          location.href = '#' + myURL;
          tthis.para['load-main-url'] = myURL;
          tthis.insertHTML(mainObj, dataObj.find('result').text());
          loadedCallBack();
        };
        tthis.para['load-main-url-lock'] = false;
      });
    };
  },
  loadMainURLRefresh: function()
  {
    var tthis = this;
    tthis.loadMainURL(tthis.para['load-main-url']);
  },
  initConsole: function()
  {
    var tthis = this;
    var myObj = $('.console');
    myObj.find('.topbar').find('span.menu').click(function(){
      var thisObj = $(this);
      if (thisObj.parent().hasClass('min'))
      {
        thisObj.parent().removeClass('min');
        myObj.find('.leftmenu').removeClass('min');
        myObj.find('.leftmenu').find('span.tit.t1').removeAttr('title');
        myObj.find('nav').trigger('selectleftmenu');
      }
      else
      {
        thisObj.parent().addClass('min');
        myObj.find('.leftmenu').addClass('min').find('li').removeClass('on');
        myObj.find('.leftmenu').find('span.tit').removeClass('open');
        myObj.find('.leftmenu').find('dl.open').removeClass('open');
        myObj.find('.leftmenu').find('span.tit.t1').each(function(){ $(this).attr('title', $(this).attr('mytitle')); });
      };
    });
    myObj.find('.topbar').find('account').find('li.l1').click(function(){
      var thisObj = $(this);
      if (thisObj.attr('loading') != 'true')
      {
        thisObj.attr('loading', 'true');
        $.get(tthis.manageURL + '?type=modifypassword', function(data){
          var dataObj = $(data);
          if (dataObj.find('result').attr('status') == '1')
          {
            var pageObj = tthis.lib.popupPage(dataObj.find('result').text());
            var tinyformObj = pageObj.find('.tinyform');
            tinyformObj.find('button.b2').click(function(){
              var thisObj = $(this);
              if (!thisObj.hasClass('lock'))
              {
                thisObj.addClass('lock');
                var formObj = thisObj.parent().parent();
                var url = tthis.manageURL + formObj.attr('action');
                $.post(url, formObj.serialize(), function(data){
                  var dataObj = $(data);
                  thisObj.removeClass('lock');
                  formObj.find('span.tips').addClass('h').html(dataObj.find('result').attr('message'));
                  if (dataObj.find('result').attr('status') == '1') formObj.each(function(){ this.reset(); });
                });
              };
            });
          };
          thisObj.attr('loading', 'false');
        });
      };
    });
    myObj.find('.topbar').find('account').find('li.l2').click(function(){ myObj.find('.topbar').find('logout').trigger('click'); });
    myObj.find('.topbar').find('lang').each(function(){
      var thisObj = $(this);
      $.get(tthis.manageURL + '?type=getlang', function(data){
        var dataObj = $(data);
        if (dataObj.find('result').attr('status') == '1')
        {
          thisObj.html(dataObj.find('result').text());
          thisObj.find('li').on('click', function(){
            var thisObj = $(this);
            if (thisObj.attr('loading') != 'true')
            {
              thisObj.attr('loading', 'true');
              $.get(tthis.manageURL + '?type=action&action=setlang&lang=' + encodeURIComponent(thisObj.attr('val')), function(data){
                var dataObj = $(data);
                thisObj.attr('loading', 'false');
                if (dataObj.find('result').attr('status') == '1')
                {
                  myObj.find('nav').find('u:last').trigger('click');
                  thisObj.parent().parent().find('b').find('flag').attr('class', 'f' + thisObj.attr('val')).nextAll('span').html(tthis.parent.htmlEncode(thisObj.attr('text')));
                };
              });
            };
          });
        };
      });
    });
    myObj.find('.topbar').find('logout').click(function(){
      var thisObj = $(this);
      tthis.lib.popupConfirm(thisObj.attr('confirm_text'), thisObj.attr('confirm_b2'), thisObj.attr('confirm_b3'), function(){ $.get(tthis.manageURL + '?type=action&action=logout', function(data){ location.href = '#'; tthis.loadHTML(); }); });
    });
    myObj.find('.leftmenu').find('span.tit').click(function(){
      var thisObj = $(this);
      if (!myObj.find('.leftmenu').hasClass('min'))
      {
        if (thisObj.next().hasClass('open'))
        {
          thisObj.removeClass('open');
          thisObj.next().removeClass('open');
        }
        else
        {
          thisObj.addClass('open');
          thisObj.next().addClass('open');
        };
      };
      if (thisObj.attr('link')) tthis.loadMainURL(tthis.para['root'] + thisObj.attr('genre') + '/' + thisObj.attr('link'));
    });
    var defURL = myObj.find('.container').find('.main').attr('def');
    if (location.href.indexOf('#') != -1)
    {
      var locURL = location.href.substr(location.href.indexOf('#') + 1);
      if (locURL != '') defURL = locURL;
    };
    tthis.loadMainURL(defURL);
    tthis.rsetWidthAndHeight();
    $(window).resize(function(){ tthis.rsetWidthAndHeight(); });
  },
  initLogin: function()
  {
    var tthis = this;
    var myObj = $('.login');
    myObj.find('.button .b1').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        myObj.find('.message').html(thisObj.attr('loading'));
        var formValue = myObj.find('form').serialize();
        $.post(tthis.manageURL + '?type=action&action=login', formValue, function(data){
          var dataObj = $(data);
          thisObj.removeClass('lock');
          if (dataObj.find('result').attr('status') != '1')
          {
            myObj.find('.message').html(dataObj.find('result').attr('message'));
          }
          else
          {
            tthis.loadHTML();
          };
        });
      };
    });
  },
  ready: function()
  {
    var tthis = this;
    tthis.loadHTML();
  }
};

jtbc.console.lib = {
  para: [],
  parent: jtbc.console,
  dragSort: function(argObj, argChild, argDragName, argCallBack, argCallBackDone)
  {
    var tthis = this;
    var myObj = argObj;
    var myChild = argChild;
    var myDragName = argDragName;
    var myCallBack = argCallBack;
    var myCallBackdDone = argCallBackDone;
    myObj.on({
      mousemove: function(ev)
      {
        var thisObj = $(this);
        var noselectObj = thisObj.find(myChild + '.noselect');
        if (noselectObj.length == 1)
        {
          var targetObj = null;
          if (ev.pageY < noselectObj.offset().top || ev.pageY > (noselectObj.offset().top + noselectObj.height()))
          {
            thisObj.find(myChild).each(function(){
              var myLiObj = $(this);
              if (ev.pageY < (myLiObj.offset().top + myLiObj.height()))
              {
                if (!myLiObj.hasClass('.noselect')) targetObj = myLiObj;
                return false;
              };
            });
          };
          if (targetObj != null)
          {
            thisObj.attr('change', 'true');
            if (targetObj.index() < noselectObj.index()) targetObj.before(noselectObj);
            else targetObj.after(noselectObj);
            myCallBack();
          };
        };
      },
      mouseup: function()
      {
        var thisObj = $(this);
        $(document.body).removeClass('noselect');
        thisObj.find(myChild + '.noselect').removeClass('noselect');
        if (thisObj.attr('change') == 'true')
        {
          myCallBackdDone();
          thisObj.attr('change', 'false');
        };
      },
      mouseover: function()
      {
        var thisObj = $(this);
        clearTimeout(tthis.para[thisObj.attr('ds')]);
      },
      mouseout: function()
      {
        var thisObj = $(this);
        tthis.para[thisObj.attr('ds')] = setTimeout(function(){ thisObj.trigger('mouseup'); }, 100);
      }
    });
    myObj.on('mousedown', myChild + ' ' + myDragName, function(){
      var thisObj = $(this);
      var itemObj = thisObj.parent();
      myObj.attr('ds', 'rnd' + Math.floor(Math.random() * 1000000 + 1000000));
      $(document.body).addClass('noselect');
      itemObj.find(myChild).removeClass('noselect');
      itemObj.addClass('noselect');
    });
  },
  fileUp: function(argObj, argFileUpObj, argFileUpURL, argCallBack, argCallBackItem)
  {
    var myObj = argObj;
    var fileUpObj = argFileUpObj;
    var fileUpURL = argFileUpURL;
    var callback = argCallBack;
    var callbackItem = argCallBackItem;
    var fileupload = {
      files: null,
      filecount: 0,
      fileindex: 0,
      formatFileInfo: function(argFile)
      {
        var fileSize = 0;
        var file = argFile;
        if (file.size > 1024 * 1024) fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
        else if (file.size > 1024) fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
        else fileSize = file.size + 'B';
        var fileItem = $('<div class="item"></div>');
        fileItem.append('<span class="filename">' + file.name + '</span>');
        fileItem.append('<span class="filesize">' + fileSize + '</span>');
        fileItem.append('<span class="bar"></span>');
        return fileItem;
      },
      uploadNextFile: function()
      {
        var index = this.fileindex;
        var myFormData = new FormData();
        myFormData.append('file', this.files[index]);
        var myXMLHttpRequest = new XMLHttpRequest();
        myXMLHttpRequest.upload.addEventListener('progress', function(evt){
          fileUpObj.find('div.item').eq(fileupload.fileindex).find('span.bar').css({'width': Math.round(evt.loaded * 100 / evt.total) + '%'});
        }, false);
        myXMLHttpRequest.addEventListener('load', function(evt){
          var upSucceed = false;
          var upMessage = '';
          var resultObj = $(evt.target.responseXML);
          if (resultObj.find('result').attr('status') == '1') upSucceed = true;
          else if (resultObj.find('result').attr('status') == '0') upMessage = resultObj.find('result').attr('message');
          if (upSucceed == false)
          {
            fileUpObj.find('div.item').eq(fileupload.fileindex).addClass('error').attr('title', upMessage).on('dblclick', function(){ $(this).fadeOut(); });
          };
          if (typeof(callbackItem) == 'function') callbackItem(upSucceed, resultObj, fileupload.fileindex);
          fileupload.fileindex += 1;
          if (fileupload.fileindex < fileupload.filecount) fileupload.uploadNextFile();
          else callback();
        }, false);
        myXMLHttpRequest.open('POST', fileUpURL);
        myXMLHttpRequest.send(myFormData);
      },
      startUpload: function()
      {
        this.fileindex = 0;
        this.uploadNextFile();
      }
    };
    if (myObj.files.length >= 1)
    {
      fileupload.files = myObj.files;
      fileupload.filecount = myObj.files.length;
      for (var k = 0; k < myObj.files.length; k ++)
      {
        fileUpObj.append(fileupload.formatFileInfo(myObj.files[k]));
      };
      fileupload.startUpload();
    };
  },
  fileUpSingle: function(argObj, argFileUpURL, argCallBack)
  {
    var myObj = argObj;
    var fileUpURL = argFileUpURL;
    var callback = argCallBack;
    if (myObj.files.length == 1)
    {
      var file = myObj.files[0];
      var myFormData = new FormData();
      myFormData.append('file', file);
      var myXMLHttpRequest = new XMLHttpRequest();
      myXMLHttpRequest.addEventListener('load', function(evt){
        var dataObj = $(evt.target.responseXML);
        callback(dataObj);
      }, false);
      myXMLHttpRequest.open('POST', fileUpURL);
      myXMLHttpRequest.send(myFormData);
    };
  },
  getCheckBoxValue: function(argObj)
  {
    var value = '';
    var myObj = argObj;
    myObj.each(function(){ value += $(this).val() + ','; });
    if (value.length > 0) value = value.substring(0, value.length - 1);
    return value;
  },
  highlightLine: function(argObj)
  {
    var myObj = argObj;
    if (myObj.checked) $(myObj).parent().parent().parent().addClass('selected');
    else $(myObj).parent().parent().parent().removeClass('selected');
  },
  highlightLineAll: function(argObj)
  {
    var myObj = argObj;
    var myForObj = $('input[name=\'' + $(myObj).attr('forname') + '\']');
    if (myObj.checked)
    {
      myForObj.each(function(){ if (!this.checked) this.click(); });
    }
    else
    {
      myForObj.each(function(){ if (this.checked) this.click(); });
    };
  },
  initAttEvents: function(argObj, argInsertFun)
  {
    var tthis = this;
    var myObj = argObj;
    var insertFun = argInsertFun;
    var attObj = myObj.find('.att');
    var attValObj = attObj.find('input[name=\'att\']');
    var resetAttInputVal = function()
    {
      var attArray = new Array();
      attObj.find('ul').find('li').each(function(){
        var thisObj = $(this);
        if (!thisObj.hasClass('del'))
        {
          attArray[attArray.length] = thisObj.attr('para');
        };
      });
      attValObj.val(JSON.stringify(attArray));
    };
    var appendNewAttLi = function(argPara)
    {
      var para = argPara;
      var paraArray = JSON.parse(para);
      var ulObj = attObj.find('ul');
      ulObj.find('li.null').remove();
      ulObj.append('<li para="' + tthis.parent.parent.htmlEncode(para) + '"><em class="filetype move ' + tthis.parent.parent.htmlEncode(paraArray['filetype']) + '">' + tthis.parent.parent.htmlEncode(paraArray['filetype']) + '</em><span class="tit">' + tthis.parent.parent.htmlEncode(paraArray['filename']) + '</span><span class="size">' + tthis.parent.parent.htmlEncode(paraArray['filesizetext']) + '</span><icons><icon class="insert" title="' + ulObj.attr('text-insert') + '"></icon><icon class="delete" title="' + ulObj.attr('text-delete') + '"></icon></icons></li>');
      resetAttInputVal();
    };
    var initAttValLi = function(){
      var attValObjValue = attValObj.val();
      if (attValObjValue)
      {
        allAttObj = JSON.parse(attValObjValue);
        for (var i in allAttObj) appendNewAttLi(allAttObj[i]);
      };
    }();
    tthis.dragSort(attObj.find('ul'), 'li', 'em.filetype', function(){ resetAttInputVal(); });
    attObj.find('ul').on('click', 'li span.tit', function(){
      var thisObj = $(this);
      var liObj = thisObj.parent();
      var paraArray = JSON.parse(liObj.attr('para'));
      var filetype = paraArray['filetype'];
      var fileURL = paraArray['fileurl'];
      if (!tthis.parent.parent.isAbsoluteURL(fileURL)) fileURL = attObj.attr('folder') + fileURL;
      var pageObj = tthis.previewAtt(filetype, paraArray['filename'], fileURL, liObj.parent().attr('text-preview-link'), '1');
      pageObj.find('input.title').on('keyup', function(){
        paraArray['filename'] = $(this).val();
        thisObj.html(tthis.parent.parent.htmlEncode(paraArray['filename']));
        liObj.attr('para', JSON.stringify(paraArray));
        resetAttInputVal();
      });
    });
    attObj.find('ul').on('click', 'li icon.insert', function(){
      var thisObj = $(this);
      var liObj = thisObj.parent().parent();
      var paraArray = JSON.parse(liObj.attr('para'));
      var filetype = paraArray['filetype'];
      if (filetype == 'jpg' || filetype == 'gif' || filetype == 'png')
      {
        insertFun('<img src="' + tthis.parent.parent.htmlEncode(paraArray['fileurl']) + '" alt="" />');
      }
      else
      {
        insertFun('<a href="' + tthis.parent.parent.htmlEncode(paraArray['fileurl']) + '" target="_blank">' + tthis.parent.parent.htmlEncode(paraArray['filename']) + '</a>');
      };
    });
    attObj.find('ul').on('click', 'li icon.delete', function(){
      var thisObj = $(this);
      var liObj = thisObj.parent().parent();
      if (!liObj.hasClass('del')) liObj.addClass('del');
      else liObj.removeClass('del');
      resetAttInputVal();
    });
    attObj.find('div.icons').find('icon.db').on('click', function(){
      var thisObj = $(this);
      tthis.loadSelectMaterialPage('multiple', function(argFileInfo){
        var fileInfo = argFileInfo;
        var fileAry = JSON.parse(fileInfo);
        for (var i in fileAry)
        {
          var currentFile = JSON.parse(fileAry[i]);
          var currentFileURL = currentFile['fileurl'];
          if (!tthis.parent.parent.isAbsoluteURL(currentFileURL)) currentFile['fileurl'] = thisObj.attr('root') + tthis.parent.materialFolder + currentFileURL;
          appendNewAttLi(JSON.stringify(currentFile));
        };
      });
    });
    attObj.find('div.icons').find('icon.upload').on('click', function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock')) attObj.find('input.upload').trigger('click');
    });
    attObj.find('input.upload').on('change', function(){
      var thisObj = $(this);
      var url = tthis.parent.para['current-main-fileurl'] + thisObj.attr('action');
      if (thisObj.attr('uploading') != 'true')
      {
        thisObj.attr('uploading', 'true');
        myObj.find('.fileup').find('.item').remove();
        attObj.find('div.upload').addClass('lock');
        tthis.fileUp(this, myObj.find('.fileup'), url, function(){
          attObj.find('div.upload').removeClass('lock');
          attObj.find('input.upload').attr('uploading', 'false').val('');
        }, function(argUpSucceed, argUpResult, argFileIndex){
          var upSucceed = argUpSucceed;
          var upResult = argUpResult;
          var fileIndex = argFileIndex;
          if (upSucceed == true)
          {
            var para = upResult.find('result').attr('para');
            myObj.find('.fileup').find('.item').eq(fileIndex).fadeOut();
            appendNewAttLi(para);
          };
        });
      };
    });
  },
  initUpFileEvents: function(argObj)
  {
    var tthis = this;
    var myObj = argObj;
    myObj.find('button.upbtn').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock')) thisObj.parent().find('input.upfiles').trigger('click');
    });
    myObj.find('input.fileurl').on('dblclick', function(){
      var thisObj = $(this);
      if (thisObj.val()) tthis.previewAtt(null, thisObj.attr('text-preview-title'), tthis.parent.para['current-main-path'] + thisObj.val(), thisObj.attr('text-preview-link'), '0');
    });
    myObj.find('input.upfiles').on('change', function(){
      var thisObj = $(this);
      var btnObj = thisObj.parent().find('button.upbtn');
      var url = tthis.parent.para['current-main-fileurl'] + thisObj.attr('action');
      if (thisObj.attr('uploading') != 'true')
      {
        thisObj.attr('uploading', 'true');
        btnObj.addClass('lock').html(btnObj.attr('uploading'));
        tthis.fileUpSingle(this, url, function(result){
          thisObj.attr('uploading', 'false');
          btnObj.removeClass('lock').html(btnObj.attr('text'));
          if (result.find('result').attr('status') == '1')
          {
            var paraArray = JSON.parse(result.find('result').attr('para'));
            thisObj.parent().find('input.fileurl').val(paraArray['fileurl']);
          }
          else
          {
            tthis.popupAlert(result.find('result').attr('message'), thisObj.attr('text-ok'), function(){});
          };
        });
      };
    });
  },
  loadPagiGoURL: function(argObj1, argObj2)
  {
    var tthis = this;
    var myObj1 = argObj1;
    var myObj2 = argObj2;
    var myObj = $(myObj1);
    var baseLink = myObj.attr('baselink');
    var myLink = baseLink.replace(/(\[\~page\])/g, myObj.parent().find('input.pagenum').val());
    tthis.parent.loadMainURL(tthis.parent.para['current-main-fileurl'] + myLink);
  },
  loadSelectMaterialPage: function(argMode, argCallBack)
  {
    var tthis = this;
    var mode = argMode;
    var callback = argCallBack;
    var url = tthis.parent.para['root'] + tthis.parent.materialFolder + tthis.parent.managerapiURL + '?type=list&mode=' + encodeURIComponent(mode);
    if (tthis.para['select-material-loading'] != true)
    {
      tthis.para['select-material-loading'] = true;
      $.get(url, function(data){
        var dataObj = $(data);
        if (dataObj.find('result').attr('status') == '1')
        {
          var pageObj = tthis.popupPage('');
          tthis.parent.insertHTML(pageObj.find('.content'), dataObj.find('result').text());
          pageObj.on('reload', '.substance', function(){
            var thisObj = $(this);
            if (tthis.para['select-material-loading'] != true)
            {
              tthis.para['select-material-loading'] = true;
              var reloadURL = url + '&get=reload&' + thisObj.attr('reloadpara');
              $.get(reloadURL, function(data){
                var dataObj = $(data);
                if (dataObj.find('result').attr('status') == '1')
                {
                  tthis.parent.insertHTML(pageObj.find('.content'), dataObj.find('result').text());
                  tthis.para['select-material-loading'] = false;
                };
              });
            };
          });
          pageObj.on('click', 'button.b2', function(){
            pageObj.find('span.close').trigger('click');
            callback(pageObj.find('input[name=\'material\']').val());
          });
          tthis.para['select-material-loading'] = false;
        };
      });
    };
  },
  previewAtt: function(argFileType, argFileName, argFileURL, argPreviewLinkText, argMode)
  {
    var tthis = this;
    var fileType = argFileType;
    var fileName = argFileName;
    var fileURL = argFileURL;
    var previewLinkText = argPreviewLinkText;
    var mode = argMode;
    var previewHTML = '';
    if (fileType == null)
    {
      var fileURLAry = fileURL.split('.');
      fileType = fileURLAry[fileURLAry.length - 1];
    };
    if (mode == '0') previewHTML += '<div class="title">' + tthis.parent.parent.htmlEncode(fileName) + '</div>';
    else if (mode == '1') previewHTML += '<div class="title"><input type="text" class="title" value="' + tthis.parent.parent.htmlEncode(fileName) + '" /></div>';
    if (fileType == 'jpg' || fileType == 'gif' || fileType == 'png')
    {
      previewHTML += '<div class="attPreview"><img class="item" src="' + tthis.parent.parent.htmlEncode(fileURL) + '" /></div>';
    }
    else
    {
      previewHTML += '<div class="attPreview"><a class="item" href="' + tthis.parent.parent.htmlEncode(fileURL) + '" target="_blank">' + tthis.parent.parent.htmlEncode(previewLinkText) + '</a></div>';
    };
    var pageObj = tthis.popupPage(previewHTML);
    return pageObj;
  },
  popupAlert: function(argWord, argB2, argCallBack)
  {
    var tthis = this;
    var word = argWord;
    var b2 = argB2;
    var callback = argCallBack;
    var consoleObj = $('#console');
    if (consoleObj.find('.popup_mask').length == 0) consoleObj.append('<div class="popup_mask"></div>');
    if (consoleObj.find('.popup_alert').length == 1) consoleObj.find('.popup_alert').remove();
    consoleObj.append('<div class="popup_alert"><div class="title"></div><div class="word"></div><div class="button"><button class="b2">' + b2 + '</button></div></div>');
    var alertObj = consoleObj.find('.popup_alert');
    var maskObj = consoleObj.find('.popup_mask');
    alertObj.find('.word').html(word);
    alertObj.find('button.b2').click(function(){
      callback($(this));
      maskObj.removeClass('on');
      alertObj.removeClass('on');
    });
    tthis.para['popup-confirm-timeout'] = setTimeout(function(){
      maskObj.addClass('on');
      alertObj.addClass('on');
    }, 100);
    return alertObj;
  },
  popupConfirm: function(argWord, argB2, argB3, argCallBack)
  {
    var tthis = this;
    var word = argWord;
    var b2 = argB2;
    var b3 = argB3;
    var callback = argCallBack;
    var consoleObj = $('#console');
    if (consoleObj.find('.popup_mask').length == 0) consoleObj.append('<div class="popup_mask"></div>');
    if (consoleObj.find('.popup_confirm').length == 1) consoleObj.find('.popup_confirm').remove();
    consoleObj.append('<div class="popup_confirm"><div class="title"></div><div class="word"></div><div class="button"><button class="b3">' + b3 + '</button><button class="b2">' + b2 + '</button></div></div>');
    var confirmObj = consoleObj.find('.popup_confirm');
    var maskObj = consoleObj.find('.popup_mask');
    confirmObj.find('.word').html(word);
    confirmObj.find('button.b2').click(function(){
      var thisObj = $(this);
      if (!thisObj.hasClass('lock'))
      {
        thisObj.addClass('lock');
        callback($(this));
      };
    });
    confirmObj.find('button.b3').click(function(){
      maskObj.removeClass('on');
      confirmObj.removeClass('on');
    });
    tthis.para['popup-confirm-timeout'] = setTimeout(function(){
      maskObj.addClass('on');
      confirmObj.addClass('on');
    }, 100);
    return confirmObj;
  },
  popupPage: function(argHTML)
  {
    var tthis = this;
    var html = argHTML;
    var consoleObj = $('#console');
    if (consoleObj.find('.popup_mask').length == 0) consoleObj.append('<div class="popup_mask"></div>');
    if (consoleObj.find('.popup_page').length == 1) consoleObj.find('.popup_page').remove();
    consoleObj.append('<div class="popup_page"><span class="close"></span><div class="content"></div></div>');
    var pageObj = consoleObj.find('.popup_page');
    var maskObj = consoleObj.find('.popup_mask');
    pageObj.find('div.content').html(html);
    tthis.parent.bindCommonEvents(pageObj);
    pageObj.find('span.close').click(function(){
      maskObj.removeClass('on');
      pageObj.removeClass('on');
    });
    var checkPopupSize = function()
    {
      pageObj.css({'min-width': 'auto', 'min-height': 'auto'});
      var myWidth = Math.round(pageObj.width());
      var myHeight = Math.round(pageObj.height());
      if (myWidth % 2 == 1) myWidth = myWidth + 1;
      if (myHeight % 2 == 1) myHeight = myHeight + 1;
      if (myWidth != 0 && myHeight != 0) pageObj.css({'min-width': myWidth + 'px', 'min-height': myHeight + 'px'});
      requestAnimationFrame(checkPopupSize);
    };
    tthis.para['popup-page-timeout'] = setTimeout(function(){
      maskObj.addClass('on');
      pageObj.addClass('on');
    }, 100);
    checkPopupSize();
    return pageObj;
  }
};