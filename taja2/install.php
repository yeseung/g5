<?php
include_once("./_common.php");
include_once("./config.php");

// 최고관리자만 실행 가능하게
if ($is_admin !== "super")
    die;

if(!sql_query(" DESCRIBE $taja_table ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `$taja_table` (
                  `no` int(11) NOT NULL AUTO_INCREMENT,
                  `mb_id` varchar(255) NOT NULL,
                  `taja_point` int(11) NOT NULL,
                  `taja_datetime` datetime NOT NULL,
                  PRIMARY KEY (`no`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", true);
}
goto_url('./index.php', false);
