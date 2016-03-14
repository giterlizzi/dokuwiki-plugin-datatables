/*!
 * DokuWiki DataTables Plugins
 *
 * Home      http://dokuwiki.org/template:bootstrap3
 * Author    Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * License   GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * Copyright (C) 2015-2016, Giuseppe Di Terlizzi
 */

jQuery(document).ready(function() {

var WRAP_TABLES_SELECTOR = '.page div.dt-wrapper table',
    ALL_TABLES_SELECTOR  = '.page table thead';

var $wrap_tables = jQuery(WRAP_TABLES_SELECTOR);

function init_datatables($target_table, dt_config) {

  var headerRows = dt_config.headerRows;
  if (headerRows) {

    var $thead = jQuery('thead', $target_table),
        $tbody = jQuery('tbody', $target_table),
        missingThead = $thead.size() === 0;

    headerRows -= $thead.children().size();

    if (missingThead) {
      $thead = jQuery('<thead>');
    }

    while(headerRows > 0) {
      headerRows--;
      $thead.append($tbody.children().first());
    }

    if (missingThead) {
      $target_table.prepend($thead);
    }

  }

  if (jQuery('thead > tr', $target_table).size() && ! jQuery('tbody', $target_table).find('[rowspan], [colspan]').length) {
    $target_table.attr('width', '100%');
    $target_table.DataTable(dt_config);
  }

}

if (   'plugin'     in JSINFO
    && 'datatables' in JSINFO.plugin) {

  if (JSINFO.plugin.datatables.enableForAllTables) {

    jQuery(ALL_TABLES_SELECTOR).each(function() {

      var $target_table = jQuery(this).parent();

      if (! $target_table.parents('.dt-wrapper').length) {
        init_datatables($target_table, JSINFO.plugin.datatables.config);
      }

    });

  }


  if ($wrap_tables.length) {

    $wrap_tables.each(function() {

      var $target_table = jQuery(this),
          wrap_config   = jQuery(this).parents('.dt-wrapper').data(),
          dt_config     = jQuery.extend(wrap_config, JSINFO.plugin.datatables.config);

      init_datatables($target_table, dt_config);

    });

  }

}

});
