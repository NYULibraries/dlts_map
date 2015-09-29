<?php if ($page) : ?>
  <div id="navbar" class="pane navbar">
    <?php if ( isset( $button_fullscreen ) || isset( $button_metadata ) ) : ?>
      <ul class="navbar navbar-left">
        <?php if ( isset( $button_metadata)) : print $button_metadata; endif; ?>
      </ul>
    <?php endif; ?>
    <div class="navbar navbar-middle">
      <?php if ($control_panel): ?>
        <?php print $control_panel; ?>
      <?php endif; ?>
    </div>
    <ul class="navbar  navbar-arrows">
      <?php if (isset($prevpage)) : print '<li class="navbar-item">' . $prevpage . '</li>'; endif; ?>      
      <?php if (isset($nextpage)) : print '<li class="navbar-item">' . $nextpage . '</li>'; endif; ?>
    </ul>
    <ul class="navbar-fullscreen" >
      <?php if (isset( $button_fullscreen)) : print $button_fullscreen; endif; ?>     
    </ul>
  </div>
  <div id="main" class="pane main">
    <div id="pagemeta" class="pane pagemeta hidden">
      <?php if (isset($metadata)) : print render($metadata); endif; ?>
    </div>
    <div id="display" class="pane display">
      <?php if ( isset( $prevpage ) ) : print $prevpage; endif; ?>
      <?php print render($content); ?>
      <?php if ( isset( $nextpage ) ) : print $nextpage; endif; ?>    
    </div>
    <div class="pane load loading"><?php if (isset($loading)) : print $loading; endif; ?></div>
  </div>
<?php endif; ?>
