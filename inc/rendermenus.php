<?php

/**************************************************************************
Copyright (C) Chicken Katsu 2013-2018 

This code is protected by copyright under the terms of the 
Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License
http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode

For licenses that allow for commercial use please contact cluck@chickenkatsu.co.uk

// USE AT YOUR OWN RISK - NO GUARANTEES OR ANY FORM ARE EITHER EXPRESSED OR IMPLIED
**************************************************************************/


//#######################################################################
//#######################################################################
class cRenderMenus{
	
	//******************************************************************************************
	// menus are populated in the footer() function in renderhtml.php
	//******************************************************************************************
	public static function show_app_functions($poApp=null){
		global $home;
		cDebug::enter();
		$oCred = cRenderObjs::get_appd_credentials();
		if ($oCred->restricted_login) {
			cRender::button($poApp->name,null);
			cDebug::leave();
			return;
		}
		
		if ($poApp == null) $poApp = cRenderObjs::get_current_app();
		?>
			<SELECT
				type="appdmenus" menu="appfunctions" 
				home="<?=$home?>"
				appname="<?=$poApp->name?>" appid="<?=$poApp->id?>">
			</SELECT>
		<?php
		cDebug::leave();
	}
	//******************************************************************************************
	public static function show_app_agent_menu($poApp = null){
		global $home;

		cDebug::enter();
		$oCred = cRenderObjs::get_appd_credentials();
		if ($oCred->restricted_login) {
			cDebug::leave();
			return;
		}
		
		if ($poApp == null) $poApp = cRenderObjs::get_current_app();
		?>
			<SELECT 
				type="appdmenus" menu="appagents" 				
				home="<?=$home?>"
				appname="<?=$poApp->name?>" appid="<?=$poApp->id?>">
			</SELECT>
		<?php
		cDebug::leave();
	}

	//******************************************************************************************
	public static function show_apps_menu($psCaption, $psURLFragment, $psExtraQS=""){
		global $home;
	
		cDebug::enter();
		$oCred = cRenderObjs::get_appd_credentials();
		if ($oCred->restricted_login) {
			cRender::button($psCaption,null);
			cDebug::leave();
			return;
		}
		
		$sApps_fragment = self::get_apps_fragment();

		?>
			<SELECT
				type="appdmenus" menu="appsmenu" 
				home="<?=$home?>"
				caption="<?=$psCaption?>" url="<?=$psURLFragment?>" 
				extra="<?=$psExtraQS?>" <?=$sApps_fragment?>>
			</SELECT>
		<?php
		self::show_app_functions();
		cDebug::leave();
	}
	
	//******************************************************************************************
	public static function show_tier_menu($psCaption, $psURLFragment, $psExtraQS=""){
		global $home;
		
		cDebug::enter();
		$oCred = cRenderObjs::get_appd_credentials();
		if ($oCred->restricted_login){
			cDebug::leave();
			return;
		}

		$oApp = cRenderObjs::get_current_app();
		
		try{
			$oTiers = $oApp->GET_Tiers();
		}
		catch (Exception $e)
		{
			cRender::errorbox("Oops unable to get tier data from controller");
			cDebug::leave();
			exit;
		}
		
		$sFragment = "";
		$iCount = 1;
		foreach ($oTiers as $oTier){
			$sFragment .= " tname.$iCount=\"".$oTier->name."\" tid.$iCount=\"$oTier->id\" ";
			$iCount++;
		}
		
		?>
			<SELECT 
				type="appdmenus" menu="tiermenu" 
				home="<?=$home?>"
				caption="<?=$psCaption?>" url="<?=$psURLFragment?>" 
				extra="<?=$psExtraQS?>" <?=$sFragment?>>
			</SELECT>
		<?php
		cDebug::leave();
	}
	
	//******************************************************************************************
	// this is a slightly different type of menu - so initialises differently
	public static function top_menu(){
		global $home;

		cDebug::enter();
		$oCred = cRenderObjs::get_appd_credentials();
		$sApps_attr = self::get_apps_attr();

		?>
			<script>
				function init_top_menu(){
					$oDiv = $("#<?=cRenderHtml::NAVIGATION_ID?>");
					$oDiv.attr({
						home:"<?=$home?>",
						controller:"<?=$oCred->host?>",
						restricted:"<?=$oCred->restricted_login?>",
						<?=$sApps_attr?>
					})
										
					//now trigger the rendering of the top menu
					cTopMenu.render( $oDiv);
				}
				
				$(init_top_menu);
			</script>
		<?php
		cDebug::leave();
	}
	
	//******************************************************************************************
	public static function show_tier_functions($poTier = null, $psNode=null){
		global $home;

		cDebug::enter();
		$oCred = cRenderObjs::get_appd_credentials();
	
		if ($oCred->restricted_login) {
			cRender::button($poTier->name,null);
			cDebug::leave();
			return;
		}
		if ($poTier == null){
			$poTier = cRenderObjs::get_current_tier();
		}
		?>
			<SELECT 
				type="appdmenus" menu="tierfunctions"  
				home="<?=$home?>"
				tier="<?=$poTier->name?>" tid="<?=$poTier->id?>" node="<?=$psNode?>">
			</SELECT>
		<?php
		cDebug::leave();
	}
	
	//******************************************************************************************
	public static function get_apps_attr(){

		cDebug::enter();
		try{
			$aApps = cAppDynController::GET_Applications();
		}
		catch (Exception $e)
		{
			cRender::errorbox("Oops unable to get application data from controller");
			cDebug::leave();
			exit;
		}
		uasort($aApps,"sort_by_app_name" );
		$iCount=0;
		$sfragment = "";
		foreach ($aApps as $oApp){
			$iCount++;
			if ($iCount > 1)$sfragment.=",";
			$sfragment.= "'appname.$iCount':\"".$oApp->name."\",'appid.$iCount':\"$oApp->id\"";
		}
		
		cDebug::leave();
		return $sfragment;
	}
	//******************************************************************************************
	public static function get_apps_fragment(){

		cDebug::enter();
		try{
			$aApps = cAppDynController::GET_Applications();
		}
		catch (Exception $e)
		{
			cRender::errorbox("Oops unable to get application data from controller");
			cDebug::leave();
			exit;
		}
		uasort($aApps,"sort_by_app_name" );
		$iCount=0;
		$sApps_fragment = "";
		foreach ($aApps as $oApp){
			$iCount++;
			$sApps_fragment.= "appname.$iCount =\"".$oApp->name."\" appid.$iCount=\"$oApp->id\" ";
		}
		
		cDebug::leave();
		return $sApps_fragment;
	}
}
?>
