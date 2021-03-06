<?php

require_once realpath(__DIR__.'/../../').'/config.php';
require_once MODELPATH.'hako-file.php';
require_once MODELPATH.'hako-cgi.php';

/**
 * 箱庭諸島 S.E
 * @author hiro <@hiro0218>
 */
class Main
{
    public function execute()
    {
        global $init;

        $hako = new \Hako;
        $cgi  = new \Cgi;

        $cgi->parseInputData();
        $cgi->getCookies();

        // 管理パスワード・データファイル存在確認
        if (!file_exists($init->passwordFile) || !$hako->readIslands($cgi)) {
            HTML::header();
            HakoError::noDataFile();
            println('<p><a href="./hako-mente.php">→初期設定</a></p>');
            HTML::footer();
            exit;
        }

        // ファイルロック失敗時、強制終了
        if (false === ($lock = Util::lock())) {
            exit;
        }

        $cgi->setCookies();

        if (strtolower($cgi->dataSet['DEVELOPEMODE'] ?? '') == 'javascript') {
            $html = new HtmlMapJS;
            $com  = new MakeJS;
        } else {
            $html = new HtmlMap;
            $com  = new Make;
        }
        switch ($cgi->mode) {
            case "log":
                $html = new HtmlTop;
                $html->header();
                $html->log();
                $html->footer();

                break;

            case "turn":
                $turn = new Turn;
                $html = new HtmlTop;
                $html->header();
                // ターン処理後、通常トップページ描画
                $turn->turnMain($hako, $cgi->dataSet);
                $html->main($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "owner":
                $html->header();
                $html->owner($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "command":
                $html->header();
                $com->commandMain($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "new":
                $html->header();
                $com->newIsland($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "comment":
                $html->header();
                $com->commentMain($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "print":
                $html->header();
                $html->visitor($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "targetView":
                $html->head();
                $html->printTarget($hako, $cgi->dataSet);
                //$html->footer();
                break;

            case "change":
                $html->header();
                $com->changeMain($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "ChangeOwnerName":
                $html->header();
                $com->changeOwnerName($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "conf":
                $html = new HtmlTop;
                $html->header();
                $html->register($hako, $cgi->dataSet);
                $html->footer();

                break;

            default:
                $html = new HtmlTop;
                $html->header();
                $html->main($hako, $cgi->dataSet);
                $html->footer();
        }
        Util::unlock($lock);
        exit;
    }
}
