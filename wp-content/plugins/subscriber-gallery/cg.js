function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}


function sg_initing() {
	
var sg_reg = getUrlVars()["cg-reg"];
if(sg_reg == "1") {
	jQuery("#sg-statement").show();
}

}

function repl_titl() {
	if(jQuery("#biz_name").val() == "Company Name") {
		jQuery("#biz_name").val(""); 
	} else if(jQuery("#biz_name").val() == "") {
		jQuery("#biz_name").val("Company Name"); 
	}
}

function repl_user() {
	if(jQuery("#user_login").val() == "Username") {
		jQuery("#user_login").val(""); 
	} else if(jQuery("#user_login").val() == "") {
		jQuery("#user_login").val("Username"); 
	}
}

function repl_email() {
	if(jQuery("#user_email").val() == "E-Mail") {
		jQuery("#user_email").val(""); 
	} else if(jQuery("#user_email").val() == "") {
		jQuery("#user_email").val("E-Mail"); 
	}
}
			
function repl_uname() {
	if(jQuery("#user_name").val() == "Your Name") {
		jQuery("#user_name").val(""); 
	} else if(jQuery("#user_name").val() == "") {
		jQuery("#user_name").val("Your Name"); 
	}
}			
			
function repl_oc() {
	if(jQuery("#user_occupation").val() == "Occupation") {
		jQuery("#user_occupation").val(""); 
	} else if(jQuery("#user_occupation").val() == "") {
		jQuery("#user_occupation").val("Occupation"); 
	}
}			
			
function repl_addrs() {
	if(jQuery("#user_address").val() == "Address") {
		jQuery("#user_address").val(""); 
	} else if(jQuery("#user_address").val() == "") {
		jQuery("#user_address").val("Address"); 
	}
}				
			
function repl_addrs2() {
	if(jQuery("#user_address2").val() == "Address 2") {
		jQuery("#user_address2").val(""); 
	} else if(jQuery("#user_address2").val() == "") {
		jQuery("#user_address2").val("Address 2"); 
	}
}			
			
function repl_city() {
	if(jQuery("#user_city").val() == "City") {
		jQuery("#user_city").val(""); 
	} else if(jQuery("#user_city").val() == "") {
		jQuery("#user_city").val("City"); 
	}
}	

function repl_zip() {
	if(jQuery("#user_zip").val() == "Zip") {
		jQuery("#user_zip").val(""); 
	} else if(jQuery("#user_zip").val() == "") {
		jQuery("#user_zip").val("Zip"); 
	}
}			
			
function repl_cou() {
	if(jQuery("#user_county").val() == "County") {
		jQuery("#user_county").val(""); 
	} else if(jQuery("#user_county").val() == "") {
		jQuery("#user_county").val("County"); 
	}
}			
			
function repl_phone() {
	if(jQuery("#user_phone").val() == "Phone") {
		jQuery("#user_phone").val(""); 
	} else if(jQuery("#user_phone").val() == "") {
		jQuery("#user_phone").val("Phone"); 
	}
}			
			
function repl_web() {
	if(jQuery("#user_website").val() == "Website") {
		jQuery("#user_website").val(""); 
	} else if(jQuery("#user_website").val() == "") {
		jQuery("#user_website").val("Website"); 
	}
}			
			
function repl_tw() {
	if(jQuery("#user_tw").val() == "Twitter Handle") {
		jQuery("#user_tw").val(""); 
	} else if(jQuery("#user_tw").val() == "") {
		jQuery("#user_tw").val("Twitter Handle"); 
	}
}			
			
function repl_fb() {
	if(jQuery("#user_fb").val() == "Facebook URL") {
		jQuery("#user_fb").val(""); 
	} else if(jQuery("#user_fb").val() == "") {
		jQuery("#user_fb").val("Facebook URL"); 
	}
}	