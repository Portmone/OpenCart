$(document).ready(function() {
	$('#payment_portmone_entry_fiscalization_flag').on( "click", function() {
		if ($(this).is(':checked')) {
			$('#div-payment_portmone_entry_fiscalization_settings').removeClass('hidden');
		} else {
			$('#div-payment_portmone_entry_fiscalization_settings').addClass('hidden');
		}
	} );

	var list_of_tax_rates = $('#select-list_of_tax_rates');
	if (list_of_tax_rates.val() === 'tax_rate_each_product') {
		$('#div-payment_portmone_tax_rate_codes').addClass('hidden');
		$('#div-payment_portmone_product_tax_rate_codes').removeClass('hidden');
	} else {
		$('#div-payment_portmone_tax_rate_codes').removeClass('hidden');
		$('#div-payment_portmone_product_tax_rate_codes').addClass('hidden');
	}

	list_of_tax_rates.change(function() {
		var val = $(this).val();
		if (val === 'tax_rate_each_product') {
			$('#div-payment_portmone_tax_rate_codes').addClass('hidden');
			$('#div-payment_portmone_product_tax_rate_codes').removeClass('hidden');
		} else {
			$('#div-payment_portmone_tax_rate_codes').removeClass('hidden');
			$('#div-payment_portmone_product_tax_rate_codes').addClass('hidden');
		}
	});
});
