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
$home = "../..";
$root=realpath($home);
$phpinc = realpath("$root/../phpinc");
$jsinc = "$home/../jsinc";

require_once("$phpinc/ckinc/debug.php");
require_once("$phpinc/ckinc/session.php");
require_once("$phpinc/ckinc/common.php");
require_once("$phpinc/ckinc/header.php");
require_once("$phpinc/ckinc/http.php");
	
cSession::set_folder();
session_start();
cDebug::check_GET_or_POST();

//####################################################################
require_once("$phpinc/appdynamics/appdynamics.php");
require_once("$phpinc/appdynamics/common.php");
require_once("$root/inc/inc-charts.php");
require_once("$root/inc/inc-secret.php");
require_once("$root/inc/inc-render.php");


const COLUMNS=6;
error_reporting(E_ALL);

//display the results
$oApp = cRender::get_current_app();
$fromtier = cHeader::get(cRender::FROM_TIER_QS);
$tid = cHeader::get(cRender::TIER_ID_QS);
$totier = cHeader::get(cRender::TO_TIER_QS);
$gsAppQS = cRender::get_base_app_qs();
$oFromTier = cRender_make_tier_obj($oApp, $fromtier, $tid);
$gsTierQS = cRender::build_tier_qs($oApp, $oFromTier);


//####################################################################
cRender::html_header("External tier calls");
cRender::force_login();
cChart::do_header();

//####################################################################
$title =  "$oApp->name&gt;$fromtier&gt; to tier $totier";		
cRender::show_time_options($title); 
cRenderMenus::show_tier_functions(oFromTier);
cRender::button("back to ($fromtier) external tiers", cHttp::build_url("tierextgraph.php", $gsTierQS));
?>
<h2>Tier activity details<h2>
<h3>from (<?=$fromtier?>) to (<?=$totier?>)</h3>
<p>
<?php
//********************************************************************
if (cAppdyn::is_demo()){
	cRender::errorbox("function not support ed for Demo");
	cRender::html_footer();
	exit;
}
//********************************************************************
	
	$aMetrics=[];
	$sMetricUrl=cAppDynMetric::tierExtCallsPerMin($fromtier, $totier);
	$aMetrics[] = [cChart::LABEL=>"Calls per min from ($fromtier) to ($totier)", cChart::METRIC=>$sMetricUrl];
	$sMetricUrl=cAppDynMetric::tierExtResponseTimes($fromtier, $totier);
	$aMetrics[] = [cChart::LABEL=>"Response Times in ms from ($fromtier) to ($totier)", cChart::METRIC=>$sMetricUrl];
	cChart::metrics_table($oApp,$aMetrics,1,cRender::getRowClass());

//####################################################################
//################ CHART
cChart::do_footer();

cRender::html_footer();
?>