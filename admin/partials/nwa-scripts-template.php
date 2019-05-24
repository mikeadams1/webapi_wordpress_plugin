<?php



?>

<div class="<?php echo $atts['classname']; ?>" ></div>
<script>
  (function( $ ) {
    'use strict';
    JNC.setContent("https://webapiv2.navionics.com/dist/webapi/images");
    NWA.addStyleString('.<?php echo $atts['classname']; ?> { <?php echo $atts['css_code']; ?> } ');
    <?php echo $atts['js_code']; ?>
  })( jQuery );
</script>
