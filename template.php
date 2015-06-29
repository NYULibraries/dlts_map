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
      'attributes' => array('data-title' => t('Metadata'), 'title' => t('Show/hide metadata'), 'class' => array('button', 'metadata'), 'id' => array('button-metadata')),
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

  $vars['pane_metadata_hidden'] = TRUE;

  $js_data = array(
    'map' => array(
      'path' => url('maps/' . $identifier, array('absolute' => TRUE )),
      'theme_path' => $absolute_theme_path,
      'identifier' => $identifier,
    ),
  );
      
  /** Add YUI Library from YUI Open CDN; should we add this as a setting in the theme form? */
  drupal_add_js('http://yui.yahooapis.com/3.18.1/build/yui/yui-min.js', 'external', array('group' => JS_LIBRARY, 'weight' => -100 ));

  drupal_add_js($theme_path . '/js/crossframe.js', array('type' => 'file', 'scope' => 'footer', 'weight' => -100));

  drupal_add_js($theme_path . '/js/ui.crossframe.js', array('type' => 'file', 'scope' => 'footer', 'weight' => -100));
      
  drupal_add_js($js_data, 'setting');
}


function dlts_map_preprocess_field(&$vars) {

  $language = 'en';

  $query_parameters = drupal_get_query_parameters();

  if (isset($query_parameters['lang'])) {
    $language = filter_xss($query_parameters['lang']);
  }

  // Sadly, translations for field labels was removed from Drupal 7.1, even
  // though the string translations are stored in the database, Drupal core
  // does not render them translated. Thus, we are forced to either install
  // i18n_fields module, or the less performance intensive solution: pass the
  // labels through the t() function in a preprocess function.
  //
  // See http://drupal.org/node/1169798, http://drupal.org/node/1157426,
  // and http://drupal.org/node/1157512 for more information.
  if (!empty($vars['label'])) {
    $vars['label'] = locale($vars['label'], $vars['element']['#field_name'] . ':' . $vars['element']['#bundle'] . ':label', $language);
  }

  if ($vars['element']['#field_name'] == 'field_pdf_file') {
    $vars['label'] = t('PDF');
    foreach ($vars['items'] as $key => $value) {
      if (isset( $value['#markup'])) {
        preg_match('/\/(.*)\/(.*){1}_(.*).pdf{1}/', $value['#markup'], $matches);
        if (isset($matches) && isset( $matches[3])) {
      if ($matches[3] == 'hi') {
        $pdf_link_text = t('High resolution');
          }
          else {
            $pdf_link_text = t('Low resolution');
          }

          $vars['items'][$key]['#markup'] = '<span class="field-item pdf-'. $matches[3] .'">' . l( $pdf_link_text, $value['#markup'], array('attributes' => array('target' => '_blank'))) . '</span>';

        }
      }
    }
  }

  if ($vars['element']['#field_name'] == 'field_language_code') {
    // Run the language code through dlts_book_language() to get a human readable language type from IA the language code
    // Label is changed in field--field-language-code--dlts-book.tpl.php
    $vars['items']['0']['#markup'] = dlts_book_language($vars['items']['0']['#markup'] );
  }

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
