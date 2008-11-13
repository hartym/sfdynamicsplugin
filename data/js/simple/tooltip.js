var DH = 0, an = 0, al = 0, ai = 0;

if (document.getElementById) {
  ai = 1;
  DH = 1;
}
else {
  if (document.all) {
    al = 1;
    DH = 1;
  }
  else {
    browserVersion = parseInt(navigator.appVersion);
    if ((navigator.appName.indexOf('Netscape') != -1) && (browserVersion == 4)) {
      an = 1;
      DH = 1;
    }
  }
}

jQuery.fn.extend({
  tooltip: function(index)
  {
    var _tooltip = $('div#sf_tooltip_'+index).eq(0);

    this.mouseover(function(event){ jQuery.tooltip(event, _tooltip); });
    this.mouseout(function(event){ jQuery.tooltip(event, _tooltip); });
  }
});

jQuery.extend({
  tooltip: function(evt, element)
  {
    function pageWidth()
    {
      return window.innerWidth != null ? window.innerWidth : document.body.clientWidth != null ? document.body.clientWidth : null;
    }

    function mouseX(evt)
    {
      if (evt.pageX)
      {
        return evt.pageX;
      }
      else if (evt.clientX)
      {
        return evt.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
      }
      else
      {
        return null;
      }
    }

    function mouseY(evt)
    {
      if (evt.pageY)
      {
        return evt.pageY;
      }
      else if (evt.clientY)
      {
        return evt.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
      }
      else
      {
        return null;
      }
    }

    if (DH)
    {
      if (element.attr('offsetWidth'))
      {
        elementWidth = element.attr('offsetWidth');
      }
      else if (element.attr('clip'))
      {
        elementWidth = element.attr('clip').width;
      }

      if (element.css('visibility') == 'visible' || element.css('visibility') == 'show')
      {
        element.css('visibility', 'hidden');
      }
      else
      {
        tv = mouseY(evt) + 20;
        lv = mouseX(evt) - (elementWidth/4);

        if (lv < 2)
        {
          lv = 2;
        }
        else if (lv + elementWidth > pageWidth())
        {
          lv -= elementWidth/2;
        }

        if (!an)
        {
          lv += 'px';
          tv += 'px';
        }

        element.css('left', lv);
        element.css('top', tv);
        element.css('visibility', 'visible');
      }
    }
  }
});

