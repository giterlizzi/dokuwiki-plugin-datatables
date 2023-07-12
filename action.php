<?php

/**
 * DataTables Action Plugin
 *
 * Add DataTables support to DokuWiki
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2020, Giuseppe Di Terlizzi
 */
class action_plugin_datatables extends DokuWiki_Action_Plugin
{
    const ASSET_DIR = __DIR__ . '/assets';
    const ASSET_URL = DOKU_BASE . 'lib/plugins/datatables/assets';


    /**
     * Register events
     *
     * @param Doku_Event_Handler $controller
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'datatables');
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'jsinfo');
    }

    /**
     * Set config for DataTables in JSINFO
     *
     * @param Doku_Event $event DOKUWIKI_STARTED
     * @param mixed $param
     * @return void
     */
    public function jsinfo(Doku_Event $event, $param)
    {
        global $JSINFO;

        // default config
        $datatables_config = [
            'config' => [
                'dom' => 'lBfrtip'
            ],
            'enableForAllTables' => $this->getConf('enableForAllTables'),
        ];

        // find a matching language file
        foreach ($this->getLangPath() as $path) {
            if (file_exists(self::ASSET_DIR . '/' . $path)) {
                $datatables_config['config']['language']['url'] = self::ASSET_URL . '/' . $path;
                break;
            }
        }

        $JSINFO['plugin']['datatables'] = $datatables_config;
    }

    /**
     * Add DataTables scripts and styles
     *
     * @param Doku_Event $event TPL_METAHEADER_OUTPUT
     */
    public function datatables(Doku_Event $event, $param)
    {

        global $ID;
        global $conf;

        $excluded_pages = $this->getConf('excludedPages');

        if (!empty($excluded_pages) && (bool)preg_match("/$excluded_pages/", $ID)) {
            return;
        }

        $base_url = self::ASSET_URL;

        $dt_scripts[] = "$base_url/datatables.net/js/jquery.dataTables.min.js";

        $dt_scripts[] = "$base_url/datatables.net-fixedheader-dt/js/fixedHeader.dataTables.min.js";
        $dt_styles[] = "$base_url/datatables.net-fixedheader-dt/css/fixedHeader.dataTables.min.css";

        $dt_scripts[] = "$base_url/datatables.net-fixedcolumns-dt/js/fixedColumns.dataTables.min.js";
        $dt_styles[] = "$base_url/datatables.net-fixedcolumns-dt/css/fixedColumns.dataTables.min.css";

        $dt_scripts[] = "$base_url/datatables.net-buttons/js/dataTables.buttons.min.js";
        $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.html5.min.js";
        $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.print.min.js";

        $dt_scripts[] = "$base_url/jszip/jszip.min.js";
        $dt_scripts[] = "$base_url/pdfmake/pdfmake.min.js";
        $dt_scripts[] = "$base_url/pdfmake/vfs_fonts.js";

        switch ($conf['template']) {
            case 'bootstrap3':
                $dt_scripts[] = "$base_url/datatables.net/js/dataTables.bootstrap.min.js";
                $dt_styles[] = "$base_url/datatables.net/css/dataTables.bootstrap.min.css";
                $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.bootstrap.min.js";
                $dt_styles[] = "$base_url/datatables.net-buttons/css/buttons.bootstrap.min.css";
                break;
            default:
                $dt_scripts[] = "$base_url/datatables.net/js/dataTables.jqueryui.min.js";
                $dt_styles[] = "$base_url/datatables.net/css/dataTables.jqueryui.min.css";
                $dt_scripts[] = "$base_url/datatables.net-buttons/js/buttons.jqueryui.min.js";
                $dt_styles[] = "$base_url/datatables.net-buttons/css/buttons.jqueryui.min.css";
        }

        foreach ($dt_scripts as $script) {
            $event->data['script'][] = [
                'type' => 'text/javascript',
                'src' => $script,
                'defer' => 'defer',
                '_data' => null,
            ];
        }

        foreach ($dt_styles as $style) {
            $event->data['link'][] = [
                'type' => 'text/css',
                'rel' => 'stylesheet',
                'href' => $style,
            ];
        }
    }

    /**
     * Get possible language file paths for the current language
     *
     * @return Generator<string> Path relative to the asset locations
     */
    protected function getLangPath()
    {
        global $conf;
        [$lang, $dialect] = sexplode('-', $conf['lang'], 2);
        $dialect = strtoupper($dialect);

        // exact match with dialect
        if ($dialect) {
            yield "datatables.net-i18n/{$lang}-{$dialect}.json";
        }

        // exact match with language only
        yield "datatables.net-i18n/{$lang}.json";

        // fake dialect
        $dialect = strtoupper($lang);
        yield "datatables.net-i18n/{$lang}-{$dialect}.json";

        // any matching language
        $glob = self::ASSET_DIR . '/datatables.net-i18n/' . $lang . '*.json';
        $result = glob($glob);
        if ($result) {
            yield 'datatables.net-i18n/' . basename($result[0]);
        }
    }
}
