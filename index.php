<!DOCTYPE html>
<html>
    <head>
        <title>AstroShelf v3</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="icon" href="favicon.ico" type="image/x-icon"/>
        <link rel="stylesheet" type="text/css" href="css/Aristo/Aristo.css" />
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery/jquery.contextMenu.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery/jquery.tagsinput.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery/datatables/jquery.dataTables.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery/datatables/extras/jquery.AutoFill.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery/datatables/extras/jquery.TableTools.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery/jquery.qtip.css" />
        <link rel="stylesheet" type="text/css" href="css/ui.notify.css">
		
		<!-- trend image progress bar -->
		<style>
			.ui-progressbar {
				position: relative;
			}
			.progress-label {
				position: absolute;
				left: 50%;
				top: 4px;
				font-weight: bold;
				text-shadow: 1px 1px 0 #fff;
			}
		</style>
		
        <!-- Plug-ins and external sources -->
		<script src="lib/jquery.min.js" type="text/javascript"></script>	  		
 		<script src="lib/jquery-ui.min.js" type="text/javascript"></script>
        <script src="lib/jquery.json-2.3.js" type="text/javascript"></script>
		<script src="lib/jquery.browser.js" type="text/javascript"></script>
        <script src="lib/contextMenu/jquery.contextMenu.js" type="text/javascript"></script>
        <script src="lib/contextMenu/jquery.ui.position.js" type="text/javascript"></script>
        <script src="lib/tagsInput/jquery.tagsinput.min.js" type="text/javascript"></script>
        <script src="lib/jquery.simple-color.js" type="text/javascript"></script>
        <script src="lib/jquery.jeditable.mini.js" type="text/javascript"></script>
        <script src="lib/jquery.tabSlideOut.v1.3.js" type="text/javascript"></script>
        <script src="lib/DataTables-1.9.1/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
		<script src="lib/DataTables-1.9.1/extras/TableTools/media/js/TableTools.min.js" type="text/javascript"></script>
		<script src="lib/DataTables-1.9.1/extras/AutoFill/media/js/AutoFill.min.js" type="text/javascript"></script>
		<script src="lib/DataTables-1.9.1/extras/Dates/moment.js" type="text/javascript"></script>
		<script src="lib/DataTables-1.9.1/extras/Dates/moment-plugin.js" type="text/javascript"></script>
        <script src="lib/qtip/jquery.qtip.js" type="text/javascript"></script>
		<script src="lib/livequery/jquery.livequery.js" type="text/javascript"></script>
		<script src="lib/jsend/jsend.min.js" type="text/javascript"></script>
		<script src="lib/ui.notify-ehynds/jquery.notify.js" type="text/javascript"></script>
        <script src="lib/MultiDatesPickerv1.6.1/jquery-ui.multidatespicker.js" type="text/javascript"></script>
		<script src="lib/URI.js" type="text/javascript"></script>
		<!--configuration and set-ups-->
        <script src="lib/init.js" type="text/javascript"></script>
        <script src="lib/userHandler.js" type="text/javascript"></script>
        <!-- For SNeT v0.2 -->
        <script src="lib/supernovaeExt/jquery.snet.funcs.js" type="text/javascript"></script>
        <script src="lib/supernovaeExt/supernova_config.js" type="text/javascript"></script>
        <script src="lib/supernovaeExt/jquery.supernova.js" type="text/javascript"></script>
        
        <script src="lib/raDecLib.js" type="text/javascript"></script>
        <script src="lib/validation.js" type="text/javascript"></script>      
        <script src="lib/REST.js" type="text/javascript"></script>
               
        <!-- For LiveAnnotations -->
        <script src="lib/liveannotations.js" type="text/javascript"></script>
        <script src="lib/jquery.notycounter.js" type="text/javascript"></script>
        <script src="lib/jquery.notylist.js" type="text/javascript"></script>
        <script src="lib/jquery.interest.js" type="text/javascript"></script>
        <script src="lib/jquery.interestlist.js" type="text/javascript"></script>
        <script src="lib/jquery.infobox.js" type="text/javascript"></script>
        <script src="lib/jquery.notification.js" type="text/javascript"></script>
		
        <!-- For Creating Annotations -->
		<script src="lib/contextMenu/contextGen.js" type="text/javascript"></script>
        <script src="lib/jquery.annotate.js" type="text/javascript"></script>
		
		<!-- For Creating Bookmarks -->
		<script src="lib/jquery.bookmarks_widget.js" type="text/javascript"></script>
		<script src="lib/jquery.bookmarks.js" type="text/javascript"></script>
		
        <!-- For Preferences -->
        <!--script src="lib/preferences/preferences.js" type="text/javascript"></script-->
        
		
        <!-- FOR SkyView -->
        <script type="text/javascript" src="lib/skyview/webgl/glMatrix-0.9.5.min.js"></script>
        <script type="text/javascript" src="lib/skyview/webgl/webgl-utils.js"></script>
        <script type="text/javascript" src="lib/skyview/webgl/webgl-debug.js"></script>
        <script type="text/javascript" src="lib/skyview/webgl/GLU.js"></script>
        <script type="text/javascript" src="lib/skyview/CustomOverlay.js"></script>

        <script type="text/javascript" src="lib/skyview/Math.js"></script>
        <script type="text/javascript" src="lib/skyview/WebGL.js"></script>
		<script type="text/javascript" src="lib/skyview/Config.js"></script>
        <script type="text/javascript" src="lib/skyview/Overlay.js"></script>
        <script type="text/javascript" src="lib/skyview/Projection.js"></script>   
		<script type="text/javascript" src="lib/skyview/locator.js"></script>

        <script type="text/javascript" src="lib/skyview/SkyView.js"></script>
		
		<!-- FOR Trend Images -->
				
		<!-- JS -->        
		<script type="text/javascript" src="lib/trend/jQuery_upload/js/jquery.bt.js"></script>
		<script type="text/javascript" src="lib/trend/jQuery_upload/js/jquery.hoverIntent.minified.js"></script>		
		
        <script type="text/javascript" src="lib/trend/js/WebGL2D.js"></script>
		<script type="text/javascript" src="lib/trend/js/jcanvas.min.js"></script> 
        <script type="text/javascript" src="lib/trend/js/Trend.js"></script>
		
		<!-- End Trend -->
		
		<!-- FOR Thumbnails -->
		<script type="text/javascript" src="lib/thumbnail/thumbnails.js"></script>
		<script type="text/javascript" src="lib/thumbnail/thumbContextMenu.js"></script>
		
		<!-- Pre-Processing Functions for search/result tabs -->
		<script type="text/javascript" src="lib/pre_funcs_tabs.js"></script>
    
        <!-- For Search Objects -->
        <script src="lib/jquery.search_obj.guided.js" type="text/javascript"></script>
		<script src="lib/jquery.search_obj.directsql.js" type="text/javascript"></script>
		
		<!-- For Search Annotations -->
		<script src="lib/jquery.search_anno.js" type="text/javascript"></script>
		
		<!-- For Results -->
		<script src="lib/jquery.result.js" type="text/javascript"></script>
		
		<!-- For Object details -->
		<script src="lib/jquery.obj_details.js" type="text/javascript"></script>

		<!-- SCRIPT ADDED TO AVOID PAGE REFRESH -->
		<script type="text/javascript">
			window.onbeforeunload = function(){
				return "Page is ready to refresh";
			}
			window.onunload = function() {
				if(isUserLoggedIn()) {
					doLogout();
				}
			}
		</script>
		
		
		<script type="text/javascript">
			var skyView;
			var SDSSOverlay;
			var FIRSTOverlay;
			var LSSTOverlay;
			var overlaysList = new Array(); 
			
			jQuery(document).ready(function($){
				
				var skyViewHeight = window.innerHeight - $('#topSection').height();
				
				initSkyView(skyViewHeight);
				initLocator();
				
				//hover to show tabs	
				initTabs(skyViewHeight);
				
				initCrosshairButton();
				initSupernovaeExt();
				initCounter();
				//construct instance of annotateDialog
				$("#annotate_dialog").annotateDialog();
				$("#bookmark_dialog").bookmarkDialog();
				initUser();
				initGroupManagementButton();
				
				//For Search Objects tab, global variable
				restab_base = {obj_base: 0, anno_base: 0};
				$.fn.search_obj_guided();
				$.fn.search_obj_directsql();
				//For Results tab
				$.fn.result();
				//For Bookmarks tab
				$.fn.bookmarks();
				
				$("#search_obj_tabs, #result_tabs, #trend_tabs, #bookmarks_tabs, #annotations_tabs").tabs({
					fx: { opacity: 'toggle' },
					select: function(event, ui) {
						jQuery(this).css('height', jQuery(this).height());
						jQuery(this).css('overflow', 'hidden');
					},
					show: function(event, ui) {
						jQuery(this).css('height', 'auto');
						jQuery(this).css('overflow', 'visible');
					}
				});
				//For Search Annotations tab
				$.fn.search_anno();
				//For Overlays tab
				initOverlayTab();
				//For Results tab, global variable
				restab_tables = {};
				
				$("#thumbnail_canvas").hide();
				
				//For Object details tab
				$.fn.obj_details();
			});
		</script>
    </head>
	
    <body class="ui-form" onkeypress="skyView.keyPressed(event); if(TrendImages != null) TrendImages[0].key(event);">

        <div class="logoDiv"><img class="logoImage" src="css/images/pittLogo.png"></div>
        <div class="nsfLogoDiv"><img class="nsfImage" src="css/images/NSF_Logo.PNG"></div>

        <div id="topSection">
            <div id="titleDiv" class="titleDiv">
                <div>
                    <div class="titleTxt">AstroShelf</div><br/>
                    <div class="urlTitleTxt"><a class="urlTitleTxt" target="_blank" href="http://astro.cs.pitt.edu">astro.cs.pitt.edu</a></div>
                </div> 
            </div>
            
            <span id="toolbar" class="ui-widget-header ui-corner-all">
                <span id="username"></span>
				<button id="crosshairs"></button>
                <button id="loginout"></button>
                <button id="supernovae"></button>
                <!--button id="counter"></button-->
            </span>
        </div>

        <div id="skyPanelDiv" style="position:relative;" class="skyPanel-context-menu">
			<img src="./lib/skyview/temp.png" style="display:none;" id="temp">
		</div>
		
        <div id="coordinates">
            <table>
				<tr>
                    <td>RA,Dec (degrees): <span id="RA-Dec" class="editable">0,0</span></td>
					<td><button id="bookmark_current_loc" class="tiny_button"><span class="ui-button-icon-primary ui-icon ui-icon-bookmark"></span></button></td>
					<td>-- Scale (arcsec/pixel): <span id="Scale" class="editable">0</span></td>
				</tr>
			</table>
        </div>
		
		<!-------------- Supernovae v0.1 Dialog -------------->
		<div id="supernovae_dialog" title="SNeT v0.2">
				<ul id="supernovaTabs">
					<li class="selectedSupernovaTab"><a href="#searchSupernova"><span>Search/ Browse</span></a></li>
					<li><a href="#searchResultsSupernova"><span>Search Results</span></a></li>
					<li><a href="#yourListsSupernova"><span>List Management</span></a></li>
					<li><a href="#scheduleSupernova"><span>Scheduler</span></a></li>
					<li><a href="#viewPlanSupernova"><span>View Plan</span></a></li>
				</ul>
				<div id="progressbarSupernova"></div>
				
					<div id="searchSupernova" class="displaySupernovaContent">
						<div id="searchTabs" class="innerContainers blueGradient" style="padding:10px;">
							<ul>
								<li><a href="#searchSupernovaBrowseAll">Browse All</a></li>
								<li><a href="#searchSupernovaName">By Name</a></li>
								<li><a href="#searchSupernovaRADec">By R.A. &amp; Dec.</a></li>
								<li><a href="#searchSupernovaAdvanced">Advanced</a></li>
							</ul>
						<div id="searchSupernovaBrowseAll" class="search">
							<h4 class="supernovaDescs">This option allows you to browse the entire supernova list. If you want to refine your search, use the tabs to the left to search by name, R.A. and declination, or perform an advanced search.</h4>
							<label>Browse All: <input type="checkbox" id="browse_all" /></label>
							<label class="snerror"></label>
						</div>
						<div id="searchSupernovaName" class="search">
							<h4 class="supernovaDescs">Enter the name of the supernova(s) you want to search for.</h4>
							<label>Name: <input type="text" id="obj_name" size="30" /></label>
							<label class="snerror"></label>
						</div>
						<div id="searchSupernovaRADec" class="search">
							<h4 class="supernovaDescs">Enter the R.A., the Dec. and the radius within which you want to search.</h4>
							<label>Right Ascension: </label>
							<input class="numericInput" type="text" id="obj_ra" size="15" />
							<label class="snerror"></label>
							<label>Declination: </label>
							<input class="numericInput" type="text" id="obj_dec" size="15" />
							<label class="snerror"></label>
							<label>Radius: </label>
							<input class="numericInput" type="text" id="obj_epsilon" size="10" value="0.001" />
							<label class="snerror"></label>
						</div>
						<div id="searchSupernovaAdvanced" class="search">
							<h4 class="supernovaDescs">Trigger/Filter Conditions: <b><i>(Up to 3 conditions)</i></b></h4>
							<div class="supernovaAdvOption">
								<select id="supernovaAdvParams">
									<option value="0">R.A.</option>
									<option value="1">Dec.</option>
									<option value="2">Mag.</option>
									<option value="3">Redshift</option>
									<option value="4">Type</option>
								</select>
								<select id="supernovaAdvOperators">
									<option value="0">BETWEEN</option>
									<option value="1">=</option>
									<option value="2">!=</option>
									<option value="3">&#8249;</option>
									<option value="4">&#8249;=</option>
									<option value="5">&#8250;</option>
									<option value="6">&#8250;=</option>
								</select>
								<input type="text" id="supernovaAdvValue" size="15" />
								<button class="snbuttons addAdvValue">+</button>
								<button class="snbuttons deleteAdvValue">-</button>
							</div>
							<label class="snerror"></label>
							<div class="supernovaAdvOption" style="visibility:hidden;">
								<select id="supernovaAdvParams1">
									<option value="0">R.A.</option>
									<option value="1">Dec.</option>
									<option value="2">Mag.</option>
									<option value="3">Redshift</option>
									<option value="4">Type</option>
								</select>
								<select id="supernovaAdvOperators1">
									<option value="0">BETWEEN</option>
									<option value="1">=</option>
									<option value="2">!=</option>
									<option value="3">&#8249;</option>
									<option value="4">&#8249;=</option>
									<option value="5">&#8250;</option>
									<option value="6">&#8250;=</option>
								</select>
								<input type="text" id="supernovaAdvValue1" size="15" />
								<button class="snbuttons addAdvValue">+</button>
								<button class="snbuttons deleteAdvValue">-</button>
							</div>
							<label class="snerror"></label>
							<div class="supernovaAdvOption" style="visibility:hidden;">
								<select id="supernovaAdvParams2">
									<option value="0">R.A.</option>
									<option value="1">Dec.</option>
									<option value="2">Mag.</option>
									<option value="3">Redshift</option>
									<option value="4">Type</option>
								</select>
								<select id="supernovaAdvOperators2">
									<option value="0">BETWEEN</option>
									<option value="1">=</option>
									<option value="2">!=</option>
									<option value="3">&#8249;</option>
									<option value="4">&#8249;=</option>
									<option value="5">&#8250;</option>
									<option value="6">&#8250;=</option>
								</select>
								<input type="text" id="supernovaAdvValue2" size="15" />
								<button class="snbuttons addAdvValue">+</button>
								<button class="snbuttons deleteAdvValue">-</button>
							</div>
							<label class="snerror"></label>
						</div>
						</div>
						<div id="searchOrdering" class="innerContainers blueGradient" style="padding: 5px 0 5px 17px;">
							<h3 class="supernovaOrderingText">Ordering:</h3>
							<label>Order By:</label>
							<select id="supernovaOrderBy">
								<option value="0" selected="selected">Supernova</option>
								<option value="1">R.A.</option>
								<option value="2">Dec.</option>
								<option value="3">Type</option>
								<option value="4">Magnitude</option>
								<option value="5">Redshift</option>
							</select>&nbsp;
							<select id="supernovaSortOrder">
								<option value="asc" selected="selected">Ascending</option>
								<option value="desc">Descending</option>
							</select>
							<h3 class="supernovaOrderingText">Limitation:</h3>
							<label>Set the range of objects you want:</label>
							<select>
								<option>All</option>
								<option>Top 10</option>
								<option>Top 100</option>
								<option>Top 1000</option>
							</select>
						</div>
						<div id="searchButtons" class="innerContainers" style="left: 50%; width: 300px; margin-left: -150px;">
							<button id="searchsupernovas" class="snbuttons">Search</button>
							<button id="resetsearchsupernova" class="snbuttons">Reset</button>
						</div>
					</div>
					<div id="searchResultsSupernova" class="displaySupernovaContent">
						<div id="searchResultsOverlay">
							<p>You haven't searched for anything yet. Click on the "Search" tab, enter your parameters, and click search to obtain search results.</p>
						</div>
						<div id="supernovaSearchResultsListDisplay" style="position:relative; top:10px; width:48%; margin: 0; padding: 0; display:none;">
							<select>
								<option>Search Results</option>
							</select>	
							<table cellpadding="0" cellspacing="0" border="0" id="searchResultsDisplay" class="listDisplay" style="width:100%;">
								<thead>
							
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
						<div id="supernovaNewListDisplay" style="position:absolute; top:10px; right:10px; bottom:10px; width:48%; margin: 0; padding: 0; display:none;">
							<select id="searchResultsListSelector">
								<option>New List</option>
							</select>
							<table cellpadding="0" cellspacing="0" border="0" id="newListDisplay" class="listDisplay">
								<thead>
								
								</thead>
								<tbody>
									
								</tbody>
							</table>	
						</div>
						<div id="searchResultsUtilitiesContainer" class="ui-helper-clearfix" style="display:none;">
							<button id="copySelectionSearchResults" class="snbuttons" style="float:left; margin-right:5px;">Copy Selection</button>
							<button id="copySearchResults" class="snbuttons" style="float:left; margin:0 5px;">Copy Current Page</button>
							<div style="float:left; margin-left:5px;">
								<label>Can't find what you're looking for?</label><br/>
								<a href="http://astro.cs.pitt.edu/di/SN/web/add-new-supernovas.html" target="_blank">Click here to add new supernovas.</a>
							</div>
							<button id="updateList" class="snbuttons" style="float:right; margin: 0 5px 0;">Update List</button>
							<button id="saveList" class="snbuttons" style="float:right; margin-left:5px;">Save to New List</button>
							<button id="clearList" class="snbuttons" style="float:right; margin-right:5px;">Clear List</button>
						</div>	
					</div>
					<div id="yourListsSupernova" class="displaySupernovaContent">
						<select id="listManagementListSelector"></select>
							<table cellpadding="0" cellspacing="0" border="0" id="listManagementDisplay" class="listDisplay">
								<thead>
								
								</thead>
								<tbody>
									
								</tbody>
							</table>	
					</div>
					<div id="scheduleSupernova" class="displaySupernovaContent">
						<label>Lists: <select id="scheduleListSelector"></select></label>
						<label>My Experiments: <select id="myExperimentSelector"></select></label><br/>
						<p style="margin: 3px 0;">Experiment Name: (optional, <b>required if you want to save experiment parameters</b>)
							<input type="text" id="experimentName" size="25" />
						</p>
						<div id="scheduleAccordion" style="margin:5px 0;">
							<h3><a href="#">General Options</a></h3>
							<div id="scheduleGeneralOptions" class="innerContainers blueGradient">
								<label>Number of nights: <input class="numericInput" type="text" id="scheduleNumNights" size="15" /></label><span><img src="css/images/redalerticon.png" alt="Error" /></span>
								<div id="scheduleDatesAndHours"></div>
							</div>
							<h3><a href="#">Overview of Objects in Current List</a></h3>
							<div id="scheduleObjectOverview" class="innerContainers blueGradient">
								<table cellpadding="0" cellspacing="0" border="0" id="scheduleObjectDisplay" class="listDisplay" style="width:90%;">
									<thead>
									
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
						</div>
						<div id="scheduleButtons" class="innerContainers" style="left: 50%; margin-left: -400px;">
							<button id="generatePlan" class="snbuttons" style="margin-right:5px;">Generate Plan</button>
							<button id="saveExperiment" class="snbuttons" style="margin: 0 5px;">Save As New Experiment</button>
							<button id="updateExperiment" class="snbuttons" style="margin-left:5px;">Update Current Experiment</button>
						</div>
						<div id="scheduleFeedbackContainer" class="innerContainers blueGradient" style="margin-left:10px; height:100px;">
							<label class="innerLabel">State</label>
							<label id="feedbackDisplay" class="snerror" style="margin:5px 0 0 10px;"></label>
						</div>
					</div>
					<div id="viewPlanSupernova" class="displaySupernovaContent">
						<div id="viewPlanOverlay">
							<p>You haven't generated any plan.  Click on "Scheduler", enter all of the necessary information, and then click "Generate Plan" to view your new plan.</p>
						</div>
						<table cellpadding="0" cellspacing="0" border="0" id="viewPlanDisplay" class="listDisplay" style="visibility:hidden;">
							<thead>
							
							</thead>
							<tbody>
								
							</tbody>
						</table>
						<div id="planFeedback" class="viewPlanFooter" style="float:left; visibility:hidden;"></div>
						<div class="viewPlanFooter" style="float:right; visibility:hidden;">
							<button id="likePlan" class="snbuttons">Like</button>
							<button id="dislikePlan" class="snbuttons">Dislike</button>
							<button id="tryagainPlan" class="snbuttons">Try Again</button>
						</div>
					</div>
		</div>

		<!-------------- Create New List Dialog -------------->
		<div id="createNewListDialog" class="supernovae_new_dialog" title="Create New List">
			<label style="display:block;">List Name: <input type="text" id="newListName" size="38" name="List Name"/></label>
			<label class="snerror"></label>
			<textarea placeholder="Enter the list description." rows="5" cols="47" spellcheck="true" style="resize:none;"></textarea>
			<button style="float:right;">Save List</button>
		</div>

		<!-------------- Generate Plan Dialog -------------->
		<div id="generatePlanDialog" class="supernovae_new_dialog" title="Generate Plan">
			<label>Strategy: </label>
			<select id="gpstrategy">
				<option value="0" selected="selected">0</option>
				<option value="1">1</option>
				<option value="2">2</option>
			</select><br/>
			<label>Algorithm: </label>
			<select id="gpalgorithm">
				<option value="0">0</option>
				<option value="1">1</option>
				<option value="2" selected="selected">2</option>
				<option value="3">3</option>
			</select><br/>
			<label>Training: </label>
			<select id="gptraining">
				<option value="0" selected="selected">No</option>
				<option value="1">Yes</option>
			</select><br/>
			<button style="float:right;">Generate Plan</button>
		</div>

		<!--------- Notifications Pop-up Container ------------>
		<!-- set the container hidden to avoid a flash of unstyled content when page first loads -->
		<div id="notycontainer" style="display:none;">
			<div id="basic-template" class="ui-state-notification">
		      <!-- close link -->
		      <a class="ui-notify-close" href="#">
		         <span class="ui-icon ui-icon-close" style="float:right"></span>
		      </a>

		      <!-- alert icon -->
		      <span style="float:left; margin:0 5px 0 0;" class="ui-icon ui-icon-check"></span>

		      <h1>#{title}</h1>
		      <p>#{text}</p>
		   </div>
		   <div id="error-template" class="ui-state-notification-error">
		   	<!-- close link -->
		      <a class="ui-notify-close" href="#">
		         <span class="ui-icon ui-icon-close" style="float:right"></span>
		      </a>

		      <!-- alert icon -->
		      <span style="float:left; margin:0 5px 0 0;" class="ui-icon ui-icon-notice"></span>

		      <h1>#{title}</h1>
		      <p>#{text}</p>
		   </div>
		</div>
		
		<!-------------- Notifications/Overlay/Annotate Dialogs -------------->
		<div id="notifications_dialog"><div id="notylist"></div></div>
        <div id="new_overlay_dialog"></div>
		<div id="result_history_dialog"></div>
		<div id="new_trend_dialog"></div>
		<div id="annotate_dialog"></div>
		<div id="bookmark_dialog"></div>
		
		<!---------------- Group Management Dialog --------------->
		<div id="groupmanage_dialog">
			<table>
				<colgroup>
					<col style="width:50%">
					<col style="width:50%">
				</colgroup>
				<tbody>
					<tr><th>Users</th><th>Groups</th></tr>
					<tr><td><select id="groupmanage_dialog_user_list" size="15"></select></td><td><select id="groupmanage_dialog_group_list" size="15"></select></td></tr>
				</tbody>
			</table>
			<p>
				Add: <b id="groupmanage_dialog_user"></b>&nbsp;&nbsp;&nbsp;To: <b id="groupmanage_dialog_group"></b>&nbsp;&nbsp;&nbsp;<button id="groupmanage_dialog_add_button">Submit</button>
			</p>
			<p id="groupmanage_dialog_add_result">
			</p>
		</div>
		
        <!-------------- Search Objects --------------->
        <div id="search_object" class="slide-out-div">
            <a id="so_handle" class="search_object_label handle ui-state-default ui-corner-top">Search Objects</a>
            <div id="search_object_form_content">
				<div id="search_obj_tabs">
					<ul>
						<li><a href="#tabs-1">Guided SQL</a></li>
						<li><a href="#tabs-2">Direct SQL</a></li>
						<li><a id="search_objs-history" href="#tabs-3">Query History</a></li>
					</ul>
					
					<!--Guided SQL tab-->
					<div id="tabs-1" class="astro-tab-contents">
						<div id="section1">
							<label>-- Surveys:</label><br/>
							<div id="survey_set">
								<p align="center">SDSS<input type="radio" name="radio" value="SDSS" id="radio_sdss" checked="checked"> 
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FIRST<input type="radio" name="radio" value="FIRST" id="radio_first">
								<!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LSST<input type="radio" name="radio" value="LSST" id="radio_lsst">--></p>
							</div>
						</div><hr />
						
						<div id="section5">
							<label>+ From:</label><br/>
							<div id="table_set" style="text-align:center">
								<form id="SDSS_form3">
									tables:&nbsp;&nbsp;&nbsp;&nbsp;
									<select id="SDSS_table_select" style="width:45%;overflow:hidden;text-overflow:ellipsis">
										<option value=" PhotoObj as p LEFT OUTER JOIN SpecObj as s ON p.objid = s.bestobjid ">PhotoObj as p LEFT OUTER JOIN SpecObj as s ON p.objid = s.bestobjid</option>
                                        <option value=" PhotoObj as p INNER JOIN SpecObj as s ON p.objid = s.bestobjid ">PhotoObj as p INNER JOIN SpecObj as s ON p.objid = s.bestobjid</option>
									</select>								
								</form>
								<form id="FIRST_form3">
									tables:&nbsp;&nbsp;&nbsp;&nbsp;
									<select id="FIRST_table_select" style="width:45%;overflow:hidden;text-overflow:ellipsis">
										<option value=" FIRSTcatalog ">FIRSTcatalog</option>
									</select>									
								</form>
								<form id="LSST_form3">
									tables:&nbsp;&nbsp;&nbsp;&nbsp;
									<select id="LSST_table_select" style="width:45%;overflow:hidden;text-overflow:ellipsis">
										<option value=" SimRefObject ">SimRefObject</option>
										<option value=" Object ">Object</option>
									</select>								
								</form>
							</div>
						</div><hr />
						
						<div id="section2">
							<label>+ Parameters:</label><br/>
							<div id="param_set">
								<form id="SDSS_form1">
									<p>
									<table>
									<tr>
									<td align="right">objid<input name="selsts" type="checkbox" value="p.objid"></td>
									<td align="right">RA,Dec<input name="selsts" type="checkbox" value="p.ra, p.dec"></td>
									<td align="right">type<input name="selsts" type="checkbox" value="dbo.fPhotoTypeN(p.type) as type"></td>
									</tr>
									<tr>
									<td align="right">name<input name="selsts" type="checkbox" value="dbo.fIAUFromEq(p.ra, p.dec) as name"></td>
									<td align="right">u, err_u<input name="selsts" type="checkbox" value="p.u, p.err_u"></td>
									<td align="right">g, err_g<input name="selsts" type="checkbox" value="p.g, p.err_g"></td>
									</tr>
									<tr>
									<td align="right">r, err_r<input name="selsts" type="checkbox" value="p.r, p.err_r"></td>
									<td align="right">i, err_i<input name="selsts" type="checkbox" value="p.i, p.err_i"></td>
									<td align="right">z, err_z<input name="selsts" type="checkbox" value="p.z, p.err_z"></td>
									</tr>
									<tr>							
									<td align="right">redshift, red_err<input name="selsts" type="checkbox" value="s.z as redshift, s.zerr as rederr"></td>
									<td align="right">specclass<input name="selsts" type="checkbox" value="dbo.fSpecClassN(s.specclass) as specclass"></td>
									<td align="right">zconf<input name="selsts" type="checkbox" value="s.zconf"></td>
									</tr>
									<tr style="line-height:35px">							
									<td align="right">ALL<input type="checkbox" name="all_check" ></td>
									<td align="right">NONE<input type="checkbox" name="none_check"></td>
									<td align="right">DEFAULT<input type="checkbox" name="default_check" checked="checked"></td>
									</tr>
									</table>
									<div style="text-align:center"><input type="button" id="SDSS_addbtn1" value="Add Parameters" style="width:250px"></div>
									</p>
								</form>
								<form id="FIRST_form1">
									<p>
									<table align="center">
									<tr>
									<td align="right">RA,Dec<input name="selsts" type="checkbox" value="RA, Declination"></td>
									<td align="right">Ps<input name="selsts" type="checkbox" value="Ps"></td>
									<td align="right">Fpeak<input name="selsts" type="checkbox" value="Fpeak"></td>
									</tr>
									<tr>
									<td align="right">Fint<input name="selsts" type="checkbox" value="Fint"></td>
									<td align="right">RMS<input name="selsts" type="checkbox" value="RMS"></td>
									<td align="right">Field<input name="selsts" type="checkbox" value="Field"></td>
									</tr>
									<tr>
									<td align="right">Maj, fMaj<input name="selsts" type="checkbox" value="Maj, fMaj"></td>
									<td align="right">Min, fMin<input name="selsts" type="checkbox" value="Min, fMin"></td>
									<td align="right">PA, fPA<input name="selsts" type="checkbox" value="PA, fPA"></td>
									</tr>
									<tr style="line-height:35px">							
									<td align="right">ALL<input type="checkbox" name="all_check" ></td>
									<td align="right">NONE<input type="checkbox" name="none_check"></td>
									<td align="right">DEFAULT<input type="checkbox" name="default_check" checked="checked"></td>
									</tr>
									</table>
									<div style="text-align:center"><input type="button" id="FIRST_addbtn1" value="Add Parameters" style="width:250px"></div>
									</p>
								</form>
								<form id="LSST_form1">
									<p>
									<table align="center">
									<!--Table `SimRefObject`-->
									<tr>
									<td align="right">refObjectId<input name="selsts" type="checkbox" value="refObjectId"></td>
									<td align="right">ra, decl<input name="selsts" type="checkbox" value="ra, decl"></td>
									<td align="right">isStar<input name="selsts" type="checkbox" value="isStar"></td>
									</tr>
									<tr>
									<td align="right">uMag<input name="selsts" type="checkbox" value="uMag"></td>
									<td align="right">gMag<input name="selsts" type="checkbox" value="gMag"></td>
									<td align="right">rMag<input name="selsts" type="checkbox" value="rMag"></td>
									</tr>
									<tr>
									<td align="right">iMag<input name="selsts" type="checkbox" value="iMag"></td>
									<td align="right">zMag<input name="selsts" type="checkbox" value="zMag"></td>
									<td align="right">yMag<input name="selsts" type="checkbox" value="yMag"></td>
									</tr>
									<tr>
									<td align="right">redshift<input name="selsts" type="checkbox" value="redshift"></td>
									</tr>
									
									<!--Table `Object`-->
									<tr>
									<td align="right">objectId<input name="selsts" type="checkbox" value="objectId"></td>
									<td align="right">ra_PS, decl_PS<input name="selsts" type="checkbox" value="ra_PS, decl_PS"></td>
									</tr>
									<tr>
									<td align="right">uFlux_G<input name="selsts" type="checkbox" value="uFlux_Gaussian"></td>
									<td align="right">gFlux_G<input name="selsts" type="checkbox" value="gFlux_Gaussian"></td>
									<td align="right">rFlux_G<input name="selsts" type="checkbox" value="rFlux_Gaussian"></td>
									</tr>
									<tr>
									<td align="right">iFlux_G<input name="selsts" type="checkbox" value="iFlux_Gaussian"></td>
									<td align="right">zFlux_G<input name="selsts" type="checkbox" value="zFlux_Gaussian"></td>
									<td align="right">yFlux_G<input name="selsts" type="checkbox" value="yFlux_Gaussian"></td>
									</tr>
									<tr>
									<td align="right">uFlux_G_Sigma<input name="selsts" type="checkbox" value="uFlux_Gaussian_Sigma"></td>
									<td align="right">gFlux_G_Sigma<input name="selsts" type="checkbox" value="gFlux_Gaussian_Sigma"></td>
									<td align="right">rFlux_G_Sigma<input name="selsts" type="checkbox" value="rFlux_Gaussian_Sigma"></td>
									</tr>
									<tr>
									<td align="right">iFlux_G_Sigma<input name="selsts" type="checkbox" value="iFlux_Gaussian_Sigma"></td>
									<td align="right">zFlux_G_Sigma<input name="selsts" type="checkbox" value="zFlux_Gaussian_Sigma"></td>
									<td align="right">yFlux_G_Sigma<input name="selsts" type="checkbox" value="yFlux_Gaussian_Sigma"></td>
									</tr>
									
									<tr style="line-height:35px">							
									<td align="right">ALL<input type="checkbox" name="all_check" ></td>
									<td align="right">NONE<input type="checkbox" name="none_check"></td>
									<td align="right">DEFAULT<input type="checkbox" name="default_check" checked="checked"></td>
									</tr>
									</table>
									<div style="text-align:center"><input type="button" id="LSST_addbtn1" value="Add Parameters" style="width:250px"></div>
									</p>
								</form>
							</div>
						</div><hr />
						
						<div id="section3">
							<label>+ Conditions:</label><br/>
							<div id="condi_set">
								<form id="SDSS_form2">
									<p>
									<table>
										<tr>
											<td></td><td>parameter: </td><td>operator: </td><td>value: </td>
										</tr>
										<tr class="content_0" id="SDSS_menu">							
											<td>
											<select id="SDSS_condition_select" style="width:70px">
												<option value=" AND ">AND</option>
												<option value=" OR ">OR</option>
												<option value=") AND (">) AND (</option>
												<option value=") OR (">) OR (</option>
											</select>
											</td>
											<td>
											<select id="SDSS_parameter_select" style="width:80px">
												<option value="">select</option>
												<option value="p.ra">RA</option>
												<option value="p.dec">Dec</option>
												<option value="p.type">Type</option>									
												<option value="p.u">u</option>
												<option value="p.g">g</option>
												<option value="p.r">r</option>
												<option value="p.i">i</option>
												<option value="p.z">z</option>
												<option value="(p.g-p.r)">g-r</option>
												<option value="(p.r-p.i)">r-i</option>
												<option value="(p.u-p.r)">u-r</option>
												<option value="(p.u-p.z)">u-z</option>
												<option value="(p.i-p.z)">i-z</option>
												<option value="(p.u-p.g)">u-g</option>
												<option value="s.z">RedShift</option>
												<option value="s.specclass">SpecClass</option>
												<option value="s.zconf">Zconf</option>
											</select>
											</td>
											<td>
											<select id="SDSS_operation_select" style="width:70px">
												<option value="">select</option>
												<option value="between">BETWEEN</option>
												<option value="=">=</option>
												<option value="!=">!=</option>
												<option value="<"><</option>
												<option value="<="><=</option>
												<option value=">">></option>
												<option value=">=">>=</option>
											</select>
											</td>
											<td><input type="text" id="SDSS_value_input" size="2" style="width:60px"/></td>
											<td><input type="button" value="+" id="SDSS_addBtn2"></td>
											<td><input type="button" value="-" id="SDSS_minBtn2"></td>
										</tr>
									</table>
									<div id="SDSS_extend1" style="text-align:center; font-size:13px;">3-Galaxy&nbsp;&nbsp;6-Star&nbsp;&nbsp;0-Unknown</div>
									<div id="SDSS_extend2" style="text-align:center; font-size:13px;">1-Star&nbsp;&nbsp;2-Galaxy&nbsp;&nbsp;3-QSO&nbsp;&nbsp;4-HIZQSO&nbsp;&nbsp;0-Unknown</div>
									</p>
								</form>
								<form id="FIRST_form2">
									<p>
									<table>
										<tr>
											<td></td><td>parameter: </td><td>operator: </td><td>value: </td>
										</tr>
										<tr class="content_0" id="FIRST_menu">							
											<td>
											<select id="FIRST_condition_select" style="width:70px">
												<option value=" AND ">AND</option>
												<option value=" OR ">OR</option>
												<option value=") AND (">) AND (</option>
												<option value=") OR (">) OR (</option>
											</select>
											</td>
											<td>
											<select id="FIRST_parameter_select" style="width:80px">
												<option value="">select</option>
												<option value="RA">RA</option>
												<option value="Declination">Dec</option>
												<option value="Ps">Ps</option>									
												<option value="Fpeak">Fpeak</option>
												<option value="Fint">Fint</option>
												<option value="RMS">RMS</option>
												<option value="Maj">Maj</option>
												<option value="Min">Min</option>
												<option value="PA">PA</option>
												<option value="fMaj">fMaj</option>
												<option value="fMin">fMin</option>
												<option value="fPA">fPA</option>
											</select>
											</td>
											<td>
											<select id="FIRST_operation_select" style="width:70px">
												<option value="">select</option>
												<option value="between">BETWEEN</option>
												<option value="=">=</option>
												<option value="!=">!=</option>
												<option value="<"><</option>
												<option value="<="><=</option>
												<option value=">">></option>
												<option value=">=">>=</option>
											</select>
											</td>
											<td><input type="text" id="FIRST_value_input" size="2" style="width:60px"/></td>
											<td><input type="button" value="+" id="FIRST_addBtn2"></td>
											<td><input type="button" value="-" id="FIRST_minBtn2"></td>
										</tr>
									</table>
									</p>
								</form>
								<form id="LSST_form2">
									<p>
									<table>
										<tr>
											<td></td><td>parameter: </td><td>operator: </td><td>value: </td>
										</tr>
										<tr class="content_0" id="LSST_menu">							
											<td>
											<select id="LSST_condition_select" style="width:70px">
												<option value=" AND ">AND</option>
												<option value=" OR ">OR</option>
												<option value=") AND (">) AND (</option>
												<option value=") OR (">) OR (</option>
											</select>
											</td>
											<td>
											<select id="LSST_parameter_select" style="width:80px">
												<option value="">select</option>
												<!--Table `SimRefObject`-->
												<option value="ra">ra</option>
												<option value="decl">decl</option>
												<option value="isStar">isStar</option>									
												<option value="uMag">uMag</option>
												<option value="gMag">gMag</option>
												<option value="rMag">rMag</option>
												<option value="iMag">iMag</option>
												<option value="zMag">zMag</option>
												<option value="yMag">yMag</option>
												<option value="gMag-rMag">g-r</option>
												<option value="rMag-iMag">r-i</option>
												<option value="uMag-rMag">u-r</option>
												<option value="uMag-zMag">u-z</option>
												<option value="iMag-zMag">i-z</option>
												<option value="uMag-gMag">u-g</option>
												<option value="zMag-yMag">z-y</option>
												<option value="rMag-yMag">r-y</option>
												<option value="uMag-yMag">u-y</option>
												<option value="redshift">redshift</option>
												
												<!--Table `Object`-->
												<option value="ra_PS">ra_PS</option>
												<option value="decl_PS">decl_PS</option>							
												<option value="uFlux_Gaussian">uFlux_Gaussian</option>
												<option value="gFlux_Gaussian">gFlux_Gaussian</option>
												<option value="rFlux_Gaussian">rFlux_Gaussian</option>
												<option value="iFlux_Gaussian">iFlux_Gaussian</option>
												<option value="zFlux_Gaussian">zFlux_Gaussian</option>
												<option value="yFlux_Gaussian">yFlux_Gaussian</option>
												<option value="uFlux_Gaussian_Sigma">uFlux_Gaussian_Sigma</option>
												<option value="gFlux_Gaussian_Sigma">gFlux_Gaussian_Sigma</option>
												<option value="rFlux_Gaussian_Sigma">rFlux_Gaussian_Sigma</option>
												<option value="iFlux_Gaussian_Sigma">iFlux_Gaussian_Sigma</option>
												<option value="zFlux_Gaussian_Sigma">zFlux_Gaussian_Sigma</option>
												<option value="yFlux_Gaussian_Sigma">yFlux_Gaussian_Sigma</option>
												<option value="gFlux_Gaussian-rFlux_Gaussian">g-r</option>
												<option value="rFlux_Gaussian-iFlux_Gaussian">r-i</option>
												<option value="uFlux_Gaussian-rFlux_Gaussian">u-r</option>
											</select>
											</td>
											<td>
											<select id="LSST_operation_select" style="width:70px">
												<option value="">select</option>
												<option value="between">BETWEEN</option>
												<option value="=">=</option>
												<option value="!=">!=</option>
												<option value="<"><</option>
												<option value="<="><=</option>
												<option value=">">></option>
												<option value=">=">>=</option>
											</select>
											</td>
											<td><input type="text" id="LSST_value_input" size="2" style="width:60px"/></td>
											<td><input type="button" value="+" id="LSST_addBtn2"></td>
											<td><input type="button" value="-" id="LSST_minBtn2"></td>
										</tr>
									</table>
									<div id="LSST_extend1" style="text-align:center; font-size:13px;">0-Galaxy&nbsp;&nbsp;1-Star</div>
									</p>
								</form>
							</div>
						</div><hr />

						<div id="section6">
							<label>+ Limitation:</label><br/>
							<div id="limit_set" style="text-align:center">
								<form id="SDSS_form4">
									<br/>
									tops:&nbsp;
									<select id="SDSS_limit_select" style="width:25%;overflow:hidden;text-overflow:ellipsis">
										<option value="unlimited">UNLIMITED</option>
										<option value=" TOP 100 ">TOP 100</option>
										<option value=" TOP 1000 ">TOP 1000</option>
										<option value=" TOP 10000 ">TOP 10000</option>
										<option value=" TOP 100000 ">TOP 100000</option>
									</select>&nbsp;&nbsp;&nbsp;&nbsp;
									samples:&nbsp;
									<select id="SDSS_sample_select" style="width:25%;overflow:hidden;text-overflow:ellipsis">
										<option value="-1">NOT SAMPLING</option>
										<option value="0.5">Random 0.5% sample</option>
										<option value="1">Random 1% sample</option>
										<option value="5">Random 5% sample</option>
										<option value="10">Random 10% sample</option>
									</select>									
								</form>
								<form id="FIRST_form4">
									<br/>
									limits:&nbsp;
									<select id="FIRST_limit_select" style="width:25%;overflow:hidden;text-overflow:ellipsis">
										<option value="unlimited">UNLIMITED</option>
										<option value=" LIMIT 100 ">LIMIT 100</option>
										<option value=" LIMIT 1000 ">LIMIT 1000</option>
										<option value=" LIMIT 10000 ">LIMIT 10000</option>
										<option value=" LIMIT 100000 ">LIMIT 100000</option>
									</select>&nbsp;&nbsp;&nbsp;&nbsp;
									randomization:&nbsp;
									<input id="FIRST_random_check" type="checkbox" value="random"/>
								</form>
								<form id="LSST_form4">
									<br/>
									limits:&nbsp;
									<select id="LSST_limit_select" style="width:25%;overflow:hidden;text-overflow:ellipsis">
										<option value="unlimited">UNLIMITED</option>
										<option value=" LIMIT 100 ">LIMIT 100</option>
										<option value=" LIMIT 1000 ">LIMIT 1000</option>
										<option value=" LIMIT 10000 ">LIMIT 10000</option>
										<option value=" LIMIT 100000 ">LIMIT 100000</option>
									</select>&nbsp;&nbsp;&nbsp;&nbsp;
									randomization:&nbsp;
									<input id="LSST_random_check" type="checkbox" value="random"/>									
								</form>
							</div>
						</div><hr />
						
						<div id="section4">
							<label>+ SQL Query:</label><br/>
							<div id="SDSS_query_display"><p></p></div>
							<div id="FIRST_query_display"><p></p></div>
							<div id="LSST_query_display"><p></p></div>
							<div id="copy_to_clipboard" style="text-align:right">
								<input type="button" value="copy" id="copy_inner" style="width:50px">
							</div><hr />
							<div id="rest_buttons" style="text-align:center"><br/>
								enable user preference<input id="OBJ_pref" type="checkbox" value="pref"/><br/><br/>
								<input type="button" value="Search" id="search_res" style="width:100px"/>&nbsp;&nbsp
								<input type="button" value="Cancel" id="cancel_ajax" style="width:100px"/>&nbsp;&nbsp
								<input type="button" value="Reset" id="reset_input" style="width:100px"/>
							</div>
						</div>
					</div>
					<!--Direct SQL tab-->
					<div id="tabs-2" class="astro-tab-contents">
						<div id="tabs2_top" style="text-align:center">
							<p>SDSS<input type="radio" name="radio2" id="radio_sdss_tab2" checked="checked"> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FIRST<input type="radio" name="radio2" id="radio_first_tab2">
							<!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LSST<input type="radio" name="radio2" id="radio_lsst_tab2"-->
							</p>
						</div>
						<div id="tabs2_middle" style="text-align:center">
						<textarea id="textarea_tab2" rows="10" cols="50" style="font-size:12pt;" class="search_box1"></textarea>
						</div>
						<div id="tabs2_bottom" style="text-align:center"><br/>
							enable user preference<input id="OBJ_pref_tab2" type="checkbox" value="pref"/><br/><br/>
							<input type="button" value="Search" id="search_tab2" style="width:100px"/>&nbsp;&nbsp
							<input type="button" value="Cancel" id="cancel_tab2" style="width:100px"/>&nbsp;&nbsp
							<input type="button" value="Reset" id="reset_tab2" style="width:100px"/>
						</div>					
					</div>
					<!--Query history tab-->
					<div id="tabs-3" class="astro-tab-contents">
						<div id="tabs3_top" style="text-align:center">
							<p><a href="#" id="login_tab3">login</a></p>
						</div>
						<div id="tabs3_middle" style="text-align:center">
							<table border="1" cellpadding="5" style="width:100%;word-break:break-all" id="the_table_tabs3">
								<tr id="title">
									<th style="width:100px">survey</th><th style="width:250px">query</th><th style="width:100px">time</th><th style="width:10px"></th>
								</tr>
							</table>
							<br/>
							<a href="#" id="prev_tab3" style="position:absolute;left:50px;">Previous</a><a href="#" id="next_tab3" style="position:absolute;right:50px;">Next</a>
							<br/><p><i>Click query to restore corresponding history result...</i></p><br/>
						</div>
						<div id="tabs3_bottom" style="text-align:center">
							<!--<input type="button" value="More" id="more_tab3" style="width:100px"/>&nbsp;&nbsp-->
							<input type="button" value="Refresh" id="reload_tab3" style="width:100px"/>&nbsp;&nbsp
							<input type="button" value="Delete" id="my_delete_tab3" style="width:100px"/>
						</div>
					</div>
				</div>
			</div>
        </div>

		<!-------------- Search Annotations --------------->	
        <div id="search_annotation" class="slide-out-div">
            <a id="sa_handle" class="search_annotation_label handle ui-state-default ui-corner-top">Search Annotations</a>
            <div id="annotations_content" style="display:block">
				<div id="annotations_tabs">
					<ul>
						<li><a href="#annotations_tab1">Search</a></li>
						<li><a href="#annotations_tab2">My Annotations</a></li>
						<li><a href="#annotations_tab3">My Group</a></li>
					</ul>
					<div id="annotations_tab1" class="astro-tab-contents">
						<div id="search_annotation_form_content">			
							<div id="anno_section1">
								<label>Parameters:</label><br/>
								<form id="ANNO_form">
									<p><table>
										<tr class="content_0" id="ANNO_menu">
											<td align="center">
											<select id="ANNO_parameter_select">
												<option value="">select</option>
												<option value="first_name">first_name</option>
												<option value="last_name">last_name</option>
												<option value="username">username</option>
												<option value="user_id" disabled="disabled">user_id</option>
												<option value="group_name" disabled="disabled">group_name</option>							
												<option value="type">type</option>
												<option value="target_type">target_type</option>
												<option value="keyword">keyword</option>
                                                <option value="title_keyword">title_keyword</option>
												<option value="ra">ra</option>
												<option value="dec">dec</option>
                                                <option value="redshift">redshift</option>
												<option value="raFrom">raFrom</option>
												<option value="raTo">raTo</option>
												<option value="decFrom">decFrom</option>
												<option value="decTo">decTo</option>
                                                <option value="redshiftFrom">redshiftFrom</option>
                                                <option value="redshiftTo">redshiftTo</option>
												<option value="limit">LIMIT</option>
											</select>
											</td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;&nbsp;</td>
											<td align="center"><input type="text" id="ANNO_value_input" size="22"/></td>
											<td align="center"><input type="button" value="+" id="ANNO_addBtn"></td>
											<td align="center"><input type="button" value="-" id="ANNO_minBtn"></td>
										</tr>
									</table>
									<div id="ANNO_extend1" style="text-align:center; font-size:13px;">The first name of the user that is author of an annotation</div>
									<div id="ANNO_extend2" style="text-align:center; font-size:13px;">The last name of the user that is author of an annotation</div>
									<div id="ANNO_extend3" style="text-align:center; font-size:13px;">The username of the user that is author of an annotation</div>
									<div id="ANNO_extend4" style="text-align:center; font-size:13px;">The id# of the user that is author of an annotation</div>
									<div id="ANNO_extend5" style="text-align:center; font-size:13px;">Group name. Searches for all annotations introduced by users in this group</div>
									<div id="ANNO_extend6" style="text-align:center; font-size:13px;">The type of annotation</div>
									<div id="ANNO_extend7" style="text-align:center; font-size:13px;">The type of entity on which the annotation attached,<br/>for example, object/annotation/area(point)/view...</div>
									<div id="ANNO_extend8" style="text-align:center; font-size:13px;">Keyword that will be searched in annotation value field</div>
									<div id="ANNO_extend9" style="text-align:center; font-size:13px;">The area/point where the annotation is attached</div>
									<div id="ANNO_extend10" style="text-align:center; font-size:13px;">Caution: <br/>this paramter is typically used to limit the size of returned result set</div></p>
								</form>
							</div><hr/>
							
							<div id="anno_section2">
								<label>Summary:</label><br/>
								<p style='font-size:13px'>The chosen parameters are listed as following...</p>
								<div id="ANNO_display"><p></p></div>
							</div><hr/><br/>
							
							<div id="anno_section3" style="text-align:center">
								enable user preference<input id="ANNO_pref" type="checkbox" value="pref"/><br/><br/>
								<input type="button" value="Search" id="search_anno" style="width:100px"/>&nbsp;&nbsp;
								<input type="button" value="Cancel" id="cancel_anno" style="width:100px"/>&nbsp;&nbsp;
								<input type="button" value="Reset" id="reset_anno" style="width:100px"/>				
							</div>
						</div>
					</div>
					<div id="annotations_tab2" class="astro-tab-contents">
						<div><table></table></div>
					</div>
					<div id="annotations_tab3" class="astro-tab-contents">
						<div><table></table></div>
					</div>
				</div>
			</div>
        </div>

		<!-------------- Bookmarks Tab --------------->	
        <div id="bookmarks" class="slide-out-div">
            <a id="bookmarks_handle" class="handle ui-state-default ui-corner-top">Bookmarks</a>
            <div id="bookmarks_content" style="display:block">
				<div id="bookmarks_tabs">
					<ul>
						<li><a href="#bookmarks_tab1">Locations</a></li>
						<li><a href="#bookmarks_tab2">Objects</a></li>
						<li><a href="#bookmarks_tab3">Annotations</a></li>
					</ul>
					<!---Location Bookmarks--->
					<div id="bookmarks_tab1" class="astro-tab-contents">
						<div><table></table></div>
						<!--ul>
							<li onclick="skyView.jump(150.0,0.0)" class="bookmark-list">150.0, 0.0</li>
							<li onclick="skyView.jump(0.0,0.0)" class="bookmark-list">0.0, 0.0</li>
							<li onclick="skyView.jump(224.594, -1.09)" class="bookmark-list">NGC 5792 - a spiral galaxy: 224.594, -1.09</li>
							<li onclick="skyView.jump(193.092, -1.199)" class="bookmark-list">NGC 4753 - an elliptical with dust lanes: 193.092, -1.199</li>
							<li onclick="skyView.jump(166.454, -0.036)" class="bookmark-list">NGC 3521 - a nice dusty spiral: 166.454, -0.036 *</li>
							<li onclick="skyView.jump(40.433,   0.449)" class="bookmark-list">NGC 1055 - an edge-on spiral: 40.433, 0.449</li>
							<li onclick="skyView.jump(208.227, -1.114)" class="bookmark-list">NGC 5334 - a young star-forming galaxy: 208.227, -1.114</li>
							<li onclick="skyView.jump(9.895, 0.86)" class="bookmark-list">NGC 201 - a nearby group of galaxies: 9.895, 0.86</li>
							<li onclick="skyView.jump(323.360003, -0.82154)" class="bookmark-list">M2 - a globular cluster: 323.360003, -0.82154 *</li>
							<li onclick="skyView.jump(141.077, 34.5134)" class="bookmark-list">NGC 2859 - a ringed galaxy: 141.077, 34.5134</li>
							<li onclick="skyView.jump(202.469, 47.195)" class="bookmark-list">M51 - a biggie: 202.469, 47.195</li>
							<li onclick="skyView.jump(198.61646, 45.919)" class="bookmark-list">UGC 8320 - a faint irregular galaxy: 198.61646, 45.919 *</li>
							<li onclick="skyView.jump(209.03004, 5.2547)" class="bookmark-list">NGC 5363 - a big elliptical: 209.03004, 5.2547 *</li>
							<li onclick="skyView.jump(204.97, 0.84)" class="bookmark-list">ARP 240 - interacting galaxies: 204.97, 0.84 *</li>
							<li onclick="skyView.jump(204.061, -1.04)" class="bookmark-list">UGC 08584 - interacting galaxies: 204.061, -1.04</li>
							<li onclick="skyView.jump(28.174, 1.008)" class="bookmark-list">Abell 267 - a distant cluster: 28.174, 1.008</li>
							<li onclick="skyView.jump(153.378, -0.85)" class="bookmark-list">Abell 0957 - a rich cluster: 153.378, -0.85</li>
							<li onclick="skyView.jump(187.7059, 12.391)" class="bookmark-list">M83 - elliptical radio galaxy: 187.7059, 12.391</li>
							<li onclick="skyView.jump(202.54787, -1.66313)" class="bookmark-list">NGC 5184: 202.54787562, -1.66313135</li>
							<li onclick="skyView.jump(213.79892, 15.74208)" class="bookmark-list">UGC 09121: 213.79892901, 15.74208366</li>
							<li onclick="skyView.jump(210.08382, 38.9154)" class="bookmark-list">NGC 5406: 210.08382622, 38.9154254</li>
							<li onclick="skyView.jump(30.55140,-0.10063)" class="bookmark-list">NGC 0799: 30.55140766,-0.10063257</li>
							<li onclick="skyView.jump(115.1636,39.23329)" class="bookmark-list">NGC 2424: 115.16367545,39.23329478</li>
							<li onclick="skyView.jump(162.45728,32.98421)" class="bookmark-list">NGC 3395: 162.45728434,32.98421215</li>
							<li onclick="skyView.jump(208.07421,31.446306)" class="bookmark-list">UGC 08782 - radio elliptical: 208.07421,31.446306</li>
						</ul-->
					</div>
					<!---Object Bookmarks--->
					<div id="bookmarks_tab2" class="astro-tab-contents">
						<div><table></table></div>
					</div>
					<!---Annotation Bookmarks--->
					<div id="bookmarks_tab3" class="astro-tab-contents">
						<div><table></table></div>
					</div>
				</div>
            </div>
        </div>

		<!-------------- Overlays Tab -------------->
        <div id="overlays" class="slide-out-div">
            <a id="overlays_handle" class="handle ui-state-default ui-corner-top">Overlays</a>
            <div id="overlays_content" style="display:block">
                <div class="sliderBlock"><div class="sliderLabel">SDSS:</div><div id="SDSS_slider" class="slider"></div></div>
                <div class="sliderBlock"><div class="sliderLabel">FIRST:</div><div id="FIRST_slider" class="slider"></div></div>
				<div class="sliderBlock"><div class="sliderLabel">LSST:</div><div id="LSST_slider" class="slider"></div></div>
            </div>
        </div>

		<!-------------- Results Tab -------------->	
        <div id="results" class="slide-out-div">
            <a id="results_handle" class="handle ui-state-default ui-corner-top">Results</a>
            <div id="results_content" class="slider-contents">
				<div id="result_tabs">
					<ul>
						<li><a href="#tab1">Display Results</a></li>
						<li><a href="#tab2">Result History</a></li>
						<li><a href="#tab3">Upload Result</a></li>
					</ul>
					<div id="tab1" class="astro-tab-contents"></div>
					<div id="tab2" class="astro-tab-contents">
						<div id="res_his_search" style="text-align:center">
							<label>Search from Result History</label><hr/><br/>
							Result Name:&nbsp;&nbsp;<input type="text" id="res_his_name" size="25"/><br/><br/>
							<input type="button" id="res_his_button" value="Search Results" style="width:275px"/>
						</div>
						<br/><hr/><br/>
						<div id="res_his_display" style="text-align:center">
							<table border="1" cellpadding="5" style="width:100%;word-break:break-all" id="res_his_table">
								<tr id="res_his_title">
									<th style="width:60px">survey</th><th style="width:100px">result name</th><th style="width:120px">result comment</th><th style="width:50px">result size</th><th style="width:50px"></th>
								</tr>
							</table>						
						</div>
					</div>
					<div id="tab3" class="astro-tab-contents">
						<br/>
						<label>Caution: please select the well-formatted "Comma-separated Value" file from your local disk, 
						any incorrect in file type or content will affect properly upload or parse of the dataset.</label><br/><br/><hr/><br/>
						<div style="text-align:center">
							<select id="upload_res_survey" style="width:150px">
								<option value="">-- select survey --</option>
								<option value="SDSS">SDSS</option>
								<option value="FIRST">FIRST</option>
								<option value="LSST">LSST</option>
							</select><br/><br/>
							<select id="upload_res_table" style="width:150px">
								<option value="">-- select table --</option>
								<option value="SimRefObject">SimRefObject</option>
								<option value="Object">Object</option>
							</select><br/><br/>				
							<input type="file" id="upload_res_file" accept="text/csv" /><br/><br/>
							<input type="button" id="upload_res_button" value="Upload" style="width:275px"/>
						</div>
					</div>
				</div>
			</div>
        </div>

		<!-------------- Object details Tab -------------->
		<div id="object_tab" class="slide-out-div">
			<a id="object_handle" class="handle ui-state-default ui-corner-top">Object details</a>
			<div id="object_content" class="slider-contents"></div>
		</div>

		<!-------------- Tread Image Tab -------------->
		<div id="trend_tab" class="trend-slide-out-div">

			<a id="trend_handle" class="handle ui-state-default ui-corner-top">Trend Image</a>
			<div id="trend_image_content" class="slider-contents">
				<div id="trend_tabs">
					<ul>
						<li><a href="#tab1">Trend Images</a></li>
					</ul>
					<div id="tab1" class="astro-tab-contents"></div>
					<!-- end trend tabs -->
				</div>
				<!-- end trend image content -->
			</div>
			
			<div class="images" style="display:none;" onload="init();"></div>
  	  <!-- end trend tab -->
	  </div>

		<!-------------- Thumbnail Tab -------------->
        <div id="thumbnail" class="slide-out-div" >
            <a id="thumbnail_handle" class="handle ui-state-default ui-corner-top">Thumbnails</a>
			<div id="thumbnailCanvasDiv">
				<canvas id="thumbnail_canvas" 
					style="border:1px solid #000000; 		
					position: absolute; top: 10px; left: 10px;"
					class="thumbnail-context-menu">
	            </canvas>
				<div id="thumbnailCrosshairs">
					<svg height="120" width="120">
					  <line x1="60" y1="0" x2="60" y2="50" style="stroke:rgb(200,200,200);stroke-width:2" />
					  <line x1="60" y1="70" x2="60" y2="120" style="stroke:rgb(200,200,200);stroke-width:2" />
					  <line x1="0" y1="60" x2="50" y2="60" style="stroke:rgb(200,200,200);stroke-width:2" />
					  <line x1="70" y1="60" x2="120" y2="60" style="stroke:rgb(200,200,200);stroke-width:2" />
					</svg>
				</div>
			</div>
        </div>
	<div id="APIToken" style="display:none;">
<?php
$ctx = stream_context_create(
	array(
		'http'=>array(
			'header'=> array("Content-Type: application/json", 'Authorization: Basic '. base64_encode($_SERVER["PHP_AUTH_USER"].":".$_SERVER["PHP_AUTH_PW"])),
			'method'=>'GET'
		)
	)
);
$token = file_get_contents("https://astro.cs.pitt.edu:21003/astroservice/user/login", 0, $ctx);
if($token) {
	echo $token;
}
?>
	</div>	
	</body>
</html>
