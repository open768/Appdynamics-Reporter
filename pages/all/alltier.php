<?php

/**************************************************************************
Copyright (C) Chicken Katsu 2013-2018 

This code is protected by copyright under the terms of the 
Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License
http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode

For licenses that allow for commercial use please contact cluck@chickenkatsu.co.uk

// USE AT YOUR OWN RISK - NO GUARANTEES OR ANY FORM ARE EITHER EXPRESSED OR IMPLIED
**************************************************************************/

//####################################################################
$home="../..";
require_once "$home/inc/common.php";
require_once "$root/inc/inc-charts.php";

//####################################################################
cRenderHtml::header("All Tiers");
cRender::force_login();
?>
	<script type="text/javascript" src="js/remote.js"></script>
	
<?php
cChart::do_header();

//####################################################################
cRender::show_time_options( "All Tiers"); 
	
//********************************************************************
if (cAppdyn::is_demo()){
	cRender::errorbox("function not support ed for Demo");
	cRenderHtml::footer();
	exit;
}
//********************************************************************
	

$aApps = cAppDynController::GET_Applications();
if (count($aApps) == 0) cRender::errorbox("No Applications found");

//####################################################################
foreach ( $aApps as $oApp){
	if (cFilter::isAppFilteredOut($oApp)) continue;
	$sAppQS = cRenderQS::get_base_app_QS($oApp);
	$sClass = cRender::getRowClass();
	?><DIV><?php
		cRenderMenus::show_app_functions($oApp);
		$aTiers =$oApp->GET_Tiers();
		$aMetrics = [];
		foreach ($aTiers as $oTier){ 
			if (cFilter::isTierFilteredOut($oTier)) continue;
			$sTierQs = cRenderQS::get_base_tier_QS( $oTier );
			$sUrl = "tier.php?$sTierQs";
			
			$aMetrics[] = [cChart::TYPE=>cChart::LABEL, cChart::LABEL=>$oTier->name];
			$aMetrics[] = [cChart::LABEL=>"calls: $oTier->name", cChart::METRIC=>cAppDynMetric::tierCallsPerMin($oTier->name), cChart::GO_HINT=>$oTier->name, cChart::GO_URL=>$sUrl];
			$aMetrics[] = [cChart::LABEL=>"Response: $oTier->name", cChart::METRIC=>cAppDynMetric::tierResponseTimes($oTier->name),cChart::GO_HINT=>$oTier->name, cChart::GO_URL=>$sUrl];
		}
		cChart::metrics_table($oApp,$aMetrics,3,$sClass,null,cChart::CHART_WIDTH_LETTERBOX/3);
	?></div><?php
}
cChart::do_footer();

cRenderHtml::footer();
?>
