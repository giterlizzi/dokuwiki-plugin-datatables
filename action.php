<?php
/**
 * DataTables Action Plugin
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015, Giuseppe Di Terlizzi
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
    }

    /**
     * Add DataTables scripts and styles
     *
     * @param  Doku_Event  &$event
     */
    public function datatables(Doku_Event &$event, $param) {

        global $ID;
        global $conf;

        if ((bool) preg_match_all('/'.$this->getConf('excludedPages').'/', $ID)) {
          return false;
        }

        $base_path = dirname(__FILE__) . '/assets/datatables';
        $base_url  = DOKU_BASE . 'lib/plugins/datatables/assets/datatables';

        $datatables_lang   = sprintf('%s/plugins/i18n/%s.lang', $base_path, $conf['lang']);
        $datatables_config = array();

        $datatables_config['enableForAllTables'] = $this->getConf('enableForAllTables');

        $event->data['script'][] = array (
          'type' => 'text/javascript',
          'src'  => "$base_url/js/jquery.dataTables.min.js",
        );

        $event->data['link'][] = array (
          'type' => 'text/css',
          'rel'  => 'stylesheet',
          'href' => "$base_url/css/jquery.dataTables.min.css",
        );

        if (file_exists($datatables_lang) && $this->getConf('enableLocalization')) {
          $datatables_config['language'] = json_decode(preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '',
                                                       file_get_contents($datatables_lang)));
        }

        $event->data['script'][] = array (
          'type'  => 'text/javascript',
          '_data' => sprintf('if (typeof window.DATATABLES_CONFIG === "undefined") { window.DATATABLES_CONFIG = {}; } window.DATATABLES_CONFIG = %s;', json_encode($datatables_config))
        );

        //$event->data['script'][] = array (
        //  'type' => 'text/javascript',
        //  'src'  => '//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js',
        //);
        //
        //$event->data['link'][] = array (
        //  'type' => 'text/css',
        //  'rel'  => 'stylesheet',
        //  'href' => '//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css',
        //);

    }

}
