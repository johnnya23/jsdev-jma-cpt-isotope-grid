
jQuery(window).load(function(){


	var $container = jQuery('.jma-iso-items-wrap').isotope({
		itemSelector: '.jma-iso-item',
		layoutMode: 'fitRows',
	});
	// bind filter button click
	jQuery('#filters').on( 'click', '.trigger', function() {
		var filterValue = '.' + jQuery( this ).attr('data-filter');//add the dot to grab the classes
		filterValue = (filterValue == '.*')? '*':filterValue;
		$container.isotope({ filter: filterValue });
	});
	jQuery(document).ready(function($){
		// change is-checked class on buttons
		//     SET DEFAULT CATEGORY
		$default_cat = $('.isotope-wrap').data('init');
		if($default_cat){
			$init_filters = $('#filters');
            $init_filters.find('.is-checked').removeClass('is-checked');
			$container.isotope({ filter: '.' + $default_cat });
            $init_filters.find('[data-filter="' + $default_cat + '"]').addClass('is-checked');
		}
		$('.trigger').click( function() {
			$clicked = $( this );
			$filters = $clicked.parents('#filters');
			$filters.find('.is-checked').removeClass('is-checked');
			$filters.find('.jma-show').removeClass('jma-show');
			$filters.find('.jma-hide').removeClass('jma-hide');

			$clicked.addClass('is-checked');
			if($filters.find('.btn-group').length > 1){//don't bother with multi layer if only one btn-group
				$current_tax = $clicked.data('filter');
				$level = $clicked.parents('.btn-group').data('level');
				$clicked.parents('#filters').find('.btn').each(function(){//console.log($current_tax);
					$checked = $( this );
					$kids_array = $checked.data('kids').split(' ');//add is-checked class to parents of clicked item
					if($.inArray($current_tax, $kids_array) >= 0){
						$checked.addClass('is-checked');
						$this_checked_level = $checked.parents('.btn-group').data('level');
						$this_filter = $checked.data('filter');
						$checked.parents('#filters').find(('[data-level="'+($this_checked_level+1)+'"]')).find('.btn').each(function(){
							//go down a level and cycle thru buttons
							$next_level_array = $(this).data('parents').split(' ');//show siblings of checked buttons (parents) on next level
							if($.inArray($this_filter, $next_level_array) >= 0){
								$(this).parent().addClass('jma-show');
							}
						});
					}
				});
				$clicked.parents('#filters').find(('[data-level="'+($level+1)+'"]')).find('.btn').each(function(){
					//go down a level and cycle thru buttons
					$below_level_array = $(this).data('parents').split(' ');//show children of clicked item on next level
					$this_filter = $clicked.data('filter');
					if($.inArray($this_filter, $below_level_array) >= 0){
						$(this).parent().addClass('jma-show');
					}
				});
				$filters.find('.btn-group').each(function(){//hide empty btn groups
					if($(this).find('.jma-show').length){
								$(this).removeClass('jma-hide');
							}else{
								$(this).addClass('jma-hide');
							}
				});
			}

		});

	});
});
