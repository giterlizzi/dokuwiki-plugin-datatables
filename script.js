/* global JSINFO */

/**
 * DokuWiki DataTables Plugins
 *
 * Home      http://dokuwiki.org/template:bootstrap3
 * Author    Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * License   GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * Copyright (C) 2015-2020, Giuseppe Di Terlizzi
 */
jQuery(document).ready(function () {

    const WRAP_TABLES_SELECTOR = '.page div.dt-wrapper table';
    const ALL_TABLES_SELECTOR = '.page table thead';

    /**
     * Initialize DataTables on the given table
     *
     * @param {jQuery} $target_table
     * @param {object} dt_config DataTable configuration
     */
    function init_datatables($target_table, dt_config) {
        console.debug(dt_config);

        // adjust header rows if requested
        let headerRows = dt_config.headerRows;
        if (headerRows) {
            const $tbody = jQuery('tbody', $target_table);

            // if table heade ismissing, create it
            let $thead = jQuery('thead', $target_table);
            if ($thead.length === 0) {
                $thead = jQuery('<thead>');
                $target_table.prepend($thead);
            }

            // move the first rows from tbody to thead
            headerRows -= $thead.children().length;
            while (headerRows > 0) {
                headerRows--;
                $thead.append($tbody.children().first());
            }
        }

        // initialize, unless there are colspans or rowspans
        if (
            jQuery('thead > tr', $target_table).length &&
            !jQuery('tbody', $target_table).find('[rowspan], [colspan]').length
        ) {
            $target_table.attr('width', '100%');
            $target_table.DataTable(dt_config);
        }
    }

    // MAIN

    // check if plugin is configured
    if (!('plugin' in JSINFO) || !('datatables' in JSINFO.plugin)) return;

    // initialize on all tables, unless they have our wrapper (using default config)
    if (JSINFO.plugin.datatables.enableForAllTables) {
        jQuery(ALL_TABLES_SELECTOR).each(function () {
            const $target_table = jQuery(this).parent();

            if (!$target_table.parents('.dt-wrapper').length) {
                init_datatables($target_table, JSINFO.plugin.datatables.config);
            }
        });
    }

    // initialize on all tables with our wrapper (using default config + wrapper config)
    const $wrap_tables = jQuery(WRAP_TABLES_SELECTOR);
    if ($wrap_tables.length) {
        $wrap_tables.each(function () {
            const $target_table = jQuery(this);
            const wrap_config = jQuery(this).parents('.dt-wrapper').data();
            const dt_config = jQuery.extend(JSINFO.plugin.datatables.config, wrap_config);
            init_datatables($target_table, dt_config);
        });
    }
});
