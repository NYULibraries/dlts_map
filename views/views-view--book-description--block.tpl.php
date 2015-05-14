<?php
  // $Id: views-view.tpl.php,v 1.13 2009/06/02 19:30:44 merlinofchaos Exp $
?>
<div class="view view-<?php print $css_name; ?> view-id-<?php print $name; ?> view-display-id-<?php print $display_id; ?> view-dom-id-<?php print $dom_id; ?>">
  <?php if (isset($admin_links)): ?>
    <div class="views-admin-links views-hide">
      <?php print $admin_links; ?>
    </div>
   <?php endif; ?>
   <?php if (isset($header)): ?>
     <div class="view-header">
       <?php print $header; ?>
     </div>
   <?php endif; ?>
 
   <?php if (isset($exposed)): ?>
     <div class="view-filters">
       <?php print $exposed; ?>
     </div>
   <?php endif; ?>
 
   <?php if (isset($attachment_before)): ?>
     <div class="attachment attachment-before">
       <?php print $attachment_before; ?>
     </div>
   <?php endif; ?>
 
   <?php if (isset($rows)): ?>
     <div class="view-content">
       <?php print $rows; ?>
     </div>
   <?php elseif (isset($empty)): ?>
     <div class="view-empty">
       <?php print $empty; ?>
     </div>
   <?php endif; ?>
 
   <?php if (isset($pager)): ?>
     <?php print $pager; ?>
   <?php endif; ?>
 
   <?php if (isset($attachment_after)): ?>
     <div class="attachment attachment-after">
       <?php print $attachment_after; ?>
     </div>
   <?php endif; ?>
 
   <?php if (isset($more)): ?>
     <?php print $more; ?>
   <?php endif; ?>
 
   <?php if (isset($footer)): ?>
     <div class="view-footer">
       <?php print $footer; ?>
     </div>
   <?php endif; ?>
 
   <?php if (isset($feed_icon)): ?>
     <div class="feed-icon">
       <?php print $feed_icon; ?>
     </div>
   <?php endif; ?>
 
</div><?php /* class view */ ?>
