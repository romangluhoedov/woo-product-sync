jQuery(document).ready(function($) {
	var $ = jQuery;
	var done_num = $('.progress').find('.done');
	var count_num = $('.progress').find('.count');
	var final_status;
	var offset;

	$('.sync-products').click(function(e) {
		e.preventDefault();

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action : 'sync_products',
			},
            beforeSend : function(results){
				$('.error').addClass('hidden');
				$('.sync-products').addClass('disabled');
				$('.sync-info').addClass('hidden');
				$('.upload-started').addClass('hidden');
				$('.upload-info').addClass('hidden');
				$('.progress').addClass('hidden');
                $('.sync-started').removeClass('hidden');
                $('.spinner').addClass('is-active');
                done_num.text('0');

            },
			success : function(results){

				$('.spinner').removeClass('is-active');

				results = $.parseJSON(results);

				if (results.status == true){
					$('.sync-info').removeClass('hidden');

					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action : 'count_wc_products',
						},
						beforeSend : function(results){
							$('.error').addClass('hidden');
							$('.upload-started').removeClass('hidden');
							$('.spinner').addClass('is-active');
						},

						success : function(results){
							results = $.parseJSON(results);

							if (!results.error) {

								let i = 0;

								count_num.text(results.products_count);

								$('.progress').removeClass('hidden');

								recursiveCall();

								function recursiveCall() {
									$.ajax({
										type: 'POST',
										url: ajaxurl,
										data: {
											action : 'add_wc_products',
											offset : i,
											products_count : results.products_count
										},
										success : function(result){
											result = $.parseJSON(result);
											done_num.text(result.query_progress);
											offset = i;

											i = i + 1;
											if (i < results.products_count) recursiveCall();

											if (i >= results.products_count) check(result.success);
										}
									})
								}

								function check(final_status) {

									if (typeof(final_status) != "undefined" && final_status !== null && final_status == true) {
										$('.spinner').removeClass('is-active');
										$('.sync-products').removeClass('disabled');
										$('.upload-info').removeClass('hidden');
									}
									else {

										if (done_num.val() == 0) {
											$('.spinner').removeClass('is-active');
											$('.sync-products').removeClass('disabled');
											$('.error').text("Nothing wasn't uploaded!");
											$('.error').removeClass('hidden');
										}
										else {
											$('.spinner').removeClass('is-active');
											$('.sync-products').removeClass('disabled');
											$('.error').text("Warning: Some of the products wasn't uploaded!");
											$('.error').removeClass('hidden');
										}
									}
								}


							}
							else {
								$('.error').text("Error while counting products in DB");
								$('.error').removeClass('hidden');
							}
						}
					})

				}
				else {
					$('.error').text(results.status);
					$('.error').removeClass('hidden');
				}
			}
		})


	});

});