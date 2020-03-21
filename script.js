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

function init_datatables($target_table, dt_config, tableName)
{
  var colSearchType = dt_config.colSearchType; //get dokuwiki DT plugin extended option
  var colSearchLoc = dt_config.colSearchLoc; //get dokuwiki DT plugin extended option

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

  			$target_table.attr('class', 'inline compact'); //make the table of styling inline (a DokuWiki table class for certain behaviors) and compact (reduce padding to shrink column size)
			
			if ( colSearchType == 'text')
			{
				if (colSearchLoc == 'top')
				{
					//make each column heading a text input box for filtering
					jQuery('#'+tableName+' thead th').each( function ()
					{
						var title = jQuery('#'+tableName+' thead th').eq(jQuery(this).index()).text(); //get column title to use as ghost text in the column header filter box
						var titleSize = title.length; //limit col size to make table more horizontally compact
						jQuery(this).html( '<input type="text" placeholder="'+title+'" size="'+titleSize+'">' );
					}); //.each

					var table = jQuery('#'+tableName).DataTable(dt_config); //get the DT object passing configuration settings. These were specified in the WikiPage <datatables options> tag
					//table is DT object reference so that DT API methods can be called
					table.columns().eq(0).each( function (colIdx)
					{ //set an event listener for each column in header for when enter key pressed before doing the filtering and redraw
						jQuery('input', table.column(colIdx).header()).on( 'keydown', function (ev)
						{
							if (ev.keyCode == 13)
							{
							table
							.column(colIdx)
							.search(this.value, true, false)
							.draw();
							}
						}); //.on keydown
						
						//don't sort when typing into the column filter input text box
						jQuery('input', table.column(colIdx).header()).on('click', function(e)
						{
							e.stopPropagation();
						}); //.on click
					}); //.each
				}
				else //col searching at the bottom (in footer) of DT table (default)
				{
					//make table footer
					var tabFoot = '';
					tabFoot = tabFoot + ('<tfoot>');
					tabFoot = tabFoot + ('<tr>');
					jQuery('#'+tableName+' thead th').each( function ()
					{
						var title = jQuery(this).text();
						var titleSize = title.length; //limit col size to make table more horizontally compact
						tabFoot = tabFoot + ('<th rowspan="1" colspan="1">');
						tabFoot = tabFoot + ('<input type="text" placeholder="'+title+'" size="'+titleSize+'">');
						tabFoot = tabFoot + ('</th>');
					});
					tabFoot = tabFoot + ('</tr>');
					tabFoot = tabFoot + ('</tfoot>');
					jQuery('#'+tableName).append(tabFoot);

					var table = jQuery('#'+tableName).DataTable(dt_config); //get the DT object passing configuration settings. These were specified in the WikiPage <datatables options> tag
					//table is DT object reference so that DT API methods can be called
					table.columns().every( function ()
					{ //set an event listener for every column in footer for when enter key pressed before doing the filtering and redraw
						var column = this;
						jQuery( 'input', this.footer() ).on( 'keydown', function (ev)
						{
							if (ev.keyCode == 13)
							{
							if ( column.search() !== this.value )
							{
								column
								.search( this.value, true, false ) //DT search. Args: regex text, treat text as regex, turn off smart search so it doesn't conflict w/regex. 4th arg optional - defaults to case-insensitive
								.draw();
							}
							}
						}); //.on
					}); //.every
				}
			} // search type text
			else if (colSearchType == 'list')
			{
				if (colSearchLoc == 'top')
				{

					var colTitles = jQuery('#'+tableName+' thead tr').clone(); //clone column header row
					colTitles.attr('class', 'row1'); //make it a different row name
					jQuery('#'+tableName+' thead').append(colTitles); //append in to TR recs in THEAD
					//row0 will have the column titles, row1 will have the dropdown lists

					var table = jQuery('#'+tableName).DataTable(dt_config); //get the DT object passing configuration settings. These were specified in the WikiPage <datatables options> tag
					//table is DT object reference so that DT API methods can be called
					table.columns().every( function ()
					{
						var column = this;
						var loc = jQuery(column.header());
						var select = jQuery('<select><option value=""></option></select>')
							.appendTo( loc.empty() )
							.on( 'change', function ()
							{
								var val = jQuery.fn.dataTable.util.escapeRegex(jQuery(this).val());
								column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
							});

						column.data().unique().sort().each( function (d, j)
						{
							var val = jQuery('<div/>').html(d).text(); //deal with links in the text
							select.append('<option value="'+val+'">'+val.substr(0,30)+'</option>'); //substr deals with extra long text in column that appears in dropdown list
							select.on('click', function(e) {e.stopPropagation();});
						});
					});
				}
				else
				{
					//make table footer
					var tabFoot = '';
					tabFoot = tabFoot + ('<tfoot>');
					tabFoot = tabFoot + ('<tr>');
					jQuery('#'+tableName+' thead th').each( function ()
					{
						var title = jQuery(this).text();
						var titleSize = title.length; //limit col size to make table more horizontally compact
						tabFoot = tabFoot + ('<th rowspan="1" colspan="1" size="'+titleSize+'">');
						tabFoot = tabFoot + ('</th>');
					});
					tabFoot = tabFoot + ('</tr>');
					tabFoot = tabFoot + ('</tfoot>');
					jQuery('#'+tableName).append(tabFoot);

					var table = jQuery('#'+tableName).DataTable(dt_config); //get the DT object passing configuration settings. These were specified in the WikiPage <datatables options> tag
					//table is DT object reference so that DT API methods can be called
					table.columns().every( function ()
					{
						var column = this;
						var loc = jQuery(column.footer());
						var select = jQuery('<select><option value=""></option></select>')
							.appendTo( loc.empty() )
							.on( 'change', function ()
							{
								var val = jQuery.fn.dataTable.util.escapeRegex(jQuery(this).val());
								column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
							});
						column.data().unique().sort().each( function (d, j)
						{
							var val = jQuery('<div/>').html(d).text(); //deal with links in the text
							select.append('<option value="'+val+'">'+val.substr(0,30)+'</option>'); //substr deals with extra long text in column that appears in dropdown list
						});
					});
				}
			} //search type list
			else
			{ //default non-column filtering
				$target_table.DataTable(dt_config);
			}
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

      var tableNum = 1;
    $wrap_tables.each(function() {

      var $target_table = jQuery(this),
          wrap_config   = jQuery(this).parents('.dt-wrapper').data(),
          dt_config     = jQuery.extend(wrap_config, JSINFO.plugin.datatables.config);

  	  var tableName = 'table'+tableNum; //make each DT table unique on the page
		init_datatables($target_table, dt_config, tableName);
		tableNum++;

    });

  }

}

});
