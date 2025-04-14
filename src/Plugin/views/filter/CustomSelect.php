<?php

namespace Drupal\hbk_you_rema\Plugin\views\filter;

use Drupal\more_fields\Plugin\views\filter\MoreFieldsCheckboxList;
use Drupal\taxonomy\Plugin\views\filter\TaxonomyIndexTid;

/**
 * Filter by term id.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("hbk_you_custom_custom_select")
 */
class CustomSelect extends TaxonomyIndexTid {
}