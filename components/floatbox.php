<?php

class SUPPORTCENTER_CMP_Floatbox extends OW_Component
{
    public function __construct()
    {
        parent::__construct();

	
		
		
		
		
		

        $js = '$("#reload_button").click(function(){

                OW.loadComponent("SKELETON_CMP_Floatbox", {reload: true},
                    {
                      onReady: function( html ){
                         $("#supportcenter_floatbox_content").empty().html(html);

                      }
                    });
        });

        $("#close_button").click(function(){
            scAjaxFloatBox.close()
        });
        ';

        OW::getDocument()->addOnloadScript($js);

    }


}