/*!
 * DokuWiki DataTables Plugins
 *
 * Home     http://dokuwiki.org/template:bootstrap3
 * Author   Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * License  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

jQuery(document).ready(function() {


if (typeof window.DATATABLES_CONFIG === 'undefined') {
  window.DATATABLES_CONFIG = {};
}

var WRAP_TABLES_SELECTOR = '.mode_show #dokuwiki__content div.dt-wrapper table thead',
    ALL_TABLES_SELECTOR  = '.mode_show #dokuwiki__content table thead';

var $wrap_tables = jQuery(WRAP_TABLES_SELECTOR);


function init_datatables($target_table, dt_config) {

  // Exclude all tables with {row,col}span
  if (! $target_table.find('[rowspan], [colspan]').length) {
    $target_table.DataTable(dt_config);
  }

}


if (DATATABLES_CONFIG.enableForAllTables) {

  jQuery(ALL_TABLES_SELECTOR).each(function() {

    var $target_table = jQuery(this).parent();

    if (! $target_table.parents('.dt-wrapper').length) {
      init_datatables($target_table, DATATABLES_CONFIG);
    }

  });

}


if ($wrap_tables.length) {

  $wrap_tables.each(function() {

    var $target_table = jQuery(this).parent(),
        wrap_config   = jQuery(this).parents('.dt-wrapper').data(),
        dt_config     = jQuery.extend(DATATABLES_CONFIG, wrap_config);

    init_datatables($target_table, dt_config);

  });

}


});
