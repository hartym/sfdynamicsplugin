$.extend({
  /**
   * Initialize unitilialized ajax links.
   *
   * Ajax links are spotted by having a rel="ajax:jquery-target" and not having the sf-ignore-ajax class.
   * Once a link is initialized, we add it the sf-ignore-ajax class so it wont send more than one xhr request
   * if the script is called more than once.
   *
   * @author   Romain Dorgueil <romain.dorgueil@sensio.com>
   * @version  $SVN:Id $
   */
  links: function()
    {
      $("a[rel*=ajax]").each(function()
      {
        if (!$(this).hasClass('sf-ignore-ajax'))
        {
          $(this).addClass('sf-ignore-ajax');

          $(this).click(function()
          {
            var _selector = undefined;

            $.each($(this).attr('rel').split(' '), function()
            {
              var _tmp = this.split(':');

              if ( _tmp.length == 2 && _tmp[0] == 'ajax' )
              {
                _selector = _tmp[1];
                return false;
              }
            });

            if (_selector != undefined)
            {
              _element = $(_selector);

              if (_element.length)
              {
                $.ajax({
                   type: "GET",
                   async: false,
                   url: $(this).attr('href'),
                   success: function(msg) {
                     _element.html(msg);
                   },
                   error: function(msg) {
                     alert('Une erreur est survenue lors du chargement. Merci de réessayer ultérieurement.');
                   }
                });

                return false;
              }
            }
          });
        }
      });
    }
  });
