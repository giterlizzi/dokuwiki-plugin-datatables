/*!
 * DokuWiki DataTables Plugins
 *
 * Home     http://dokuwiki.org/template:bootstrap3
 * Author   Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * License  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

jQuery(document).ready(function() {

if (jQuery('.dt-wrapper table thead').length) {

  jQuery('.dt-wrapper').each(function() {
    jQuery(this).find('table').data(jQuery(this).data()).DataTable();
  });

}

});
