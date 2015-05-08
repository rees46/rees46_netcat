function rees46_callback() {
	REES46.recommend({
		recommender_type: 'popular'
	}, function(r) {
		console.log(r);

		$.ajax({
			url: '/netcat/modules/rees46/goods.php',
			data: {
				items: r,
				recommended_by: 'popular'
			},
			success: function(r) {
				$('#rees46_recomended').append(r);
			}
		})
	});
}

function rees46_good_view(id, price, category) {
	REES46.addReadyListener(function () {
			REES46.pushData('view', {
			item_id: id,
			price: price,
			categories: [category]
		});
	});
}