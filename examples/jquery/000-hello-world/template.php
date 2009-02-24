<style>
  div#hello
  {
    display: none;
  }
</style>

<script>
  $(document).ready(function() {
    $('a#say-hello').click(function() {
      $('div#hello').toggle();
      return false;
    })
  });
</script>

<a href="#" id="say-hello">Click here...</a>
<div id="hello">
  Hello World!
</div>
