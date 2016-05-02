$(function(){
    $.widget("ui.splashDialog", {
        options: {
			position: 'center'
        },
		
        _create: function(){
            var me = this;
            var base = this.element;
            $(base).attr("title", "Welcome to AstroShelf");
            $(base).dialog({
				width:800,
				height:520,
				autoOpen: true,
				resizable: false,
                buttons: [
					{
						text: "Enter Site",
						click: function(){
							window.location.href = "https://astro.cs.pitt.edu/astroshelfv3/index.php";
						}
					}
                ],
				closeOnEscape:false,
				open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog | ui).hide(); }
             }); 
			
            me.$tabs = $('<div/>').addClass("annotate-tabs").attr('id','tabs');
            this._initSplashTabs(me.$tabs);
            this.element.append(me.$tabs);
			
			$(".ui-tabs-panel").removeClass("ui-tabs-panel");
			this.userCreationSetup();
        },
		
		open: function(){
            $(this.element).dialog("open");
        },

        _initSplashTabs: function(parent){
            var list = $('<ul/>').html("<li><a href='#splashDialogTab1'>About</a></li>"+
				"<li><a href='#splashDialogTab2'>Credits</a></li>"+
                "<li><a href='#splashDialogTab3'>Demo</a></li>"+
				"<li><a href='#splashDialogTab4'>Create User</a></li>");
            parent.append(list);

            var tab1 = $("<div/>").attr('id', 'splashDialogTab1');
            $(tab1).html("<p>Astroshelf is a prototype platform for the integration and exploration of image, catalog and annotation astronomy data. Its goal is to enable astrophysicists to investigate celestial objects collaboratively. It does so by supporting the following functionality:</p>\
			<ol>\
				<li>Navigate the sky visualization.</li>\
				<li>Query data originating from SDSS and FIRST sky surveys.</li>\
				<li>Annotate survey objects and phenomena and share those annotations.</li>\
				<li>Generate visualizations of spectrum data for survey objects.</li>\
				<li>Bookmark survey objects, annotations, and sky coordinates.</li>\
			</ol>\
			AstroShelf also provides programmatic access to these features via a secure REST API called AstroService.</li>");
            parent.append(tab1);

            var tab2 = $("<div/>").attr('id', 'splashDialogTab2');
            $(tab2).html("<p>This system was developed by a team of computer science and astronomy researchers at the University of Pittsburgh. The project was funded by NSF award <a href='http://nsf.gov/awardsearch/showAward.do?AwardNumber=1028162' target='_blank'>OIA-1028162</a>.</p>\
				<p>\
				PI: Alexandros Labrinidis (Computer Science)<br/>\
				Co-PIs (Computer Science): Panos Chrysanthis, Liz Marai (now at University of Illinois at Chicago)<br/>\
				Co-PIs (Astronomy): Jeff Newman, Michael Wood-Vasey<br/>\
				Senior Personnel: Arthur Kosowsky (Astronomy)\
				</p>\
				<p>Current developer: Eric Gratta</p>\
				<p>\
				Di Bao<br/>\
				Brian Cherinka<br/>\
				Wen Gao<br/>\
				Roxana Gheorgiu<br/>\
				Rebecca Hachey<br/>\
				Matthew Liegey<br/>\
				Tim Luciani<br/>\
				Sean Myers<br/>\
				Panayiotis (Panickos) Neophytou<br/>\
				Daniel Oliphant<br/>\
				Matthew Seiler<br/>\
				Boyu Sun<br/>\
				Nikhil Venkatesh<br/>\
				</p>\
				<p><b>Contact:</b><br/>Dr. Alexandros Labrinidis<br/>Department of Computer Science,<br/>University of Pittsburgh<br/>Pittsburgh, PA 15260<br/>Tel: 412-624-8490<br/>Fax: 412-624-8854<br/></p>\
			");
			parent.append(tab2);
			
            var tab3 = $("<div/>").attr('id', 'splashDialogTab3');
            $(tab3).html("");
            parent.append(tab3);
			
			var tab4 = $("<div/>").attr('id', 'splashDialogTab4');
            $(tab4).html("<table>\
				<tr><td>First Name:<span class='uc-required'> *</span></td><td><input type='text' name='firstName'/></td><td></td></tr>\
				<tr><td>Last Name:<span class='uc-required'> *</span></td><td><input type='text' name='lastName'/></td><td></td></tr>\
				<tr><td>Affiliation:</td><td><input type='text' name='affiliation'/></td><td></td></tr>\
				<tr><td>Personal URL:</td><td><input type='text' name='url'/></td><td></td></tr>\
				<tr class='uc-padding'><tr/>\
				<tr><td class='uc-hint'>Username must have between 4 and 20 characters.</td><tr/>\
				<tr><td>Username:<span class='uc-required'> *</span></td><td><input type='text' name='username'/></td><td></td></tr>\
				<tr><td>E-mail address:<span class='uc-required'> *</span><span class='uc-required uc-hint'>.edu only</span></td><td><input type='text' name='email'/></td><td></td></tr>\
				<tr class='uc-padding'><tr/>\
				<tr><td class='uc-hint'>Passwords must have between 8 and 32 characters.</td></tr>\
				<tr><td>Password:<span class='uc-required'> *</span></td><td><input type='password' name='password'/></td><td></td></tr>\
				<tr><td>Confirm Password:<span class='uc-required'> *</span></td><td><input type='password' name='confirmPassword'/></td><td></td></tr>\
				<tr class='uc-padding'><tr/>\
				<tr><td><button id='uc-submit'>Submit</button></td><td class='uc-hint uc-required'></td><td></td><tr/>\
			</table>");
            parent.append(tab4);
            $(parent).tabs();
        },
		
        reset: function(){
        },
    
        _refresh: function(){
            this._trigger("change");
        },
		
        // _setOption is called for each individual option that is changing
        _setOption: function(key, value){
            // prevent invalid color values
            if (/targetObj/.test(key) && (value == null)){
                return;
            }
            // in 1.9 would use _super
            $.Widget.prototype._setOption.call(this, key, value);
            this._refresh();
        },
		
        // _setOptions is called with a hash of all options that are changing
        // always refresh when changing options
        _setOptions: function(){
            // in 1.9 would use _superApply
            $.Widget.prototype._setOptions.apply(this, arguments);
            this._refresh();
        },
		
		userCreationSetup: function(){
			var ucTab = $("#splashDialogTab4");
			var emailRegex = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
			var eduRegex = /.+\.edu$/;
			
			ucTab.find("input").on('input', function(){
				var input = $(this);
				var name = input.attr('name');
				var confirm = input.parent().next();
				if(input.val().length == 0) {
					confirm.html("");
					return;
				}
				
				var correctImage = "<img src='splash/css/images/correct.png'>";
				var incorrectImage = "<img src='splash/css/images/incorrect.png'>";
				
				if(name == "firstName" || name == "lastName") {
					if(input.val().length == 0) {
						confirm.html(incorrectImage);
					} else {
						confirm.html(correctImage);
					}
				} else if(name == "username") {
					if(input.val().length < 4 || input.val().length > 20) {
						confirm.html(incorrectImage);
					} else {
						$.ajax({
							type: "POST",
							url: "splash/php/usernameAvailable.php",
							data: {
								username: input.val()
							}
						}).done(function(response) {
							if(response == '1') {
								confirm.html(correctImage);
							} else {
								confirm.html(incorrectImage + '<span class="uc-hint uc-required"> Username taken.</span>');
							}
						}).fail(function() {
							confirm.html(incorrectImage);
						});
					}
				} else if(name == "email") {
					if(emailRegex.test(input.val()) && eduRegex.test(input.val())) {
						$.ajax({
							type: "POST",
							url: "splash/php/emailAvailable.php",
							data: {
								email: input.val()
							}
						}).done(function(response) {
							if(response == '1') {
								confirm.html(correctImage);
							} else {
								confirm.html(incorrectImage + '<span class="uc-hint uc-required"> E-mail taken.</span>');
							}
						}).fail(function() {
							confirm.html(incorrectImage);
						});
					} else {
						confirm.html(incorrectImage);
					}
				} else if(name == "password") {
					if(input.val().length < 8 || input.val().length > 32) {
						confirm.html(incorrectImage);
					} else {
						confirm.html(correctImage);
					}
				} else if(name == "confirmPassword") {
					if(input.val() == ucTab.find('input[name="password"]').val()) {
						confirm.html(correctImage);
					} else {
						confirm.html(incorrectImage);
					}
				}
			});
			
			$("#uc-submit").click(function() {
				if(ucTab.find('input[name="firstName"], input[name="lastName"]').val().length == 0 || !emailRegex.test(ucTab.find('input[name="email"]').val()) || !eduRegex.test(ucTab.find('input[name="email"]').val()) || ucTab.find('input[name="username"]').val().length < 4 || ucTab.find('input[name="username"]').val().length > 20 || ucTab.find('input[name="password"]').val().length < 8 || ucTab.find('input[name="password"]').val().length > 32 || ucTab.find('input[name="confirmPassword"]').val() != ucTab.find('input[name="password"]').val()) {
					$("#uc-submit").parent().next().text("Required fields are missing or invalid.");
				} else {
					var data = {
						firstName: ucTab.find('input[name="firstName"]').val(),
						lastName: ucTab.find('input[name="lastName"]').val(),
						username: ucTab.find('input[name="username"]').val(),
						affiliation: ucTab.find('input[name="affiliation"]').val(),
						url: ucTab.find('input[name="url"]').val(),
						email: ucTab.find('input[name="email"]').val(),
						password: ucTab.find('input[name="password"]').val()
					}
					
					$.ajax({
						type: "POST",
						url: "splash/php/createUser.php",
						data: data
					}).done(function(response) {
						if(response == "1") {
							ucTab.find("input").val("");
							ucTab.find("input").parent().next().html("");
							alert("Please check your e-mail to confirm your account creation. After confirming your account, press 'Enter Site' to log in.");
						} else {
							console.log(response);
							alert("Your account creation failed. Please try again.");
						}
					}).fail(function() {
						console.log("error");
						alert("Your account creation failed. Please try again.");
					});
				}
			});
		}
    });
});
