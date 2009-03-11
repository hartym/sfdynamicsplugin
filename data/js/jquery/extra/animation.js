$.fn.extend({
  shake: function(direction, count, strength, speed) {
    var offset = (2/3)*(1-Math.pow(-0.5, 2*count-1))*strength;
    var strength = parseInt(strength);

    switch(direction)
    {
      case 'horizontal':
        $(this).animate({'left': '-='+parseInt(offset)+'px'}, speed);

        for (var i=0; i<count; i++)
        {
          $(this).animate({'left': '+='+parseInt(strength)+'px'}, speed);
          strength = strength/2;

          $(this).animate({'left': '-='+parseInt(strength)+'px'}, speed);
          strength = strength/2;
        }
        break;

      case 'vertical':
        $(this).animate({'top': '-='+parseInt(offset)+'px'}, speed);

        for (var i=0; i<count; i++)
        {
          $(this).animate({'top': '+='+parseInt(strength)+'px'}, speed);
          strength = strength/2;

          $(this).animate({'top': '-='+parseInt(strength)+'px'}, speed);
          strength = strength/2;
        }
        break;

      case 'trigo':
        $(this).animate({'left': '-='+parseInt(offset)+'px'}, speed);
        $(this).animate({'top': '+='+parseInt(offset)+'px'}, speed);

        for (var i=0; i<count; i++)
        {
          $(this).animate({'left': '+='+parseInt(strength)+'px'}, speed);
          $(this).animate({'top': '-='+parseInt(strength)+'px'}, speed);
          strength = strength/2;

          $(this).animate({'left': '-='+parseInt(strength)+'px'}, speed);
          $(this).animate({'top': '+='+parseInt(strength)+'px'}, speed);
          strength = strength/2;
        }
        break;

      case 'antitrigo':
        $(this).animate({'top': '-='+parseInt(offset)+'px'}, speed);
        $(this).animate({'left': '+='+parseInt(offset)+'px'}, speed);

        for (var i=0; i<count; i++)
        {
          $(this).animate({'top': '+='+parseInt(strength)+'px'}, speed);
          $(this).animate({'left': '-='+parseInt(strength)+'px'}, speed);
          strength = strength/2;

          $(this).animate({'top': '-='+parseInt(strength)+'px'}, speed);
          $(this).animate({'left': '+='+parseInt(strength)+'px'}, speed);
          strength = strength/2;
        }
        break;
    }
  }
});
