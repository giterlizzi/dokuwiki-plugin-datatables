<?php
/**
 * DataTables plugin: Add DataTables support to DokuWiki
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

class syntax_plugin_datatables extends DokuWiki_Syntax_Plugin {

    function getType(){ return 'container';}
    function getAllowedTypes() { return array('container'); }
    function getPType(){ return 'block';}
    function getSort(){ return 195; }

    function connectTo($mode) {
      $this->Lexer->addEntryPattern('<(?:DATATABLES|datatables|datatable).*?>(?=.*?</(?:DATATABLES|datatables|datatable)>)', $mode, 'plugin_datatables');
    }

    public function postConnect() {
      $this->Lexer->addExitPattern('</(?:DATATABLES|datatables|datatable)>', 'plugin_datatables');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {

      switch ($state) {
        case DOKU_LEXER_ENTER     : return array($state, $match);
        case DOKU_LEXER_UNMATCHED : return array($state, $match);
        case DOKU_LEXER_EXIT      : return array($state, $match);
      }

      return array();

    }

    function render($mode, Doku_Renderer $renderer, $data) {

      if (empty($data)) return false;

      if ($mode == 'xhtml') {

        /** @var Doku_Renderer_xhtml $renderer */

        list($state, $match) = $data;

        switch($state) {
          case DOKU_LEXER_ENTER:

            $html5_data = array();
            $xml = simplexml_load_string(str_replace('>', '/>', $match));

            foreach ($xml->attributes() as $key => $value) {
              $html5_data[] = sprintf("data-%s='%s'", $key, str_replace("'", "&apos;", (string) $value));
            }

            $renderer->doc .= sprintf('<div class="dt-wrapper" %s>', implode(' ', $html5_data));
            return true;

          case DOKU_LEXER_EXIT:
            $renderer->doc .= '</div>';
            return true;

          case DOKU_LEXER_UNMATCHED:
            $renderer->doc .= $match;
            return true;

        }

      }

    }

}
