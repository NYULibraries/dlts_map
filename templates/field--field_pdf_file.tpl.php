<div class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php if (!$label_hidden) : ?>
    <div class="field-label field-label-pdf"<?php print $title_attributes; ?>><?php print $label; ?>: </div>
  <?php endif; ?>
  <div class="field-items"<?php print $content_attributes; ?>><?php foreach ($items as $delta => $item) : ?><?php print render($item); ?><?php if (!$delta == count($items) - 1 ) :?>, <?php endif; ?><?php endforeach; ?></div>  
</div>