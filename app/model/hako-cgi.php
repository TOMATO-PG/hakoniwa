<?php
/**
 * 箱庭諸島 S.E - Cookie定義用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */
class Cgi
{
    public $mode = '';
    public $dataSet = [];

    /**
     * POST、GETのデータを取得
     * @return void
     */
    public function parseInputData()
    {
        global $init;

        $this->mode = $_POST['mode'] ?? '';

        if (!empty($_POST)) {
            while (list($name, $value) = each($_POST)) {
                $this->dataSet[$name] = str_replace(",", "", $value);
            }
        }

        if (!empty($_GET['Sight'])) {
            $this->mode = "print";
            $this->dataSet['ISLANDID'] = $_GET['Sight'];
        }
        if (!empty($_GET['target'])) {
            $this->mode = "targetView";
            $this->dataSet['ISLANDID'] = $_GET['target'];
        }

        $getMode = (array_key_exists('mode', $_GET)) ? $_GET['mode'] : '';
        if ($getMode === "conf") {
            $this->mode = "conf";
        }
        if ($getMode === "log") {
            $this->mode = "log";
        }
        $init->adminMode = 0;
        if (empty($_GET['AdminButton'])) {
            $_password = $this->dataSet['PASSWORD'] ?? "";

            if (Util::checkPassword("", $_password)) {
                $init->adminMode = 1;
            }
        }
        // この段階でmodeにturnがセットされるのは不正アクセスの場合のみなのでクリアする
        $this->mode = ($this->mode != "turn") ? $this->mode : '';

        $this->dataSet['islandListStart'] = (!empty($_GET['islandListStart']))? $_GET['islandListStart'] : 1;
        $this->dataSet["ISLANDNAME"] = (isset($this->dataSet['ISLANDNAME']))? mb_substr($this->dataSet["ISLANDNAME"], 0, 16) : "";
        $this->dataSet["MESSAGE"] = (isset($this->dataSet['MESSAGE']))? mb_substr($this->dataSet["MESSAGE"], 0, 60) : "";
    }



    /**
     * COOKIEを取得
     * @return void
     */
    public function getCookies()
    {
        if (!empty($_COOKIE)) {
            while (list($name, $value) = each($_COOKIE)) {
                switch ($name) {
                    case "OWNISLANDID":
                        $this->dataSet['defaultID'] = $value;

                        break;

                    case "OWNISLANDPASSWORD":
                        $this->dataSet['defaultPassword'] = $value;

                        break;

                    case "TARGETISLANDID":
                        $this->dataSet['defaultTarget'] = $value;

                        break;

                    case "POINTX":
                        $this->dataSet['defaultX'] = $value;

                        break;

                    case "POINTY":
                        $this->dataSet['defaultY'] = $value;

                        break;

                    case "COMMAND":
                        $this->dataSet['defaultKind'] = $value;

                        break;

                    case "DEVELOPEMODE":
                        $this->dataSet['defaultDevelopeMode'] = $value;

                        break;

                    case "IMG":
                        $this->dataSet['defaultImg'] = $value;

                        break;
                }
            }
        }
    }

    /**
     * COOKIEを生成
     */
    public function setCookies()
    {
        $time = $_SERVER['REQUEST_TIME'] + 14 * 86400; // 現在から14日間有効

        // Cookieの設定 & POSTで入力されたデータで、Cookieから取得したデータを更新
        if (isset($this->dataSet['ISLANDID']) && $this->mode == "owner") {
            setcookie("OWNISLANDID", $this->dataSet['ISLANDID'], $time);
            $this->dataSet['defaultID'] = $this->dataSet['ISLANDID'];
        }
        if (isset($this->dataSet['PASSWORD'])) {
            setcookie("OWNISLANDPASSWORD", $this->dataSet['PASSWORD'], $time);
            $this->dataSet['defaultPassword'] = $this->dataSet['PASSWORD'];
        }
        if (isset($this->dataSet['TARGETID'])) {
            setcookie("TARGETISLANDID", $this->dataSet['TARGETID'], $time);
            $this->dataSet['defaultTarget'] = $this->dataSet['TARGETID'];
        }

        if (isset($this->dataSet['POINTX'])) {
            setcookie("POINTX", $this->dataSet['POINTX'], $time);
            $this->dataSet['defaultX'] = $this->dataSet['POINTX'];
        }
        if (isset($this->dataSet['POINTY'])) {
            setcookie("POINTY", $this->dataSet['POINTY'], $time);
            $this->dataSet['defaultY'] = $this->dataSet['POINTY'];
        }
        if (isset($this->dataSet['COMMAND'])) {
            setcookie("COMMAND", $this->dataSet['COMMAND'], $time);
            $this->dataSet['defaultKind'] = $this->dataSet['COMMAND'];
        }
        if (isset($this->dataSet['DEVELOPEMODE'])) {
            setcookie("DEVELOPEMODE", $this->dataSet['DEVELOPEMODE'], $time);
            $this->dataSet['defaultDevelopeMode'] = $this->dataSet['DEVELOPEMODE'];
        }
        if (isset($this->dataSet['IMG'])) {
            setcookie("IMG", $this->dataSet['IMG'], $time);
            $this->dataSet['defaultImg'] = $this->dataSet['IMG'];
        }
    }
}
