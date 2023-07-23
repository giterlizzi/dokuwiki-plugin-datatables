<?php
/**
 * DataTables plugin: Add DataTables support to DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2016, Giuseppe Di Terlizzi
 */
class syntax_plugin_datatables extends DokuWiki_Syntax_Plugin
{

    /** @inheritdoc */
    public function getType()
    {
        return 'container';
    }

    /** @inheritdoc */
    public function getAllowedTypes()
    {
        return ['container', 'substition'];
    }

    /** @inheritdoc */
    public function getPType()
    {
        return 'block';
    }

    /** @inheritdoc */
    public function getSort()
    {
        return 195;
    }

    /** @inheritdoc */
    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern(
            '<(?:DATATABLES?|datatables?)\b.*?>(?=.*?</(?:DATATABLES?|datatables?)>)',
            $mode,
            'plugin_datatables'
        );
    }

    /** @inheritdoc */
    public function postConnect()
    {
        $this->Lexer->addExitPattern('</(?:DATATABLES?|datatables?)>', 'plugin_datatables');
    }

    /** @inheritdoc */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {

        switch ($state) {
            case DOKU_LEXER_UNMATCHED:
            case DOKU_LEXER_EXIT:
            case DOKU_LEXER_ENTER:
                return [$state, $match];
        }

        return [];
    }

    /** @inheritdoc */
    public function render($mode, Doku_Renderer $renderer, $data)
    {

        if (empty($data)) return false;
        if ($mode !== 'xhtml') return false;

        /** @var Doku_Renderer_xhtml $renderer */

        list($state, $match) = $data;

        switch ($state) {

            case DOKU_LEXER_ENTER:

                $html5_data = array();
                $xml = @simplexml_load_string(str_replace('>', '/>', $match));

                if (!is_object($xml)) {
                    $xml = simplexml_load_string('<foo />');

                    global $ACT;
                    if ($ACT == 'preview') {
                        msg(
                            sprintf(
                                '<strong>DataTable Plugin</strong> - Malformed tag (<code>%s</code>).' .
                                ' Please check your code!',
                                hsc($match)
                            ),
                            -1
                        );
                    }
                }

                foreach ($xml->attributes() as $key => $value) {
                    $html5_data[] = sprintf("data-%s='%s'", $key, str_replace("'", "&apos;", (string)$value));
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

        // should never be reached
        return false;
    }

}
