!function ($) {
	$(function() {

		/**
		 * Calculates the totals
		 *
		 * @param $el
		 */
		function calculateTotal($el) {
			var $mask = $el.parents('.mask'),
				finalPrice = 0,
				price = $mask.find('.individual-price').html().replace('$', '');

			if ($el.val() >= 0) {
				finalPrice = $el.val() * price;
			}

			var $totalEl = $mask.find('.total');
			$totalEl.find(' > span').html(finalPrice);
			$totalEl[(finalPrice > 0) ? 'show' : 'hide']();
		}

		/**
		 * Validates our input
		 *
		 * @param $el
		 * @return {Boolean}
		 */
		function isValidQty($el) {
			var $li = $el.parents('li'),
				qtyAvailable = +$li.find('.qty-available').html();

			if ($el.val().length === 0) {
				$el.removeClass('error');
				$li.find('.buy').attr('disabled', 'disabled');
				return false;
			}

			if ($el.val().match(/^\d$/) && $el.val() > 0 && $el.val() <= qtyAvailable) {
				$el.removeClass('error');
				$li.find('.buy').attr('disabled', false);
				return true;
			}

			$el.addClass('error');
			$li.find('.buy').attr('disabled', 'disabled');
			return false;
		}

		$(document).on('keyup', 'ul.marketplace-list input[name="quantity"]', function(e) {
			if (isValidQty($(this))) {
				calculateTotal($(this));
			}
		});

		/**
		 * Un-list yield purchasable item
		 */
		$(document).on('click', 'ul.marketplace-list button.buy', function(e) {
			e.preventDefault();
			var $anchor = $(this),
				$modal = $('#globalMessageModal');

			if (!isValidQty($anchor.parents('.mask').find('input[name="quantity"]'))) {
				return false;
			}

			var urlData = {
				'quantity' : $anchor.parent().find('input[name="quantity"]').val(),
				'purchasable_id' : $anchor.parent().find('input[name="purchasable_id"]').val()
			};

			$('#action-loader').show();
			$.ajax({
				url : $anchor.attr('rel'),
				data : urlData,
				type : 'post',
				dataType : 'json',
				complete: function(response) {
					$('#action-loader').hide();
				},
				success : function(response) {
					if (response.success) {
						Elm.success(response);
						$modal.addClass('success-message').find('h3').html(response.message);
						$modal.modal('show');
						window.setTimeout(function(e) {
							$modal.modal('hide');
							$modal.removeClass('success-message').find('h3').html('');
						}, 4000);
					} else {
						alert(response.message);
					}
				},
				error : function() {
					alert('There was an error with your request.');
				}
			})
		});
	});
}(window.jQuery);