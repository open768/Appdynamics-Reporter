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

//-----------------------------------------------
$oApp = cRenderObjs::get_current_app();
$sAppQS = cRenderQS::get_base_app_QS($oApp);


//####################################################################
cRenderHtml::header("Web browser - Real user monitoring");
cRender::force_login();
cChart::do_header();

$title ="$oApp->name&gt;Web Real User Monitoring";
cRender::show_time_options( $title); 

cRenderMenus::show_apps_menu("Show Web RUM for:", "apprum.php");
cRender::appdButton(cAppDynControllerUI::webrum($oApp));

//********************************************************************
if (cAppdyn::is_demo()){
	cRender::errorbox("function not support ed for Demo");
	cRenderHtml::footer();
	exit;
}
//********************************************************************

//####################################################################
?><h2>Overall Statistics</h2><?php
$aMetrics = [];
$aMetrics[] = [cChart::LABEL=>"Overall Calls per min",cChart::METRIC=>cAppDynMetric::appCallsPerMin()];
$aMetrics[] = [cChart::LABEL=>"Overall response time in ms", cChart::METRIC=>cAppDynMetric::appResponseTimes()];
cChart::metrics_table($oApp, $aMetrics,2,cRender::getRowClass());			

?><h2>Browser Stats for <?=cRender::show_name(cRender::NAME_APP,$oApp)?></h2><?php
cRender::button("Show Page Statistics", "../rum/rumstats.php?$sAppQS");
$aMetrics = [];
$aMetrics[] = [cChart::LABEL=>"Page requests per minute",cChart::METRIC=>cAppDynWebRumMetric::CallsPerMin()];
$aMetrics[] = [cChart::LABEL=>"Page response time",cChart::METRIC=>cAppDynWebRumMetric::ResponseTimes()];
$aMetrics[] = [cChart::LABEL=>"Page connection time",cChart::METRIC=>cAppDynWebRumMetric::TCPTime()];
$aMetrics[] = [cChart::LABEL=>"Page Server time",cChart::METRIC=>cAppDynWebRumMetric::ServerTime()];
$aMetrics[] = [cChart::LABEL=>"Page first byte time",cChart::METRIC=>cAppDynWebRumMetric::FirstByte()];
$sUrl="rumerrors.php?$sAppQS";
$aMetrics[] = [
	cChart::LABEL=>"JavaScript Errors",cChart::METRIC=>cAppDynWebRumMetric::JavaScriptErrors(), 
	cChart::GO_URL=>$sUrl, cChart::GO_HINT=>"javascript Errors"];

cChart::metrics_table($oApp, $aMetrics,2,cRender::getRowClass());			


cChart::do_footer();

cRenderHtml::footer();
?>
