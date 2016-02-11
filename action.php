<?php
/**
 * DataTables Action Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2016, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Class DataTables Plugin
 *
 * Add DataTables support to DokuWiki
 */
class action_plugin_datatables extends DokuWiki_Action_Plugin {

    /**
     * Register events
     *
     * @param  Doku_Event_Handler  $controller
     */
    public function register(Doku_Event_Handler $controller) {
      $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'datatables');
      $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'jsinfo');
    }


    public function jsinfo(Doku_Event &$event, $param) {

      global $JSINFO;
      global $conf;

      $datatables_config = array();
      $datatables_config['enableForAllTables'] = $this->getConf('enableForAllTables');

      $asset_path = dirname(__FILE__) . '/assets/datatables';

      $datatables_lang = sprintf('%s/plugins/i18n/%s.lang', $asset_path, $conf['lang']);

      if (file_exists($datatables_lang) && $this->getConf('enableLocalization')) {
        $datatables_config['language'] = json_decode(preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '',
                                                     file_get_contents($datatables_lang)));
      }

      $JSINFO['plugin']['datatables'] = $datatables_config;

    }

    /**
     * Add DataTables scripts and styles
     *
     * @param  Doku_Event  &$event
     */
    public function datatables(Doku_Event &$event, $param) {

        global $ID;
        global $conf;
        global $JSINFO;

        $excluded_pages = $this->getConf('excludedPages');

        if (! empty($excluded_pages) && (bool) preg_match("/$excluded_pages/", $ID)) {
          return false;
        }

        $base_path = dirname(__FILE__) . '/assets/datatables';
        $base_url  = DOKU_BASE . 'lib/plugins/datatables/assets/datatables';

        $event->data['script'][] = array (
          'type' => 'text/javascript',
          'src'  => "$base_url/js/jquery.dataTables.min.js",
        );

        $event->data['link'][] = array (
          'type' => 'text/css',
          'rel'  => 'stylesheet',
          'href' => "$base_url/css/jquery.dataTables.min.css",
        );

        $event->data['script'][] = array (
          'type' => 'text/javascript',
          'src'  => "$base_url/extensions/FixedHeader/js/dataTables.fixedHeader.min.js",
        );

        $event->data['link'][] = array (
          'type' => 'text/css',
          'rel'  => 'stylesheet',
          'href' => "$base_url/extensions/FixedHeader/css/dataTables.fixedHeader.min.css",
        );

    }

}
