/**
 *
 * Javascript / jQuery functions
 *
 *
 */

$(document).ready(function () {

/* @general functions */

/*loading spinner functions */
function showSpinner() { $('div.loading').show(); }
function hideSpinner() { $('div.loading').fadeOut('fast'); }

/* hide error div if jquery loads ok
*********************************************/
$('div.jqueryError').hide();

/* Show / hide JS error */
function showError(errorText) {
	$('div.jqueryError').fadeIn('fast');
	if(errorText.length>0)  { $('.jqueryErrorText').html(errorText).show(); }
	hideSpinner();
}
function hideError() {
	$('.jqueryErrorText').html();
	$('div.jqueryError').fadeOut('fast');
}
//hide error popup
$(document).on("click", "#hideError", function() {
	hideError();
	return false;
});

/* tooltip hiding fix */
function hideTooltips() { $('.tooltip').hide(); }

/* popups */
function showPopup(pClass) {
    $('#popupOverlay').fadeIn('fast');
    $('.'+pClass).fadeIn('fast');
    $('body').addClass('stop-scrolling');        //disable page scrolling on bottom
}
function hidePopup(pClass) {
    $('.'+pClass).fadeOut('fast');
}
function hidePopups() {
    $('#popupOverlay').fadeOut('fast');
    $('.popup').fadeOut('fast');
    $('body').removeClass('stop-scrolling');        //enable scrolling back
    $('.popup_w700').css("z-index", "100");        //set popup back
    hideSpinner();
}
function hidePopup2() {
    $('.popup_w400').fadeOut('fast');
    $('.popup_w500').fadeOut('fast');
    $('.popup_w700').css("z-index", "100");        //set popup back
    hideSpinner();
}
$(document).on("click", "#popupOverlay, button.hidePopups", function() { hidePopups(); });
$(document).on("click", "button.hidePopup2", function() { hidePopup2(); });

//prevent loading for disabled buttons
$('a.disabled, button.disabled').click(function() { return false; });

//fix for menus on ipad
$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });

/*    generate random password */
function randomPass() {
    var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    var pass = "";
    var x;
    var i;
    for(x=0; x<10; x++) {
        i = Math.floor(Math.random() * 62);
        pass += chars.charAt(i);
    }
    return pass;
}

/* reload */
function reloadPage() {
	window.location.reload();
}

/* remove self on click */
$(document).on("click", ".selfDestruct", function() {
	$(this).parent('div').fadeOut('fast');
});


