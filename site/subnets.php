<?php

/**
 * Script to print subnets from selected section
 *************************************************/
 
/* include functions */
/* if(!function_exists(CheckReferer)) { require_once(dirname(__FILE__) . '../functions/functions.php'); } */

/* verify that user is authenticated! */
isUserAuthenticated ();


/* get requested section and format it to nice output */
$sectionId = $_REQUEST['section'];

/* if it is not numeric than get ID from provided name */
if ( (!is_numeric($sectionId)) && ($sectionId != "Administration") ) {
    $sectionId = getSectionIdFromSectionName ($sectionId);
}

/**
 * Admin check, otherwise load requested subnets
 */
if ($sectionId == 'Administration')
{
    /* Print all Admin actions af user is admin :) */
    if (!checkAdmin()) {
        print '<div class="alert alert-danger">'._('Sorry, must be admin').'!</div>';
    }
    else {
        include('admin/adminMenu.php');
    }
}
else 
{    

	/* print subsections if they exist */
	$subsections = getAllSubSections($_REQUEST['section']);
	
	# permissions
	foreach($subsections as $k=>$ss) {
			$perm = checkSectionPermission ($ss['id']);
			if($perm > 0 ) 	{}
			else			{ unset($subsections[$k]); }	
	}
	
	if(sizeof($subsections)>0) {
		//title
		print "<h4>"._('Belonging subsections')."</h4><hr>";
	
		//table
		print "<table class='table table-noborder table-auto'>";
		
		foreach($subsections as $ss) {
			print "<tr>";
			print "	<td><i class='fa fa-gray fa-folder'></i> <a href='subnets/$ss[id]/' rel='tooltip' data-placement='right' title='$ss[description]'>$ss[name]</a></td>";
			print "</tr>";
		}
		
		print "</table>";
	}

	/* print Subnets */
	
    # get section name
    $sectionName = getSectionDetailsById ($sectionId);
    
    # verify permissions
	$sectionPermission = checkSectionPermission ($sectionId);
		
	if($sectionPermission == "0") { die("<div class='alert alert-danger'>"._('You do not have access to this section')."!</div>"); }
    
    # die if empty!
    if(sizeof($sectionName) == 0) { die('<div class="alert alert-danger">'._('Section does not exist').'!</div>'); }

    # header
    if(isset($_COOKIE['expandfolders'])) {
	    if($_COOKIE['expandfolders'] == "1")	{ $iconClass='fa-compress'; $action = 'open';}
	    else									{ $iconClass='fa-expand';  $action = 'close'; }
    }
    else 										{ $iconClass='fa-expand';  $action = 'close';}
    
    # Check if it has parent, and if so print back link
    if($sectionName['masterSection']!="0")	{
    	# get details
    	$mSection = getSectionDetailsById ($sectionName['masterSection']);
    	
	    print "<div class='subnets' style='padding-top:10px;'>";
	    print "	<a href='subnets/$mSection[id]/'><i class='fa fa-gray fa-angle-left fa-pad-left'></i> "._('Back to')." $mSection[name]</a><hr>";
	    print "</div>";
    }
    
    print "<h4>"._('Available subnets')." <span class='pull-right' style='margin-right:5px;cursor:pointer;'><i class='fa fa-gray fa-sm $iconClass' rel='tooltip' data-placement='bottom' title='"._('Expand/compress all folders')."' id='expandfolders' data-action='$action'></i></span></h4>";	
    print "<hr>";
	
	/* print subnets table ---------- */
	print "<div class='subnets'>";
	
	# print links
	$subnets2 = fetchSubnets ($sectionId);
	$menu = get_menu_html( $subnets2 );
	print $menu;
	
	print "</div>";						# end subnets overlay

	
	/* print VLANs */
	if($sectionName['showVLAN'] == 1) {
		$vlans = getAllVlansInSection ($sectionId);
	
		# if some is present
		if($vlans) {
			print "<div class='subnets'>";
				# title
				print "<hr><h4>"._('Available VLANs')."</h4><hr>";
				# create and print menu
				$menuVLAN = get_menu_vlan( $vlans, $sectionId );
				print($menuVLAN);
			print "</div>";	
		} 
	}


	/* print VRFs */
	if($settings['enableVRF']==1 && $sectionName['showVRF']==1) {
		$vrfs = getAllVrfsInSection ($sectionId);
		
		# if some is present
		if($vrfs) {
			print "<div class='subnets'>";
				# title
				print "<hr><h4>"._('Available VRFs')."</h4><hr>";
				# create and print menu
				$menuVRF = get_menu_vrf( $vrfs, $sectionId );
				print($menuVRF);
			print "</div>";	
		} 
	}
}

# add new subnet
$sectionPermission = checkSectionPermission ($sectionId);
if($sectionPermission == 3) {
	print "<div class='action'>";
	if(isset($_REQUEST['subnetId'])) {
	print "	<button class='btn btn-xs btn-default pull-left' id='hideSubnets' rel='tooltip' title='"._('Hide subnet list')."' data-placement='right'><i class='fa fa-gray fa-sm fa-chevron-left'></i></button>";
	}
	print "	<span>"._('Add new');
	print "	<div class='btn-group'>";
	print "	 <button id='add_subnet' class='btn btn-xs btn-default btn-success'  rel='tooltip' data-container='body'  data-placement='top' title='"._('Add new subnet to')." $sectionName[name]'  data-subnetId='' data-sectionId='$sectionName[id]' data-action='add'><i class='fa fa-sm fa-plus'></i></button>";
	print "	 <button id='add_folder' class='btn btn-xs btn-default btn-success'  rel='tooltip' data-container='body'  data-placement='top' title='"._('Add new folder to')." $sectionName[name]'  data-subnetId='' data-sectionId='$sectionName[id]' data-action='add'><i class='fa fa-sm fa-folder'></i></button>";
	print "	</div>";
	print "	</span>";
	print "</div>";
}