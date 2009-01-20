(function($) {
  $.data('focusedElement', undefined);

  $.fn.extend({
    getFocusedElement: function() {
      return $data('focusedElement');
    }
  });

  $(document).ready(function() {
    $('*').focus(function(evt) {
      $.data('focusedElement', evt.target);
    })
  });
})(jQuery);
