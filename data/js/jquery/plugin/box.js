/*
 * Licensed under the MIT:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ]
 */

(function($) {
  $.box = function(data, klass) {
    $.box.init();
    $.box.loading();
    $.isFunction(data) ? data.call($) : $.box.reveal(data, klass);
  };

  $.box.loading = function() {
    if ($('#box .loading').length == 1)
    {
      return true;
    }

    $('#box .box-bd').empty();
    $('#box .body').children().hide().end().append('<div class="loading"><img src="'+$.box.settings.loading_image+'"/></div>');

    var pageScroll = $.box.getPageScroll();


    $('#box').css({
      top:  $.box.getPageHeight() / 10,
      left: pageScroll[0]
    }).show();

    $(document).bind('keydown.box', function(e) {
      if (e.keyCode == 27) // escape
      {
        $.box.close();
      }
    })
  };

  $.box.reveal = function(data, klass) {
    if (klass)
    {
      $('#box .content').addClass(klass);
    }

    $('#box .box-bd').append(data);
    $('#box .loading').remove();
    $('#box .body').children().fadeIn('normal');
  };

  $.box.close = function() {
    $(document).trigger('close.box');
    return false;
  };

  $(document).bind('close.box', function() {
    $(document).unbind('keydown.box');
    $('#box').fadeOut(function() {
      $('#box .box-bd').removeClass().addClass('box-bd');
    })
  });

  $.fn.box = function(settings) {
    $.box.init(settings);

    var image_types = $.box.settings.image_types.join('|');
    image_types = new RegExp('\.'+image_types+'$', 'i');

    function click_handler() {
      $.box.loading(true);

      // support for rel="box[.inline_popup]" syntax, to add a class
      var klass = this.rel.match(/box\[\.(\w+)\]/);
      if (klass) klass = klass[1];

      // div
      if (this.href.match(/#/)) {
        var url    = window.location.href.split('#')[0];
        var target = this.href.replace(url,'');
        $.box.reveal($(target).clone().show(), klass);

      // image
      } else if (this.href.match(image_types)) {
        var image = new Image();
        image.onload = function() {
          $.box.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass);
        }
        image.src = this.href;

      // ajax
      } else {
        $.ajax({
          type: "GET",
          url: this.href,
          success: function(msg){
            $.box.reveal(msg, klass);
          },
          error: function (xhr, st, err) { $.box.close(); alert('Chargement impossible. Merci de réessayer dans un moment.'); }
        });
      }

      return false;
    }

    // prevent multiple click handler
    this.unbind('click');
    this.click(click_handler);
    return this;
  }

  $.box.init = function(settings) {
    if ($.box.settings.inited) {
      return true;
    } else {
      $.box.settings.inited = true;
    }

    if (settings) $.extend($.box.settings, settings);
    $('body').append($.box.settings.box_html);

    var preload = [ new Image(), new Image() ];
    preload[0].src = $.box.settings.close_image;
    preload[1].src = $.box.settings.loading_image;

    $('#box').find('.b:first, .bl, .br, .tl, .tr').each(function() {
      preload.push(new Image());
      preload.slice(-1).src = $(this).css('background-image').replace(/url\((.+)\)/, '$1');
    })

    $('#box .close').click($.box.close);
    $('#box .close_image').attr('src', $.box.settings.close_image);
  }

  // getPageScroll() by quirksmode.com
  $.box.getPageScroll = function() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) { // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;
    }
    return new Array(xScroll,yScroll);
  }

  // adapter from getPageSize() by quirksmode.com
  $.box.getPageHeight = function() {
    var windowHeight
    if (self.innerHeight) { // all except Explorer
      windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
      windowHeight = document.body.clientHeight;
    }
    return windowHeight;
  }
})(jQuery);
