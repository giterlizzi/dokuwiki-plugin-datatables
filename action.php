<?php
/**
 * DataTables Action Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2020, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

/**
 * Class DataTables Plugin
 *
 * Add DataTables support to DokuWiki
 */
class action_plugin_datatables extends DokuWiki_Action_Plugin
{

    public $i18n = array(
        'da'          => 'Danish',
        'fr'          => 'French',
        'nl'          => 'Dutch',
        'hi'          => 'Hindi',
        'fo'          => 'Faroese',
        'el'          => 'Greek',
        'bg'          => 'Bulgarian',
        'pt-br'       => 'Brazilian',
        'ml'          => 'Malayalam',
        'cs'          => 'Czech',
        'hu'          => 'Hungarian',
        'sk'          => 'Slovak',
        'fi'          => 'Finnish',
        'de'          => 'German',
        'ja'          => 'Japanese',
        'lt'          => 'Lithuanian',
        'km'          => 'Khmer',
        'no'          => 'Norwegian',
        'sl'          => 'Slovenian',
        'ru'          => 'Russian',
        'lb'          => 'Luxembourgish',
        'pl'          => 'Polish',
        'af'          => 'Afrikaans',
        'th'          => 'Thai',
        'eo'          => 'Esperanto',
        'ka'          => 'Georgian',
        'et'          => 'Estonian',
        'it'          => 'Italian',
        'is'          => 'Icelandic',
        'ro'          => 'Romanian',
        'ta'          => 'Tamil',
        'ms'          => 'Malaysian',
        'az'          => 'Azerbaijani',
        'vi'          => 'Vietnamese',
        'lv'          => 'Latvian',
        'uk'          => 'Ukrainian',
        'cy'          => 'Welsh/UK',
        'id'          => 'Indonesian',
        'kk'          => 'Kazakh',
        'sv'          => 'Swedish',
        'de-informal' => 'German',
        'he'          => 'Hebrew',
        'zh-tw'       => 'Chinese',
        'zh'          => 'Chinese',
        'pt'          => 'Portuguese',
        'ko'          => 'Korean',
        'tr'          => 'Turkish',
        'sq'          => 'Albanian',
    );

    /**
     * Register events
     *
     * @param  Doku_Event_Handler  $controller
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'datatables');
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'jsinfo');
    }

    public function jsinfo(Doku_Event &$event, $param)
    {

        global $JSINFO;
        global $conf;

        $datatables_config = array();

        // DataTables global configuration
        $datatables_config['config'] = array();

        // Plugin configuration
        $datatables_config['enableForAllTables'] = $this->getConf('enableForAllTables');

        $asset_path = dirname(__FILE__) . '/assets/datatables.net-plugins/i18n';

        $datatables_lang = sprintf('%s/%s.lang', $asset_path, $this->i18n[$conf['lang']]);

        if (file_exists($datatables_lang)) {
            $datatables_config['config']['language'] = json_decode(
                preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '',
                    file_get_contents($datatables_lang)));
        }

        $JSINFO['plugin']['datatables'] = $datatables_config;

    }

    /**
     * Add DataTables scripts and styles
     *
     * @param  Doku_Event  &$event
     */
    public function datatables(Doku_Event &$event, $param)
    {

        global $ID;
        global $conf;
        global $JSINFO;

        $excluded_pages = $this->getConf('excludedPages');

        if (!empty($excluded_pages) && (bool) preg_match("/$excluded_pages/", $ID)) {
            return false;
        }

        $base_url = DOKU_BASE . 'lib/plugins/datatables/assets';

        $dt_scripts[] = "$base_url/datatables.net/js/jquery.dataTables.min.js";

        $dt_scripts[] = "$base_url/datatables.net-fixedheader-dt/js/fixedHeader.dataTables.min.js";
        $dt_styles[]  = "$base_url/datatables.net-fixedheader-dt/css/fixedHeader.dataTables.min.css";

        $dt_scripts[] = "$base_url/datatables.net-fixedcolumns-dt/js/fixedColumns.dataTables.min.js";
        $dt_styles[]  = "$base_url/datatables.net-fixedcolumns-dt/css/fixedColumns.dataTables.min.css";

        $dt_scripts[] = "$base_url/datatables.net-buttons/js/dataTables.buttons.min.js";
        $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.html5.min.js";
        $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.print.min.js";

        $dt_scripts[] = "$base_url/jszip/jszip.min.js";
        $dt_scripts[] = "$base_url/pdfmake/pdfmake.min.js";
        $dt_scripts[] = "$base_url/pdfmake/vfs_fonts.js";

        switch ($conf['template']) {
            case 'bootstrap3':
                $dt_scripts[] = "$base_url/datatables.net/js/dataTables.bootstrap.min.js";
                $dt_styles[]  = "$base_url/datatables.net/css/dataTables.bootstrap.min.css";
                $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.bootstrap.min.js";
                $dt_styles[]  = "$base_url/datatables.net-buttons/css/buttons.bootstrap.min.css";
                break;
            default:
                $dt_scripts[] = "$base_url/datatables.net/js/dataTables.jqueryui.min.js";
                $dt_styles[]  = "$base_url/datatables.net/css/dataTables.jqueryui.min.css";
                $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.jqueryui.min.js";
                $dt_styles[]  = "$base_url/datatables.net-buttons/css/buttons.jqueryui.min.css";
        }

        foreach ($dt_scripts as $script) {
            $event->data['script'][] = array(
                'type'  => 'text/javascript',
                'src'   => $script,
                'defer' => 'defer',
                '_data' => null,
            );
        }

        foreach ($dt_styles as $style) {
            $event->data['link'][] = array(
                'type' => 'text/css',
                'rel'  => 'stylesheet',
                'href' => $style,
            );
        }
    }
}
