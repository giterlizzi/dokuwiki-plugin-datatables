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

// Give a chance to create header on individual tables by not looking for thead for now.
var WRAP_TABLES_SELECTOR = '#dokuwiki__content div.dt-wrapper table',
    // The all-tables option still requires proper thead already set.
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
  
  // Make sure the table has a thead with at least 1 row and that no (col|row)span is used in tbody, because DataTables does not support this.
  if (jQuery('thead > tr', $target_table).size() && !jQuery('tbody', $target_table).find('[rowspan], [colspan]').length) {
    // Launch DataTable.
    $target_table.DataTable(dt_config);
    
    // Config is already available in dt_config parameter.
    // Moved creation of fixed header inside the conditional block, so that it happens only if the table is converted into a DataTable.
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

    var $target_table = jQuery(this), // We are already on the table (no longer on thead).
        wrap_config   = jQuery(this).parents('.dt-wrapper').data(),
        // General config should be 2nd object in order not to be modified.
        dt_config     = jQuery.extend(wrap_config, DATATABLES_CONFIG);

    init_datatables($target_table, dt_config);

  });

}


});
