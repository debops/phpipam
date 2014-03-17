<?php
/*
 * Print graph of Top IPv4 / IPv6 hosts by percentage
 *
 * 		Inout must be IPv4 or IPv6!
 **********************************************/

/* required functions */
if(!function_exists('getSubnetStatsDashboard')) {
require_once( dirname(__FILE__) . '/../../../functions/functions.php' );
}

# no errors!
ini_set('display_errors', 0);

# set size parameters
$height = 200;
$slimit = 10;

# get widget parameters
$widget = getWidgetByFile($_REQUEST['subpage']);

# if direct request include plot JS 
if($_SERVER['HTTP_X_REQUESTED_WITH']!="XMLHttpRequest")	{ 
	# reset size and limit
	$height = 350;
	$slimit = 20;
	# include flot JS
	print '<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.js"></script>';
	print '<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.categories.js"></script>';
	print '<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot/excanvas.min.js"></script><![endif]-->';
	# and print title
	print "<div class='container'>";
	print "<h4 style='margin-top:40px;'>$widget[wtitle]</h4><hr>";
	print "</div>";
}

# get subnets statistic
$type = 'IPv6';
$subnetHost = getSubnetStatsDashboard($type, $slimit, false);

/* detect duplicates */
$unique = array();	
$numbering = array();													
$m = 0;
foreach($subnetHost as $line) {
	# check if already in array
	if(in_array($line['description'], $unique)) {
		$numbering[$line['description']]++;
		$subnetHost[$m]['description'] = $line['description'].' #'.$numbering[$line['description']];
	}
	$unique[] = $subnetHost[$m]['description'];
	$m++;
}
?>

<?php
# only print if some hosts exist
if(sizeof($subnetHost)>0) {
?>
<script type="text/javascript">
$(function () {
    
    var data = [
    <?php
	if(sizeof($subnetHost) > 0) {
		$m=0;
		foreach ($subnetHost as $subnet) {
			if($m < $slimit) {
				# verify user access
				$sp = checkSubnetPermission ($subnet['id']);
				if($sp != "0") {
					$subnet['subnet'] = transform2long($subnet['subnet']);
					$subnet['descriptionLong'] = $subnet['description'];
					# odd/even if more than 5 items
					if(sizeof($subnetHost) > 5) {
						if ($m&1) 	{ print "['|<br>$subnet[description]', $subnet[usage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}
						else		{ print "['$subnet[description]', $subnet[usage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}
					}
					else {
									{ print "['$subnet[description]', $subnet[usage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}			
					}	
					# next
					$m++;
				}
			}
		}
	}
	?>
	];

    //set JS array for clickable event
    <?php
    $allLinks = json_encode($subnetHost);
    echo "var all_links = ". $allLinks. ";\n";
    ?>

	//open link
	$('#<?php print $type; ?>top10Hosts').bind('plotclick', function(event, pos, item) {
		//get from array
		var plink = "subnets" + "/" + all_links[item.datapoint[0]]['sectionId'] + "/" + all_links[item.datapoint[0]]['id'] + "/";
		document.location = plink;
	});	

	//show tooltips
    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y - 35,
            left: x,
            border: '1px solid white',
            'border-radius': '4px',
            padding: '4px',
            'font-size': '11px',
            'background-color': 'rgba(0,0,0,0.7)',
            color: 'white'
        }).appendTo("body").fadeIn(500);
    }

    var previousPoint = null;
    $("#<?php print $type; ?>top10Hosts").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
                    
                    showTooltip(item.pageX, item.pageY,
                    			
                                data[x][2] + "<br>" + y + " hosts");
                }
                
                $("#<?php print $type; ?>top10Hosts").css({'cursor':'pointer'});
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;   
                $("#<?php print $type; ?>top10Hosts").css({'cursor':'default'});         
            }
        
    });
	
		var options = {
        series: {
            bars: {
                show: true,
                barWidth: 0.6,
                lineWidth: 1,
                align: "center",
                fillColor: "rgba(170, 70, 67, 0.8)"
            }
        },
        xaxis: {
            mode: "categories",
            tickLength: 0,
            color: '#666',
            tickLength: 1,
            show: true
        },
        yaxis: {
        },
        margin: {
	        top: 10,
	        left: 30,
	        bottom: 10,
	        right: 10
	    },
	    grid: {
		  	hoverable: true,
		  	clickable: true
	    },
	    bars: {
		    barWidth: 0.9
	    },
        legend: {
	        show: false
	    },
        shadowSize: 10,
        highlightColor: '#AA4643',
        colors: ['#AA4643' ],
        grid: {
	        show: true,
	        aboveData: false,
	        color: "#666",
	        backgroundColor: "white",
/*     margin: number or margin object */
/*     labelMargin: number */
/*     axisMargin: number */
/*     markings: array of markings or (fn: axes -> array of markings) */
    		borderWidth: 0,
    		borderColor: null,
    		minBorderMargin: null,
    		clickable: true,
    		hoverable: true,
    		autoHighlight: true,
    		mouseActiveRadius: 3
    		}
    };
    
    $.plot($("#<?php print $type; ?>top10Hosts"), [ data ], options);
});
</script>

<?php
}
else {
	print "<hr>";

	print "<blockquote style='margin-top:20px;margin-left:20px;'>";
	print "<p>"._("No $type hosts configured")."</p>";
	print "<small>"._("Add some hosts to subnets to show graph of used hosts per subnet")."</small>";
	print "</blockquote>";
	
	#remove loading
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		$("#IPv6top10Hosts").fadeOut('fast');
	});
	</script>
	<?php
}
?>

<div id="IPv6top10Hosts" class="top10" style="height: <?php print $height; ?>px; width: 95%; margin-left: 3%; padding: 0px; position: relative; ">
	<div style="text-align:center;padding-top:50px;"><strong><?php print _('Loading statistics'); ?></strong><br><i class='fa fa-spinner fa-spin'></i></div>
</div>