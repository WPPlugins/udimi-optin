(function($){
	var updateHash = function(cb, forceConnect){
		$.ajax({
			type: 'get',
			url: config.getCodeUrl,
			crossDomain: true,
			dataType: "jsonp",
			success: function(data){
				if(data.hash){
                    if(forceConnect){
                        data.force_connect = 1;
                    }
					$.post(config.updateCodeUrl,data);
					if(cb){
						cb(data);
					}
				}
			}
		});
	};

	$(document).ready(function(){
		if(!config.key){
			updateHash()
		}
	});
	$(document).on('click', '#udimi-optin-connect-button', function(){
		$(this).attr('disabled','disabled');
		updateHash(function(data){
			if(data.error){
				alert(data.error);
			}else{
				window.location.reload();
			}
		}, true);
	});
    $(document).on('click', '#udimi-optin-status-button', function(){
        $(this).attr('disabled', 'disabled');
        $.post(config.toggleStatusUrl, {'toggle_status': '1'}, function(){
            window.location.reload();
        })
    });
})(jQuery);