var hovermenuSelectedItem;

(function($) {
  $.extend({
    /**
     * _hoverMenuOpen
     *
     * Open the hover menu of the given opening element. Usually, object is a <li> object
     * enclosed by jQuery object structure, and the submenu is the first <ul> element of this
     * object.
     *
     * @option restrict: selector for object to restrict display
     *
     * @param object
     */
    _hoverMenuOpen: function(object, option)
    {
      var offset_left = 0;
      var styles = {
        visibility: 'visible',
        display: 'none',
        position: 'absolute',
        left: object.position().left,
        top: object.position().top + object.outerHeight()
        };

      if (option)
      {
        if (option.restrict)
        {
          if (object.find('ul:first').outerWidth() + object.position().left > $(option.restrict).outerWidth())
          {
            styles.left += $(option.restrict).outerWidth() - object.find('ul:first').outerWidth() - object.position().left;
          }
        }

        if (option.zIndex != 'undefined')
        {
          styles.zIndex = option.zIndex;
        }
      }

      object.find('ul:first').css(styles).show();
    },

    /**
     * _hoverMenuClose
     *
     * Close the hover menu of the given object.
     */
    _hoverMenuClose: function(object,option)
    {
      object.find('ul:first').css({
        visibility: 'hidden'
        });
    }
  });

  $.fn.extend({
    hoverMenu: function(option)
    {
      this.find('ul').css({display: "none"}); // Hack for Opera browser

      var menus = this.children('li');

      // on mouse over / on mouse out
      menus.hover(function() { $._hoverMenuOpen($(this),option); }, function(event) { $._hoverMenuClose($(this),option); });

      // on focus / on blur
      menus.children('a').focus(function(event) {
        if (hovermenuSelectedItem != undefined)
        {
          $._hoverMenuClose(hovermenuSelectedItem,option);
        }

        if (!$(this).hasClass('closer'))
        {
          $._hoverMenuOpen($(this).parent(),option);
          hovermenuSelectedItem = $(this).parent();
        }
      });
    }
  });
})(jQuery); ;
