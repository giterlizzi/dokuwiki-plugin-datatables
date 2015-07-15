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

var $all_tables  = jQuery('.mode_show table thead'),
    $wrap_tables = jQuery('.mode_show div.dt-wrapper table thead');

if (DATATABLES_CONFIG.enableForAllTables && $all_tables.length) {

  $all_tables.each(function() {

    var $target_table = jQuery(this);

    if (! $target_table.parents('.dt-wrapper').length) {
      $target_table.parent().DataTable(DATATABLES_CONFIG);
    }

  });

}

if ($wrap_tables.length) {

  $wrap_tables.each(function() {

    var $target_table = jQuery(this).parent(),
        wrap_config   = jQuery(this).parents('.dt-wrapper').data(),
        dt_config     = jQuery.extend(DATATABLES_CONFIG, wrap_config);

    $target_table.DataTable(dt_config);

  });

}

});