/* @cookies */
function createCookie(name,value,days) {
    var date;
    var expires;
    
    if (days) {
        date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires="+date.toGMTString();
    }
    else {
    }
    document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

/* draggeable elements */
$(function() {
	$(".popup").draggable({ handle: ".pHeader" });
});







/* @dashboard widgets ----------  */

//if dashboard show widgets
if($('#dashboard').length>0) {
	//get all boxes
	$('div[id^="w-"]').each(function(){
		var w = $(this).attr('id');
		//remove w-
		w = w.replace("w-", "");
		$.post('site/dashboard/widgets/'+w+'.php', function(data) {
			$("#w-"+w+' .hContent').html(data);
		}).fail(function(xhr, textStatus, errorThrown) {
			$("#w-"+w+' .hContent').html('<blockquote style="margin-top:20px;margin-left:20px;">File not found!</blockquote>');
		});
	});
}
//show add widget pupup
$(document).on('click','.add-new-widget',function() {
    showSpinner();
    
    $.post('site/dashboard/widgetPopup.php', function(data) {
	    $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });

	return false;
});
//remove item
$(document).on('click', "i.remove-widget", function() {
	$(this).parent().parent().fadeOut('fast').remove();
});
//add new widget form popup
$(document).on('click', '#sortablePopup li a.widget-add', function() {
	var wid   = $(this).attr('id');
	var wsize = $(this).attr('data-size');
	var wtitle= $(this).attr('data-htitle');
	//create
	var data = '<div class="row-fluid"><div class="span'+wsize+' widget-dash" id="'+wid+'"><div class="inner movable"><h4>'+wtitle+'</h4><div class="hContent"></div></div></div></div>';
	$('#dashboard').append(data);
	//load
	w = wid.replace("w-", "");
	$.post('site/dashboard/widgets/'+w+'.php', function(data) {
		$("#"+wid+' .hContent').html(data);
	}).fail(function(xhr, textStatus, errorThrown) {
		$("#"+wid+' .hContent').html('<blockquote style="margin-top:20px;margin-left:20px;">File not found!</blockquote>');
	});	
	//remove item
	$(this).parent().fadeOut('fast');
	
	return false;
});









/* @subnets list ----------  */

/* leftmenu toggle submenus */
// default hide
$('ul.submenu.submenu-close').hide();
// left menu folder delay tooltip
$('.icon-folder-close,.icon-folder-show, .icon-search').tooltip( {
    delay: {show:2000, hide:0}, 
    placement:"bottom"
});
// show submenus
$('ul#subnets').on("click", ".fa-folder-close-o", function() {
    //change icon
    $(this).removeClass('fa-folder-close-o').addClass('fa-folder-open-o');
    //find next submenu and hide it
    $(this).nextAll('.submenu').slideDown('fast');
});
$('ul#subnets').on("click", ".fa-folder", function() {
    //change icon
    $(this).removeClass('fa-folder').addClass('fa-folder-open');
    //find next submenu and hide it
    $(this).nextAll('.submenu').slideDown('fast');
});
// hide submenus
$('ul#subnets').on("click", ".fa-folder-open-o", function() {
    //change icon
    $(this).removeClass('fa-folder-open-o').addClass('fa-folder-close-o');
    //find next submenu and hide it
    $(this).nextAll('.submenu').slideUp('fast');
});
$('ul#subnets').on("click", ".fa-folder-open", function() {
    //change icon
    $(this).removeClass('fa-folder-open').addClass('fa-folder');
    //find next submenu and hide it
    $(this).nextAll('.submenu').slideUp('fast');
});


//hide subnets list
$('#hideSubnets').click(function() {
    $('#leftMenu').hide('fast');
    //expand content
    $('#content').css("width","97.9147%");
    return false;
});

//expand/contract all
$('#expandfolders').click(function() {
    // get action
    var action = $(this).attr('data-action');
    //open
    if(action == 'close') {
        $('.subnets ul#subnets li.folder > i').removeClass('fa-folder-close-o').addClass('fa-folder-open-o');
        $('.subnets ul#subnets li.folderF > i').removeClass('fa-folder').addClass('fa-folder-open');
        $('.subnets ul#subnets ul.submenu').removeClass('submenu-close').addClass('submenu-open').slideDown('fast');
        $(this).attr('data-action','open');
        createCookie('expandfolders','1','365');
        $(this).removeClass('fa-expand').addClass('fa-compress');
    }
    else {
        $('.subnets ul#subnets li.folder > i').addClass('fa-folder-close-o').removeClass('fa-folder-open-o');
        $('.subnets ul#subnets li.folderF > i').addClass('fa-folder').removeClass('fa-folder-open');
        $('.subnets ul#subnets ul.submenu').addClass('submenu-close').removeClass('submenu-open').slideUp('fast');
        $(this).attr('data-action','close');
        createCookie('expandfolders','0','365');
        $(this).removeClass('fa-compress').addClass('fa-expand');
    }
});










/* @ipaddress list ---------- */


/*    add / edit / delete IP address
****************************************/
//show form
$(document).on("click", ".modIPaddr", function() {
    showSpinner();        
    var action    = $(this).attr('data-action');
    var id        = $(this).attr('data-id');
    var subnetId  = $(this).attr('data-subnetId');
    var stopIP    = $(this).attr('data-stopIP');
    //format posted values
    var postdata = "action="+action+"&id="+id+"&subnetId="+subnetId+"&stopIP="+stopIP;
    $.post('site/ipaddr/modifyIpAddress.php', postdata, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//move orphaned IP address
$(document).on("click", "a.moveIPaddr", function() {
    showSpinner();        
    var action      = $(this).attr('data-action');
    var id        = $(this).attr('data-id');
    var subnetId  = $(this).attr('data-subnetId');
    //format posted values
    var postdata = "action="+action+"&id="+id+"&subnetId="+subnetId;
    $.post('site/ipaddr/moveIpAddress.php', postdata, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//resolve DNS name
$(document).on("click", "#refreshHostname", function() {
    showSpinner();
    var ipaddress = $('input.ip_addr').val();
    $.post('site/tools/resolveDNS.php', {ipaddress:ipaddress}, function(data) {
        if(data.length !== 0) {
            $('input[name=dns_name]').val(data);
        }
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});
//submit ip address change
$(document).on("click", "button#editIPAddressSubmit", function() {
    //show spinner
    showSpinner();
    var postdata = $('form.editipaddress').serialize();
    
    //replace delete if from visual
    if($(this).attr('data-action') == "all-delete" ) { postdata = postdata + '&action-visual=delete';}

    $.post('site/ipaddr/modifyIpAddressCheck.php', postdata, function(data) {
        $('div.addnew_check').html(data);
        $('div.addnew_check').slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//ping check
$(".ping_ipaddress").click(function() {
	showSpinner();
	var id       = $(this).attr('data-id');
	var subnetId = $(this).attr('data-subnetId');
	//check
	$.post('site/ipaddr/pingCheck.php', {id:id, subnetId:subnetId}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
		hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});


/*    send notification mail
********************************/
//show form
$('a.mail_ipaddress').click(function () {
    //get IP address id
    var IPid = $(this).attr('data-id');
    $.post('site/ipaddr/mailNotifyIP.php', { id:IPid }, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//send mail with IP details!
$(document).on("click", "#mailIPAddressSubmit", function() {
    showSpinner();
    var mailData = $('form#mailNotify').serialize();
    //post to check script
    $.post('site/ipaddr/mailNotifyCheck.php', mailData, function(data) {
        $('div.sendmail_check').html(data).slideDown('fast');
        //hide if success!
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){hidePopups();}, 1500); }
        else                             { hideSpinner(); }    
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});




/*    sort IP address list
*********************************************************/
$(document).on("click", "table.ipaddresses th a.sort", function() {
    showSpinner();
    
    $(this).tooltip('hide');                            //hide tooltips fix for ajax-load
    
    var direction = $(this).attr('data-id');            //sort direction
    var subnetId  = $(this).attr('data-subnetId');        //id of the subnet
    
    $.post('site/ipaddr/ipAddressPrintTable.php', {direction:direction, subnetId:subnetId}, function(data) {
        $('div.ipaddresses_overlay').html(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/*    scan subnet
*************************/
//open popup
$('a.scan_subnet').click(function() {
	showSpinner();
	var subnetId = $(this).attr('data-subnetId');
	$.post('site/ipaddr/scan/subnetScan.php', {subnetId:subnetId}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
		hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//show telnet port
$(document).on('change', "select#scanType", function() {
	var pingType = $('select[name=scanType]').find(":selected").val();
	if(pingType=="DiscoveryTelnet") { $('tbody#telnetPorts').show(); } 
	else 							{ $('tbody#telnetPorts').hide(); }
});

//start scanning
$(document).on('click','#subnetScanSubmit', function() {
	showSpinner();
	var subnetId = $(this).attr('data-subnetId');
	var pingType = $('select[name=scanType]').find(":selected").val();
	if($('input[name=debug]').is(':checked'))	{ var debug = 1; }
	else										{ var debug = 0; }
	var port     = $('input[name=telnetports]').val();
	$('#alert-scan').slideUp('fast');
	$.post('site/ipaddr/scan/subnetScan'+pingType+".php", {subnetId:subnetId, pingType:pingType, debug:debug, port:port}, function(data) {
        $('#subnetScanResult').html(data);
		hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//remove result
$(document).on('click', '.resultRemove', function() {
	var target = $(this).attr('data-target');
	$('tr.'+target).remove();
	return false;
});
//submit scanning result
$(document).on('click', 'a#saveScanResults', function() {
	showSpinner();
	var script   = $(this).attr('data-script');
	var subnetId = $(this).attr('data-subnetId');
	var postData = $('form.'+script+"Form").serialize();
	var postData = postData+"&subnetId="+subnetId;
	$.post('site/ipaddr/scan/subnetScan'+script+"Result.php", postData, function(data) {
        $('#subnetScanAddResult').html(data);
        //hide if success!
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});



/*    import IP addresses
*************************/
//load CSV import form
$('a.csvImport').click(function () {
    showSpinner();
    var subnetId = $(this).attr('data-subnetId');
    $.post('site/admin/CSVimport.php', {subnetId:subnetId}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//display uploaded file
$(document).on("click", "input#csvimportcheck", function() {
    showSpinner();
    //get filetype
    var filetype = $('span.fname').html();
    $.post('site/admin/CSVimportShowFile.php', { filetype : filetype }, function(data) {
        $('div.csvimportverify').html(data).slideDown('fast');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});
//import file script
$(document).on("click", "input#csvImportNo", function() {
    $('div.csvimportverify').hide('fast');
});
$(document).on("click", "input#csvImportYes", function() {
    showSpinner();
    //get filetype
    var filetype = $('span.fname').html();
    // get active subnet ID
    var xlsSubnetId  = $('a.csvImport').attr('data-subnetId');
    var postData = "subnetId=" + xlsSubnetId + "&filetype=" + filetype;

    $.post('site/admin/CSVimportSubmit.php', postData, function(data) {
        $('div.csvImportResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});
//donwload template
$(document).on("click", "#csvtemplate", function() {
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/csvtemplate.php'></iframe></div>");
	
	
	return false;
});


/*    export IP addresses
*************************/
//show fields
$('a.csvExport').click(function() {
    showSpinner();
    var subnetId = $(this).attr('data-subnetId');
    //show select fields
    $.post('site/ipaddr/exportSelectFields.php', {subnetId:subnetId}, function(data) {
	    $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//export
$(document).on("click", "button#exportSubnet", function() {
    var subnetId = $('a.csvExport').attr('data-subnetId');
    //get selected fields
    var exportFields = $('form#selectExportFields').serialize();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportSubnet.php?subnetId=" + subnetId + "&" + exportFields + "'></iframe></div>");
    return false;
});


/*	add / remove favourite subnet
*********************************/
$(document).on('click', 'a.editFavourite', function() {
	var subnetId = $(this).attr('data-subnetId');
	var action   = $(this).attr('data-action');
	var from     = $(this).attr('data-from');
	var item     = $(this);

	//remove
	$.post('site/tools/favouriteEdit.php', {subnetId:subnetId, action:action, from:from}, function(data) {
		//success - widget - remove item
		if(data=='success' && from=='widget') 	{ 
			$('tr.favSubnet-'+subnetId).addClass('error');
			$('tr.favSubnet-'+subnetId).delay(200).fadeOut(); 
		}
		//success - subnet - toggle star-empty
		else if (data=='success') 				{ 
			$(this).toggleClass('btn-info'); 
			$('a.favourite-'+subnetId+" i").toggleClass('fa-star-o'); 
			$(item).toggleClass('btn-info');
			//remove
			if(action=="remove") {
				$('a.favourite-'+subnetId).attr('data-original-title','Click to add to favourites');
				$(item).attr('data-action','add');
			}
			//add
			else {
				$('a.favourite-'+subnetId).attr('data-original-title','Click to remove from favourites');
				$(item).attr('data-action','remove');				
			}
		}
		//fail
		else {
	        $('div.popup_w500').html(data);
	        showPopup('popup_w500');
	        hideSpinner();			
		}
	}).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});


/*    request IP address for non-admins if locked or viewer
*********************************************************/
//show request form
$('a.request_ipaddress').click(function () {
    showSpinner();
    var subnetId  = $(this).attr('data-subnetId');
    $.post('site/tools/requestIPform.php', {subnetId:subnetId}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//auto-suggest first available IP in selected subnet
$(document).on("click", "select#subnetId", function() {
    showSpinner();
    var subnetId = $('select#subnetId option:selected').attr('value');
    //post it via json to requestIPfirstFree.php
    $.post('site/login/requestIPfirstFree.php', { subnetId:subnetId}, function(data) {
        $('input.ip_addr').val(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});

//submit request
$(document).on("click", "button#requestIPAddressSubmit", function() {
    showSpinner();
    var request = $('form#requestIP').serialize();
    $.post('site/login/requestIPresult.php', request, function(data) {
        $('div#requestIPresult').html(data).slideDown('fast');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});




//jump to page
$('select.jumptoPage').change(function() {
	var active    = $(this).find(":selected");
	var sectionId = active.attr('data-sectionId');
	var subnetId  = active.attr('data-subnetId');
    var page 	  = active.val(); 
    window.location.href = "subnets/"+sectionId+"/"+subnetId+"/"+page+"/";
});









/* @tools ----------- */


/* ipCalc */
//submit form
$('form#ipCalc').submit(function () {
    showSpinner();
    var ipCalcData = $(this).serialize();
    $.post('site/tools/ipCalcResult.php', ipCalcData, function(data) {
        $('div.ipCalcResult').html(data).fadeIn('fast');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//reset input
$('form#ipCalc input.reset').click(function () {
    $('form#ipCalc input[type="text"]').val('');
    $('div.ipCalcResult').fadeOut('fast');
});


/* search */
//submit form - topmenu
$('.searchSubmit').click(function () {
    showSpinner();
    var ip = $('.searchInput').val();
    //update search page
    window.location = "tools/search/" + ip;
    return false;
});
//submit form - topmenu
$('form#userMenuSearch').submit(function () {
    showSpinner();
    var ip = $('.searchInput').val();
    //update search page
    window.location = "tools/search/" + ip;
    return false;
});
//submit form
$('form#search').submit(function () {
    showSpinner();
    var ip = $('form#search .search').val();
    //update search page
    window.location = "tools/search/" + ip;
    return false;
});
//search export
$('a#exportSearch').click(function() {
    var searchTerm = $('form#search .search').val();
    $("div.dl").remove();                                                //remove old innerDiv
    $('div.exportDIVSearch').append("<div style='display:none' class='dl'><iframe src='site/tools/searchResultsExport.php?searchTerm=" + searchTerm + "'></iframe></div>");
    return false;
});

/* hosts */
$('#hosts').submit(function() {
    showSpinner();
    var hostname = $('input.hostsFilter').val();    
    window.location = "tools/hosts/"+hostname;
    return false;
});


/* user menu selfchange */
$('form#userModSelf').submit(function () {
    var selfdata = $(this).serialize(); 
    $('div.userModSelfResult').hide();
    
    $.post('site/tools/userMenuSelfMod.php', selfdata, function(data) {
        $('div.userModSelfResult').html(data).fadeIn('fast').delay(2000).fadeOut('slow');
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//    Generate random pass
$(document).on("click", "#randomPassSelf", function() {
    var password = randomPass();
    $('input.userPass').val(password);
    $('#userRandomPass').html( password );
    return false;
});

/* changelog */
//submit form
$('form#cform').submit(function () {
    showSpinner();
    var limit = $('form#cform .climit').val();
    var filter = $('form#cform .cfilter').val();
    //update search page
    window.location = "tools/changelog/"+filter+"/"+limit+"/";
    return false;
});


/*    sort device address list
*********************************************************/
$(document).on("click", "table#switchManagement th a.sort", function() {
    showSpinner();
    
    $(this).tooltip('hide');                            //hide tooltips fix for ajax-load
    
    var direction = $(this).attr('data-id');            //sort direction
    
    $.post('site/tools/devicesPrint.php', {direction:direction}, function(data) {
        $('div.devicePrintHolder').html(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
/* device filter */
$(document).on('submit', "#deviceFilter", function() {
	var searchData = $(this).serialize();	
    $.post('site/tools/devicesPrint.php', searchData, function(data) {
        $('div.devicePrintHolder').html(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    
    return false;
});








/* @administration ---------- */

/* save server settings */
$('#settings').submit(function() {
    showSpinner();
    var settings = $(this).serialize();
    //load submit results
    $.post('site/admin/settingsEdit.php', settings, function(data) {
        $('div.settingsEdit').html(data).slideDown('fast');
        //reload after 1 second if all is ok!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});

/* save mail settings */
$('#mailsettings').submit(function() {
    showSpinner();
    var settings = $(this).serialize();
    //load submit results
    $.post('site/admin/mailSettingsEdit.php', settings, function(data) {
        $('div.settingsMailEdit').html(data).slideDown('fast');
        //reload after 1 second if all is ok!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});

/* show/hide smtp body */
$('select#mtype').change(function() {
	var type = $(this).find(":selected").val();
	//if localhost hide, otherwise show
	if(type === "localhost") 	{ $('#mailsettingstbl tbody#smtp').hide(); } 
	else 						{ $('#mailsettingstbl tbody#smtp').show(); }
});

/* test mail */
$('.sendTestMail').click(function() {
    showSpinner();
    var settings = $('form#mailsettings').serialize();
   //send mail
    $.post('site/admin/mailSettingsTestMail.php', settings, function(data) {
        $('div.settingsMailEdit').html(data).slideDown('fast');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;	
});


/*    Edit users
***************************/
//open form
$('.editUser').click(function () {
    showSpinner();
    var id     = $(this).attr('data-userid');
    var action = $(this).attr('data-action');
    
    $.post('site/admin/usersEditPrint.php',{id:id, action:action}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//submit form
$(document).on("click", "#editUserSubmit", function() {
    showSpinner();
    var loginData = $('form#usersEdit').serialize();
    
    $.post('site/admin/usersEditResult.php', loginData, function(data) {
        $('div.usersEditResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//disable pass if domain user
$(document).on("change", "select#domainUser", function() {
    //get details - we need Section, network and subnet bitmask
    var type = $(this).val();
    //we changed to domain
    if(type == "1") { $('input.userPass').attr('disabled',''); }
    else             { $('input.userPass').removeAttr('disabled'); }
});
// generate random pass
$(document).on("click", "a#randomPass", function() {
    var password = randomPass();
    $('input.userPass').val(password);
    $(this).html( password );
    return false;
});
//search domain popup
$(document).on("click", ".adsearchuser", function() {
	$('div.popup_w500').load('site/admin/userADsearchForm.php');

    showPopup('popup_w500');
    $('.popup_w700').css("z-index", "99");        //set behind popup
    hideSpinner();
});
//search domain user result
$(document).on("click", "#adsearchusersubmit", function() {
	showSpinner();
	var dname = $('#dusername').val();
	$.post('site/admin/userADsearchResult.php', {dname:dname}, function(data) {
		$('div#adsearchuserresult').html(data)
		hideSpinner();
	});
});
//get user data from result
$(document).on("click", ".userselect", function() {
	var uname 	 = $(this).attr('data-uname');
	var username = $(this).attr('data-username');
	var email 	 = $(this).attr('data-email');
	//fill
	$('form#usersEdit input[name=real_name]').val(uname);
	$('form#usersEdit input[name=username]').val(username);
	$('form#usersEdit input[name=email]').val(email);
	
	hidePopup2();
	hidePopup('popup_w500');

	return false;
});



/*    Edit groups
***************************/
//open form
$('.editGroup').click(function () {
    showSpinner();
    var id     = $(this).attr('data-groupid');
    var action = $(this).attr('data-action');
    
    $.post('site/admin/groupEditPrint.php',{id:id, action:action}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//submit form
$(document).on("click", "#editGroupSubmit", function() {
    showSpinner();
    var loginData = $('form#groupEdit').serialize();
    
    $.post('site/admin/groupEditResult.php', loginData, function(data) {
        $('div.groupEditResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });    
    return false;
});
//add users to group - show form
$('.addToGroup').click(function() {
    showSpinner();
	var g_id = $(this).attr('data-groupid');	

    $.post('site/admin/groupAddUsers.php',{g_id:g_id}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });	
	return false;
});
//add users to group
$(document).on("click", "#groupAddUsersSubmit", function() {
	showSpinner();
	var users = $('#groupAddUsers').serialize();

    $.post('site/admin/groupAddUsersResult.php', users, function(data) {
        $('div.groupAddUsersResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });	
	return false;
});
//remove users frmo group - show form
$('.removeFromGroup').click(function() {
    showSpinner();
	var g_id = $(this).attr('data-groupid');	

    $.post('site/admin/groupRemoveUsers.php',{g_id:g_id}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });	
	return false;
});
//add users to group
$(document).on("click", "#groupRemoveUsersSubmit", function() {
	showSpinner();
	var users = $('#groupRemoveUsers').serialize();

    $.post('site/admin/groupRemoveUsersResult.php', users, function(data) {
        $('div.groupRemoveUsersResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });	
	return false;
});



/*    Edit AD settings
********************************/
$('form#ad').submit(function() {
    showSpinner();
    var addata = $(this).serialize();
    $.post('site/admin/manageADresult.php', addata, function(data) {
        $('div.manageADresult').html(data).slideDown('fast').delay(2000).fadeOut('slow');
            hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//check AD settings
$('#checkAD').click(function() {
    showSpinner();
    var addata = $('form#ad').serialize();
    $.post('site/admin/manageADcheck.php', addata, function(data) {
        $('div.manageADresult').html(data).slideDown('fast'); hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/*    instructions
***********************/
$('#instructionsForm').submit(function () {
	var instructions = CKEDITOR.instances.instructions.getData();
	$('div.instructionsPreview').hide('fast');
    
    showSpinner();
    $.post('site/admin/instructionsResult.php', {instructions:instructions}, function(data) {
        $('div.instructionsResult').html(data).fadeIn('fast');
        if(data.search("alert-danger") == -1)     	{ $('div.instructionsResult').delay(2000).fadeOut('slow'); hideSpinner(); }
        else                             	{ hideSpinner(); }      
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
$('#preview').click(function () {
    showSpinner();
    var instructions = CKEDITOR.instances.instructions.getData();

    $.post('site/admin/instructionsPreview.php', {instructions:instructions}, function(data) {
        $('div.instructionsPreview').html(data).fadeIn('fast');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/*    log files
************************/
//display log files - selection change
$('form#logs').change(function () {
    showSpinner();
    var logSelection = $('form#logs').serialize();
    $.post('site/admin/logResult.php', logSelection, function(data) {
        $('div.logs').html(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});
//log files show details
$(document).on("click", "a.openLogDetail", function() {
    var id = $(this).attr('data-logid');
    $.post('site/admin/logDetail.php', {id:id}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();        
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//log files page change
$('#logDirection button').click(function() {
    showSpinner();
    /* get severities */
    var logSelection = $('form#logs').serialize();
    /* get first or last id based on direction */
    var direction = $(this).attr('data-direction');
    /* get Id */
    var lastId;
    if (direction == "next")     { lastId = $('table#logs tr:last').attr('id'); }
    else                         { lastId = $('table#logs tr:nth-child(2)').attr('id'); }
    
    /* set complete post */
    var postData = logSelection + "&direction=" + direction + "&lastId=" + lastId;

    /* show logs */
    $.post('site/admin/logResult.php', postData, function(data1) {
        $('div.logs').html(data1);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;   
});
//logs export 
$('#downloadLogs').click(function() {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/logsExport.php'></iframe></div>");
    hideSpinner();
    //show downloading
    $('div.logs').prepend("<div class='alert alert-info' id='logsInfo'><i class='icon-remove icon-gray selfDestruct'></i> Preparing download... </div>");
    return false;
});
//logs clear
$('#clearLogs').click(function() {
    showSpinner();
    $.post('site/admin/logClear.php', function(data) {
    	$('div.logs').html(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});



/*    Sections
********************************/
//load edit form
$('button.editSection').click(function() {
    showSpinner();
    var sectionId   = $(this).attr('data-sectionid');
    var action         = $(this).attr('data-action');
    //load edit data
    $.post("site/admin/manageSectionEdit.php", {sectionId:sectionId, action:action}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});
//edit section result
$(document).on("click", "#editSectionSubmit", function() {
    showSpinner();
    var sectionData = $('form#sectionEdit').serialize();
    
    $.post('site/admin/manageSectionEditResult.php', sectionData, function(data) {
        $('div.sectionEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//section ordering
$('button.sectionOrder').click(function() {
    showSpinner();
    //load edit data
    $.post("site/admin/manageSectionOrder.php", function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//section ordering save
$(document).on("click", "#sectionOrderSubmit", function() {
    showSpinner();
	//get all ids that are checked
	var m = 0;
	var lis = $('#sortableSec li').map(function(i,n) {
	var pindex = $(this).index() +1;
		return $(n).attr('id')+":"+pindex;	
	}).get().join(';');
	
	//post
	$.post('site/admin/manageSectionOrderResult.php', {position: lis}, function(data) {
		$('.sectionOrderResult').html(data).fadeIn('fast');
		
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)	{ setTimeout(function (){window.location.reload();}, 1500); }
        else                            { hideSpinner(); hideSpinner(); }
        
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});


/*    Subnets
********************************/
//show subnets
$('table#manageSubnets button[id^="subnet-"]').click(function() {
    showSpinner();
    var swid = $(this).attr('id');                    //get id
    // change icon to down
    if( $('#content-'+swid).is(':visible') )     { $(this).children('i').removeClass('fa-angle-down').addClass('fa-angle-right'); }    //hide
    else                                         { $(this).children('i').removeClass('fa-angle-right').addClass('fa-angle-down'); }    //show
    //show content
    $('table#manageSubnets tbody#content-'+swid).slideToggle('fast');
    hideSpinner();
});
//toggle show all / none
$('#toggleAllSwitches').click(function() {
    showSpinner();
    // show
    if( $(this).children().hasClass('fa-compress') ) {
        $(this).children().removeClass('fa-compress').addClass('fa-expand');            //change icon
        $('table#manageSubnets i.fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-right');    //change section chevrons
        $('table#manageSubnets tbody[id^="content-subnet-"]').hide();                                //show content
        createCookie('showSubnets',0,30);                                                            //save cookie
    }
    //hide
    else {
        $(this).children().removeClass('fa-expand').addClass('fa-compress');
        $('table#manageSubnets tbody[id^="content-subnet-"]').show();    
        $('table#manageSubnets i.fa-angle-right').removeClass('fa-angle-right').addClass('fa-angle-down');    //change section chevrons    
        createCookie('showSubnets',1,30);                                                            //save cookie
    }
    hideSpinner();
});
//load edit form
$('button.editSubnet').click(function() {
    showSpinner();
    var sectionId   = $(this).attr('data-sectionid');
    var subnetId    = $(this).attr('data-subnetid');
    var action         = $(this).attr('data-action');
    //format posted values
    var postdata    = "sectionId=" + sectionId + "&subnetId=" + subnetId + "&action=" + action;
    
    //load edit data
    $.post("site/admin/manageSubnetEdit.php", postdata, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});
//resize / split subnet
$(document).on("click", "#resize, #split, #truncate", function() {
	showSpinner();
	var action = $(this).attr('id');
	var subnetId = $(this).attr('data-subnetId');
	//dimm and show popup2
    $.post("site/admin/manageSubnet"+action+".php", {action:action, subnetId:subnetId}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        $('.popup_w700').css("z-index", "99");        //set behind popup
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//resize save
$(document).on("click", "button#subnetResizeSubmit", function() {
	showSpinner();
	var resize = $('form#subnetResize').serialize();
	$.post("site/admin/manageSubnetResizeSave.php", resize, function(data) {
		$('div.subnetResizeResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//split save
$(document).on("click", "button#subnetSplitSubmit", function() {
	showSpinner();
	var split = $('form#subnetSplit').serialize();
	$.post("site/admin/manageSubnetSplitSave.php", split, function(data) {
		$('div.subnetSplitResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//truncate save
$(document).on("click", "button#subnetTruncateSubmit", function() {
	showSpinner();
	var subnetId = $(this).attr('data-subnetId');
	$.post("site/admin/manageSubnetTruncateSave.php", {subnetId:subnetId}, function(data) {
		$('div.subnetTruncateResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
$(document).on("submit", "#editSubnetDetails", function() {
	alert('test');
	hideSpinner();
	return false;
});
//save edit subnet changes
$(document).on("click", ".editSubnetSubmit", function() {
    showSpinner();
    var subnetData = $('form#editSubnetDetails').serialize();
        
    //if ipaddress and delete then change action!
    if($(this).hasClass("editSubnetSubmitDelete")) {
        subnetData = subnetData.replace("action=edit", "action=delete");
    }
    
    //load results
    $.post("site/admin/manageSubnetEditResult.php", subnetData, function(data) {
        $('div.manageSubnetEditResult').html(data).slideDown('fast');
        
        //reload after 2 seconds if all is ok!
        if(data.search("alert-danger") == -1) {
            showSpinner();
            var sectionId;
            var subnetId;
            var parameter;
            //reload IP address list if request came from there
            if(subnetData.search("IPaddresses") != -1) {
                //from ipcalc - load ip list
                sectionId = $('form#editSubnetDetails input[name=sectionId]').val();
                subnetId  = $('form#editSubnetDetails input[name=subnetId]').val();
                setTimeout(function (){window.location.reload();}, 1500);
            }
            //from free space 
            else if(subnetData.search("freespace") != -1) {
	            setTimeout(function (){window.location.reload();}, 1500);
            }
            //from ipcalc - ignore
            else if (subnetData.search("ipcalc") != -1) {
            }
            else {
                //from admin, reload
                setTimeout(function (){window.location.reload();}, 1500);
            }
        }
        //hide spinner - error
        else {
            hideSpinner();
        }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});

//get subnet info from ripe database
$(document).on("click", "#get-ripe", function() {
	showSpinner();
	var subnet = $('form#editSubnetDetails input[name=subnet]').val();
	
	$.getJSON("site/admin/manageSubnetEditRipeQuery.php", {subnet: subnet}, function(data) { 
		//fill fields
		dcnt = 0;
		$.each(data, function(key, val) {
			//error?
			if(key=="Error") {
		        $('div.popup_w500').html("<div class='pHeader'>Error</div><div class='pContent'><div class='alert alert-warning'>"+val+"</div></div><div class='pFooter'><button class='btn btn-sm btn-default hidePopup2'>Close</button></div>");
		        showPopup('popup_w500');
		        $('popup_w700').css("z-index","99");
				hideSpinner();
				
			} else {
				//check taht it exists
				if($('form#editSubnetDetails #field-'+key).length) {
					$('form#editSubnetDetails #field-'+key).val(val);
					dcnt++;
				}
			}
		});
		//if no hit print it!
		if(dcnt===0) {
			var ripenote = "<div class='pHeader'>Cannot find matched fileds!</div><div class='pContent'>";
			$.each(data, function(key, val) {
				ripenote += key+": "+val+"<br>";
			})
			ripenote += "</div><div class='pFooter'><button class='btn btn-sm btn-default hidePopup2'>Close</button></div>";

	        $('div.popup_w500').html(ripenote);
	        showPopup('popup_w500');
		}
		hideSpinner();
	}).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//change subnet permissions
$('.showSubnetPerm').click(function() {
	showSpinner();
	var subnetId  = $(this).attr('data-subnetId');
	var sectionId = $(this).attr('data-sectionId');
	
	$.post("site/admin/manageSubnetShowPermissions.php", {subnetId:subnetId, sectionId:sectionId}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
		hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});
//submit permission change
$(document).on("click", ".editSubnetPermissionsSubmit", function() {
	showSpinner();
	var perms = $('form#editSubnetPermissions').serialize();
	$.post('site/admin/manageSubnetPermissionsSubmit.php', perms, function(data) {
		$('.editSubnetPermissionsResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
	return false;
});


/*    Add subnet from IPCalc result
*********************************/
$(document).on("click", "#createSubnetFromCalc", function() {
    $('tr#selectSection').show();
});
$(document).on("change", "select#selectSectionfromIPCalc", function() {
    //get details - we need Section, network and subnet bitmask
    var sectionId = $(this).val();
    var subnet      = $('table.ipCalcResult td#sub2').html();
    var bitmask      = $('table.ipCalcResult td#sub4').html();
    var postdata  = "sectionId=" + sectionId + "&subnet=" + subnet + "&bitmask=" + bitmask + "&action=add&location=ipcalc";
    //make section active
    $('table.newSections ul#sections li#' + sectionId ).addClass('active');
    //load add Subnet form / popup
    $.post('site/admin/manageSubnetEdit.php', postdata , function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
});
$(document).on("click", ".createfromfree", function() {
    //get details - we need Section, network and subnet bitmask
    var sectionId = $(this).attr('data-sectionId');
    var cidr      = $(this).attr('data-cidr');
    var freespaceMSISD = $(this).attr('data-masterSubnetId');
    var cidrArr   = cidr.split('/');
    var subnet    = cidrArr[0];
    var bitmask   = cidrArr[1];
    var postdata  = "sectionId=" + sectionId + "&subnet=" + subnet + "&bitmask=" + bitmask + "&freespaceMSID=" + freespaceMSISD + "&action=add&location=ipcalc";
    //load add Subnet form / popup
    $.post('site/admin/manageSubnetEdit.php', postdata , function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});

/*    Edit subnet from ip address list
************************************/
$('.edit_subnet, button.edit_subnet, button#add_subnet').click(function () {
    var subnetId  = $(this).attr('data-subnetId');
    var sectionId = $(this).attr('data-sectionId');
    var action    = $(this).attr('data-action');

    //format posted values
    var postdata     = "sectionId="+sectionId+"&subnetId="+subnetId+"&action="+action+"&location=IPaddresses";
    //load add Subnet form / popup
    $.post('site/admin/manageSubnetEdit.php', postdata , function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/* Show add new VLAN on subnet add/edit on-thy-fly
***************************************************/
$(document).on("change", "select[name=vlanId]", function() {
    var vlanId    = $(this).val();
    if(vlanId == 'Add') {
        showSpinner();            
        $.post('site/admin/manageVLANEdit.php', {action:"add", fromSubnet:"true"}, function(data) {
            $('div.popup_w400').html(data);
            showPopup('popup_w400');
            $('.popup_w700').css("z-index", "99");        //set behind popup
            hideSpinner();
		}).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    }
    return false;    
});
//    Submit new VLAN on the fly
$(document).on("click", ".vlanManagementEditFromSubnetButton", function() {
    showSpinner();
    //get new vlan details
    var postData = $('form#vlanManagementEditFromSubnet').serialize();
	//add to save script
    $.post('site/admin/manageVLANEditResult.php', postData, function(data) {
        $('div.vlanManagementEditFromSubnetResult').html(data).show();
        // ok
        if(data.search("alert-danger") == -1) {
            var vlanId	  = $('#vlanidforonthefly').html();
            $.post('site/admin/manageSubnetEditPrintVlanDropdown.php', {vlanId:vlanId} , function(data) {
                $('.editSubnetDetails td#vlanDropdown').html(data);
                //bring to front
                $('.popup_w700').delay(1000).css("z-index", "101");        //bring to front
                hideSpinner();
			}).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
            //hide popup after 1 second
            setTimeout(function (){hidePopup('popup_w400'); parameter = null;}, 1000);
        }
        else                      { hideSpinner(); }
    });
    return false;    
});






/*	Folders
************************************/
//create new folder popup
$('#add_folder, .add_folder').click(function() {
	showSpinner();
    var subnetId  = $(this).attr('data-subnetId');
    var sectionId = $(this).attr('data-sectionId');
    var action    = $(this).attr('data-action');
    //format posted values
    var postdata     = "sectionId="+sectionId+"&subnetId="+subnetId+"&action="+action+"&location=IPaddresses";
    
    $.post('site/admin/manageFolderEdit.php', postdata, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
	}).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
 
    return false;    
});
//submit folder changes
$(document).on("click", ".editFolderSubmit", function() {
	showSpinner();
	var postData = $('form#editFolderDetails').serialize();
	$.post('site/admin/manageFolderEditSubmit.php', postData, function(data) {
		$('.manageFolderEditResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });	
	return false;
});
//delete folder
$(document).on("click", ".editFolderSubmitDelete", function() {
	showSpinner();
    var subnetId  = $(this).attr('data-subnetId');
    var description  = $('form#editFolderDetails #field-description').val();
    //format posted values
    var postData     = "subnetId="+subnetId+"&description="+description+"&action=delete";
	$.post('site/admin/manageFolderEditSubmit.php', postData, function(data) {
		$('.manageFolderEditResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });	
	return false;
});



/*    devices
********************************/
//open form
$('.editSwitch').click(function() {
    showSpinner();
    var switchId = $(this).attr('data-switchid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/manageDevicesEdit.php', {switchId:switchId, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//Edit switch result
$(document).on("click", "#editSwitchsubmit", function() {
    showSpinner();
    var switchdata = $('form#switchManagementEdit').serialize();
    $.post('site/admin/manageDevicesEditResult.php', switchdata, function(data) {
        $('div.switchManagementEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner();
        }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//open form
$('.editDevType').click(function() {
    showSpinner();
    var tid 	= $(this).attr('data-tid');
    var action  = $(this).attr('data-action');
    $.post('site/admin/manageDeviceTypeEdit.php', {tid:tid, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//Edit switch result
$(document).on("click", "#editDevTypeSubmit", function() {
    showSpinner();
    var switchdata = $('form#devTypeEdit').serialize();
    $.post('site/admin/manageDeviceTypeEditResult.php', switchdata, function(data) {
        $('div.devTypeEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner();
        }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/* VLAN
********************************/
//load edit form
$('.editVLAN').click(function() {
    showSpinner();
    var vlanId   = $(this).attr('data-vlanid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/manageVLANEdit.php', {vlanId:vlanId, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//result
$(document).on("click", "#editVLANsubmit", function() {
    showSpinner();
    var vlandata = $('form#vlanManagementEdit').serialize();
    $.post('site/admin/manageVLANEditResult.php', vlandata, function(data) {
        $('div.vlanManagementEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                               { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/*    VRF
*********/
//Load edit VRF form
$('button.vrfManagement').click(function() {
    showSpinner();
    var vrfId    = $(this).attr('data-vrfid');
    var action   = $(this).attr('data-action');
    var switchpost = "vrfId=" + vrfId + "&action=" + action;
    $.post('site/admin/manageVRFEdit.php', switchpost, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//Edit VRF details
$(document).on("click", "#editVRF", function() {
    showSpinner();
    var vrfdata = $('form#vrfManagementEdit').serialize();
    $.post('site/admin/manageVRFEditResult.php', vrfdata, function(data) {
        $('div.vrfManagementEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/*    edit IP request
***********************/
//show form
$(document).on("click",'table#requestedIPaddresses button', function() {
    showSpinner();
    var requestId = $(this).attr('data-requestid');
    $.post('site/admin/manageRequestEdit.php', { requestId: requestId }, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//approve / reject
$(document).on("click", "button.manageRequest", function() {
    showSpinner();
    var postValues = $('form.manageRequestEdit').serialize();
    var action     = $(this).attr('data-action');
    var postData   = postValues+"&action="+action;
    $.post('site/admin/manageRequestResult.php', postData, function(data) {
        $('div.manageRequestResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/*    Ripe AS import
****************************/
//get subnets form AS
$('form#ripeImport').submit(function() {
    showSpinner();
    var as = $(this).serialize();
    $.post('site/admin/ripeImportTelnet.php', as, function(data) {
        $('div.ripeImportTelnet').html(data).fadeIn('fast');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
// remove as line
$(document).on("click", "table.asImport .removeSubnet", function() {
    $(this).parent('tr').remove();
    hideTooltips();
});
// add selected to db
$(document).on("submit", "form#asImport", function() {
    showSpinner();
    //get subnets to add
    var importData = $(this).serialize();
    $.post('site/admin/ripeImportResult.php', importData, function(data) {
        $('div.ripeImportResult').html(data).slideDown('fast');
        //hide after 2 seconds
        if(data.search("alert-danger") == -1)     { $('table.asImport').delay(1000).fadeOut('fast'); hideSpinner(); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/*    set selected IP fields
********************************/
$('button#filterIPSave').click(function() {
    showSpinner();
    var addata = $('form#filterIP').serialize();
    $.post('site/admin/filterIPFieldsResult.php', addata, function(data) {
        $('div.filterIPResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { $('div.filterIPResult').delay(2000).fadeOut('slow');    hideSpinner(); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});




/*    custom fields - general
************************************/

//show edit form
$(document).on("click", ".edit-custom-field", function() {
    showSpinner();
    var action    = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    var table	  = $(this).attr('data-table');
    $.post('site/admin/customFieldsEdit.php',  {action:action, fieldName:fieldName, table:table}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//submit change
$(document).on("click", "#editcustomSubmit", function() {
    showSpinner();
    var field = $('form#editCustomFields').serialize();
    $.post('site/admin/customFieldsEditResult.php', field, function(data) {
        $('div.customEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//field reordering
$('table.customIP button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next     = $(this).attr('data-nextfieldname');
    var table	 = $(this).attr('data-table');
    $.post('site/admin/customFieldsOrder.php', {current:current, next:next, table:table}, function(data) {
        $('div.'+table+'-order-result').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});







/* Languages
*********/
//Load edit lang form
$('button.lang').click(function() {
    showSpinner();
    var langid    = $(this).attr('data-langid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/languageEdit.php', {langid:langid, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//Edit lang details
$(document).on("click", "#langEditSubmit", function() {
    showSpinner();
    var ldata = $('form#langEdit').serialize();
    $.post('site/admin/languageEditResult.php', ldata, function(data) {
        $('div.langEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     	{ setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/* Widgets
*********/
//Load edit widget form
$('button.wedit').click(function() {
    showSpinner();
    var wid    = $(this).attr('data-wid');
    var action = $(this).attr('data-action');
    $.post('site/admin/widgetEdit.php', {wid:wid, action:action}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//Edit widgets details
$(document).on("click", "#widgetEditSubmit", function() {
    showSpinner();
    var ldata = $('form#widgetEdit').serialize();
    $.post('site/admin/widgetEditResult.php', ldata, function(data) {
        $('div.widgetEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     	{ setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});



/* API
*********/
//Load edit API form
$('button.editAPI').click(function() {
    showSpinner();
    var appid    = $(this).attr('data-appid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/apiEdit.php', {appid:appid, action:action}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;    
});
//Edit API details
$(document).on("click", "#apiEditSubmit", function() {
    showSpinner();
    var apidata = $('form#apiEdit').serialize();
    $.post('site/admin/apiEditResult.php', apidata, function(data) {
        $('div.apiEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("alert-danger") == -1)     	{ setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});
//regenerate API key
$(document).on('click', "#regApiKey", function() {
	showSpinner();
    $.post('site/admin/apiKeyGenerate.php', function(data) {
        $('input#appcode').val(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});





/*    Search and replace
************************/
$('button#searchReplaceSave').click(function() {
    showSpinner();
    var searchData = $('form#searchReplace').serialize();    
    $.post('site/admin/searchReplaceResult.php', searchData, function(data) {
        $('div.searchReplaceResult').html(data);
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});


/* exports
***********************/
// XLS exports
$('button#XLSdump').click(function () {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateXLS.php'></iframe></div>");
    hideSpinner();
});
// MySQL export
$('button#MySQLdump').click(function () {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateMySQL.php'></iframe></div>");
    hideSpinner();
});
// Hostfile export
$('button#hostfileDump').click(function () {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateHostDump.php'></iframe></div>");
    hideSpinner();
});




/*	Fix database
***********************/
$(document).on('click', '.btn-tablefix', function() {
	var tableid = $(this).attr('data-tableid');
	var fieldid = $(this).attr('data-fieldid');
	var type 	= $(this).attr('data-type');
    $.post('site/admin/verifyDatabaseFix.php', {tableid:tableid, fieldid:fieldid, type:type}, function(data) {
        $('div#fix-result-'+tableid+fieldid).html(data).fadeIn('fast');
        hideSpinner();
    }).fail(function(jqxhr, textStatus, errorThrown) { showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: "+errorThrown); });
    return false;
});



return false;
});