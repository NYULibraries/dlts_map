<?php

function dlts_map_theme ( $existing, $type, $theme, $path ) {
  return array(
    'dlts_loading' => array(
      'template' => 'templates/dlts_loading',
      'variables' => array( ),
    ),
    'dlts_control_panel' => array(
      'template' => 'templates/dlts_control_panel',
      'variables' => NULL,
    ),
  );
}

/** Not meant to be pretty. We know what we want we get it. */
function dlts_map_js_alter(&$javascript) {

  $settings = drupal_array_merge_deep_array ( $javascript['settings']['data'] ) ;
  
  $data = 'var Y = YUI().use(function (Y) { Y.namespace("DLTS"); Y.DLTS.settings = ' . drupal_json_encode ( $settings ) .' });' ;

  // start looking for a better solution
  if ( isset ( $settings ) ) {
    $javascript['init'] = array (
      'group' => JS_THEME,
      'type' => 'inline',
      'every_page' => '',
      'weight' => 5,
      'scope' => 'header',
      'cache' => 1,
      'defer' => FALSE,
      'preprocess' => 1,
      'version' => '',
      'data' => $data,
    ) ;
  }

}

/**
 * Add non JavaScript tags to document
 * See: http://api.drupal.org/api/drupal/includes%21theme.inc/function/template_preprocess_html/7
 */
function dlts_map_process_html ( &$vars ) {
  if ( dlts_utilities_is_pjax() ) {
    $vars['theme_hook_suggestions'][] = 'html__pjax';
  }
}

/**
 * See: http://api.drupal.org/api/drupal/modules%21node%21node.module/function/template_preprocess_node/7
 */
function dlts_map_preprocess_node(&$vars) {

  $type = $vars['type'];
  
  if ( $type !== 'dlts_map' ) return;

  /** node object */
  $node = $vars['node'];

  // Wrap it with Entity API
  $ewrapper = entity_metadata_wrapper('node', $node);
    
  $identifier = $ewrapper->field_identifier->value();

  $isPJAX = dlts_utilities_is_pjax();

  /** Theme absolute-path */
  $theme_path = drupal_get_path('theme', 'dlts_map');

  /** Theme absolute-path */
  $absolute_theme_path = url( $theme_path . '/', array('absolute' => TRUE));
  
  /** Fallback to AJAX and hash browsing in IE <= 9 */
  if ( $isPJAX && isset($browser['msie']) && $browser['msie'] < 10 && !isset($_GET['routed']) ) {
    drupal_goto(str_replace('1#/' . dlts_utilities_collection() . '/', '', $_GET['pjax']), array('query'=>array('pjax' => 1, 'routed' => 1 )), 301);
  }
  
  /** Use node--dlts-book-page.tpl.php for both dlts_map_page and dlts_map_stitched_page content types */
  $vars['theme_hook_suggestions'][] = $isPJAX ? 'node__' . $type . '_pjax' : 'node__' . $type;
  
  /** Page title */
  $vars['page_title'] = $node->title;

  $vars['metadata'] = ( ! $isPJAX ) ? node_view( $node, 'metadata' ) : '';
    
  $vars['loading'] = theme('dlts_loading');

  /** YUI conf */
  $js_yui_files_conf = array('type' => 'file', 'scope' => 'footer', 'weight' => 5);

  drupal_add_js($theme_path . '/js/ui.keyboard.yui.js', $js_yui_files_conf);

  drupal_add_js($theme_path . '/js/ui.components.yui.js', $js_yui_files_conf);

  /** metadata button */
  $vars['button_metadata'] = _dlts_map_navbar_item(
    array(
      'title' => t('Metadata'),
      'path' => 'node/' . $node->nid,
      'attributes' => array('data-title' => t('Metadata'), 'title' => t('Show/hide metadata'), 'class' => array('button', 'metadata', 'on'), 'id' => array('button-metadata')),
      'fragment' => 'metadata',
    )
  );

  /** fullscreen button */
  $vars['button_fullscreen'] = _dlts_map_navbar_item(
    array(
      'title' => t('Fullscreen'),
      'path' => 'node/' . $node->nid,
      'attributes' => array('data-title' => t('Fullscreen'), 'title' => t('Fullscreen'), 'class' => array('button', 'fullscreen'), 'id' => array('button-fullscreen')),
      'fragment' => 'fullscreen',
    )
  );

  /** Zoom in and out buttons */
  $vars['control_panel'] = theme('dlts_control_panel');

  $vars['pane_metadata_hidden'] = FALSE;

  $js_data = array(
    'map' => array(
      'path' => url('maps/' . $identifier, array('absolute' => TRUE )),
      'theme_path' => $absolute_theme_path,
      'identifier' => $identifier,
    ),
  );
      
  /** Add YUI Library from YUI Open CDN; should we add this as a setting in the theme form? */
  drupal_add_js('http://yui.yahooapis.com/3.13.0/build/yui/yui-min.js', 'external', array('group' => JS_LIBRARY, 'weight' => -100 ));

  drupal_add_js($theme_path . '/js/crossframe.js', array('type' => 'file', 'scope' => 'footer', 'weight' => -100));

  drupal_add_js($theme_path . '/js/ui.crossframe.js', array('type' => 'file', 'scope' => 'footer', 'weight' => -100));
      
  drupal_add_js($js_data, 'setting');
}

function _dlts_map_navbar_item($variables = array()) {

  $parts = array(
    'html' => TRUE
  );

  if (isset($variables['fragment'])) {
    $parts = array_merge($parts, array( 'fragment' => $variables['fragment']));
  }

  if (isset($variables['attributes'])) {
    $parts = array_merge($parts, array('attributes' => $variables['attributes']));
  }

  if (isset($variables['query'])) {
    $parts = array_merge($parts, array('query' => $variables['query']));
  }

  return '<li class="navbar-item">'. l('<span>' . $variables['title'] . '</span>', $variables['path'], $parts) . '</li>';

}
