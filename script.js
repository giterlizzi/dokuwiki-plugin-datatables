/*!
 * DokuWiki DataTables Plugins
 *
 * Home      http://dokuwiki.org/template:bootstrap3
 * Author    Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * License   GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * Copyright (C) 2015-2016, Giuseppe Di Terlizzi
 */

jQuery(document).ready(function() {

var WRAP_TABLES_SELECTOR = '#dokuwiki__content div.dt-wrapper table',
    ALL_TABLES_SELECTOR  = '#dokuwiki__content table thead';

var $wrap_tables = jQuery(WRAP_TABLES_SELECTOR);

function init_datatables($target_table, dt_config) {
  // Build the thead if requested, before checking for thead existence and absence of (col|row)span in tbody.
  var headerRows = dt_config.headerRows;
  if (headerRows) {
    // Retrieve any already existing thead.
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


  if (jQuery('thead > tr', $target_table).size() && !jQuery('tbody', $target_table).find('[rowspan], [colspan]').length) {

    $target_table.DataTable(dt_config);

    if (dt_config.fixedHeaderEnable) {

      var options = {};

      jQuery.each(dt_config, function(key, value) {
        switch (key) {
          case 'fixedHeaderOffsetTop':
            options['offsetTop'] = value;
        }
      });

      new jQuery.fn.dataTable.FixedHeader($target_table, options);

    }
  }
}


if (JSINFO.plugin.datatables.enableForAllTables) {

  jQuery(ALL_TABLES_SELECTOR).each(function() {

    var $target_table = jQuery(this).parent();

    if (! $target_table.parents('.dt-wrapper').length) {
      init_datatables($target_table, JSINFO.plugin.datatables);
    }

  });

}


if ($wrap_tables.length) {

  $wrap_tables.each(function() {

    var $target_table = jQuery(this),
        wrap_config   = jQuery(this).parents('.dt-wrapper').data(),
        dt_config     = jQuery.extend(wrap_config, JSINFO.plugin.datatables);

    init_datatables($target_table, dt_config);

  });

}

});
