<?php

require_once("controllers/supportcenter.php");

class SUPPORTCENTER_Cron extends OW_Cron
{
    public function __construct()
    {
        parent::__construct();

    }

    public function run()
    {
        $check = OW::getConfig()->getValue("supportcenter", "auto_purge");
        $check2 = OW::getConfig()->getValue("supportcenter", "purge_limit");
        if($check == "TRUE"){
            if($check2 == "1week"){
                SupportCenterDB::delete_tickets_by_1_week();
            }
            if($check2 == "3month"){
                SupportCenterDB::delete_tickets_by_3_months();
            }
            if($check2 == "6month"){
                SupportCenterDB::delete_tickets_by_6_months();
            }
        }
        if($check == "FALSE"){
            exit;
        }
    }


}