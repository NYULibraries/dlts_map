<div id="page" class="page">
  <div id="top" class="pane top yui3-g">
    <div class="yui3-u-1">
      <?php if ($messages) : ?>
        <?php print $messages; ?>   
      <?php endif; ?>
      <?php if ($title) : ?>
        <?php print render($title_prefix); ?>
        <div id="titlebar" class="titlebar">
          <h1 class="title" id="page-title"><?php if (isset($book_title)) : print $book_title; else : print $title; endif; ?></h1>
        </div>
        <?php print render($title_suffix); ?>
      <?php endif; ?>
      <?php if ($tabs) : ?>
        <div class="tabs"><?php print render($tabs); ?></div>
      <?php endif; ?>
      <?php print render($page['help']); ?>    
      <?php if ($action_links): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
    </div>
  </div>
  <?php print render($page['content']); ?>
  <?php if (isset($page['footer']) && !empty($page['footer'])) : ?>
    <div class="footer yui3-g">
      <div class="yui3-u-1">  
        <?php print render($page['footer']); ?>
      </div>
    </div>
  <?php endif; ?>
</div>
