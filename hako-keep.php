<?php
/**
 * 箱庭諸島 S.E - 島預かり管理用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

require_once 'config.php';
require_once MODELPATH.'/hako-cgi.php';
require_once MODELPATH.'/hako-file.php';
require_once VIEWPATH.'/hako-html.php';

$init = new Init();
$MAIN_FILE = $init->baseDir . "/hako-main.php";



class KP {
	public $mode;
	public $dataSet = array();

	function execute() {
		$html = new HTMLKP();
		$cgi = new Cgi();
		$hako =& new HakoKP();
		$this->parseInputData();
		$hako->init($this);
		$cgi->getCookies();
		$html->header($cgi->dataSet);

		switch($this->mode) {
			case "TOKP":
				if($this->passCheck()) {
					$this->toMode($this->dataSet['ISLANDID'], $hako);
					$hako->init($this);
				}
				$html->main($this->dataSet, $hako);
				break;

			case "FROMKP":
				if($this->passCheck()) {
					$this->fromMode($this->dataSet['ISLANDID'], $hako);
					$hako->init($this);
				}
				$html->main($this->dataSet, $hako);
				break;

			case "enter":
			default:
				if($this->passCheck()) {
					$html->main($this->dataSet, $hako);
				}
				break;
		}
		$html->footer();
	}

	function parseInputData() {
		$this->mode = isset($_POST['mode']) ? $_POST['mode'] : "";
		if(!empty($_POST)) {
			while(list($name, $value) = each($_POST)) {
				$value = str_replace(",", "", $value);
				$this->dataSet["{$name}"] = $value;
			}
		}
	}

	function toMode($id, &$hako) {
		global $init;

		if ($id) {
			$num = $hako->idToNumber[$id];
			if (!$hako->islands[$num]['keep']) {
				$hako->islands[$num]['keep'] = 1;
				$hako->islandNumberKP++;
				//require 'hako-turn.php';
				//Turn::islandSort($hako);
				$hako->writeIslandsFile();
			}
		}
	}

	function fromMode($id, &$hako) {
		global $init;

		if ($id) {
			$num = $hako->idToNumber[$id];
			if ($hako->islands[$num]['keep']) {
				$hako->islands[$num]['keep'] = 0;
				$hako->islandNumberKP--;
				//require 'hako-turn.php';
				//Turn::islandSort($hako);
				$hako->writeIslandsFile();
			}
		}
	}

	function passCheck() {
		global $init;

		if(file_exists("{$init->passwordFile}")) {
			$fp = fopen("{$init->passwordFile}", "r");
			$masterPassword = chop(fgets($fp, READ_LINE));
			fclose($fp);
		}
		if(strcmp(crypt($this->dataSet['PASSWORD'], 'ma'), $masterPassword) == 0) {
			return 1;
		} else {
			Util::makeTagMessage("パスワードが違います。", "danger");
			return 0;
		}
	}
}

$start = new KP();
$start->execute();
