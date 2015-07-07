<?php
/**
 * DataTables Action Plugin
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi>
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

        if ((bool) preg_match_all('/'.$this->getConf('excludedPages').'/', $ID)) {
          return false;
        }

        $event->data['script'][] = array (
          'type' => 'text/javascript',
          'src'  => '//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js',
        );

        $event->data['link'][] = array (
          'type' => 'text/css',
          'rel'  => 'stylesheet',
          'href' => '//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css',
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

