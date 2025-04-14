<?php
use Drupal\hbk_you_rema\Plugin\views\filter\CustomSelectDepth;

/**
 * Implements hook_views_data_alter().
 */
function hbk_you_rema_views_data_alter(&$data) {
  // \Stephane888\Debug\debugLog::$max_depth = 5;
  // \Stephane888\Debug\debugLog::symfonyDebug($data,
  // 'more_fields_views_data_alter', true);
  foreach ($data as $table_name => $table_data) {
    foreach ($table_data as $field_name => $field_data) {
      if (!empty($field_data['filter'])) {
        if ($field_data['filter']['id'] == 'taxonomy_index_tid') {
          if (!empty($data[$table_name][$field_name]['title'])) {
            $mf = [
              'title' => t('Custom select you'),
              'group' => 'hbk_you_custom',
              'filter' => $data[$table_name][$field_name]['filter']
            ];
            $mf['filter']['id'] = 'hbk_you_custom_custom_select';
            $mf['filter']['title'] = $data[$table_name][$field_name]['title'];
            $data[$table_name]['hbk_you_custom_' . $field_name] = $mf;
          }
        }
      }
    }
  }
  $data['node_field_data']['term_node_tid_depth_hbk'] = [
    'help' => t('Display content if it has the selected taxonomy terms, or children of the selected terms. Due to additional complexity, this has fewer options than the versions without depth.'),
    'real field' => 'nid',
    'argument' => [
      'title' => t('Has taxonomy term ID (with depth)'),
      'id' => 'taxonomy_index_tid_depth',
      'accept depth modifier' => TRUE
    ],
    'title' => t('Custom select you'),
    'group' => 'hbk_you_custom',
    'filter' => [
      'title' => t('Has taxonomy terms (with depth)'),
      'id' => 'hbk_you_custom_custom_select_depth'
    ]
  ];
}

/**
 * Implements hook_views_plugins_filter_alter().
 * ( This hook may be used to modify handler properties after they have been
 * specified by other modules )
 */
function hbk_you_rema_views_plugins_filter_alter(&$plugins) {
  if (!empty($plugins['taxonomy_index_tid_depth'])) {
    $plugins['taxonomy_index_tid_depth']['class'] = CustomSelectDepth::class;
  }
}