$(document).ready(function(){ 
	// prevent submit
	$("#doeditreview").attr("type", "button"); //button for save draft
		
	$("#doeditreview").click( function(){	
		if (!confirm('You are trying to submit this editted post for review. Are you sure to do this?')) {
            stopImmediatePropagation();
            preventDefault();
        }
		
		var postid = $(this).data("postid");		
		var userid = $(this).data("editorid");
		//CKEDITOR.instances['content'].updateElement();
		var formdata = $("form[name=q_editreview]").serializeArray();
		
		var dataArray = {
			postid: postid,
			userid: userid,
			formdata: formdata,
		};
			
		var senddata = JSON.stringify(dataArray);
		console.log("sending: "+senddata);
				
		// send ajax
		$.ajax({
			type: "POST",
			url: editreviewAjaxURL,
			data: { ajaxdrdata: senddata },
			dataType:"json",
			cache: false,
			success: function(data){
				console.log("got server data:");
				console.log(data);
					
				if(typeof data.error !== "undefined"){
					alert(data.error);
				}
				else{
					alert("Your edition has been successfully submited for review.");
					window.location.replace(postid);
				}
			},
			error: function(data){
				console.log("Ajax error:");
				console.log(data);
			}
		});
	});
});