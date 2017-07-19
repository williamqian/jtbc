var jtbc = window.jtbc || {};
jtbc.frontend = {
  para: [],
  bindEventsByMode: function(argObj)
  {
    var tthis = this;
    var obj = argObj;
    obj.find('*[mode]').each(function(){
      var thisObj = $(this);
      if (thisObj.attr('mode') == 'submenu')
      {
        thisObj.on('click', function(){
          var myObj = $(this);
          if (!myObj.hasClass('on'))
          {
            myObj.addClass('on');
            myObj.parent().find(myObj.attr('selector')).addClass('on');
          }
          else
          {
            myObj.removeClass('on');
            myObj.parent().find(myObj.attr('selector')).removeClass('on');
          };
        });
      }
      else if (thisObj.attr('mode') == 'pitchon')
      {
        thisObj.find('*[name=\'' + thisObj.attr('onname') + '\']').addClass('on');
      };
    });
  },
  ready: function()
  {
    var tthis = this;
    var obj = $('.wrap');
    obj.find('dfn').each(function(){
      var myObj = $(this);
      if (myObj.attr('call')) eval(myObj.attr('call'));
    });
    tthis.bindEventsByMode(obj);
    $(document).on('scroll', function(){ obj.find('.header').css({'left': -$(document).scrollLeft() || 'auto'}); });
  }
};