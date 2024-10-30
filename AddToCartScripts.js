jQuery(document).ready(function($) {

var basePrice = $('.finalPrice').html();
basePrice = Number(basePrice.replace(/[^0-9\.]+/g,""));
//alert(basePrice);

refreshPrice();

function refreshPrice() {


	var TotalAdditional = 0;
	var finalPrice = basePrice;

	/* Additional Costs From Selections */
	$('.cartNinjaForm select').each(function() {
		var ValueDetails = $(this).val(); 

		var AdditionalCost = parseFloat(ValueDetails.substring(ValueDetails.indexOf('+')+1));
		
		//alert(AdditionalCost);

		TotalAdditional += AdditionalCost;

	});

	/* Additional Costs From Radio Buttons */
	$('.cartNinjaForm input[type="radio"], .cartNinjaForm input[type="checkbox"]').each(function() {
		if($(this).is(":checked")) {
			var ValueDetails = $(this).val();
			var AdditionalCost = parseFloat(ValueDetails.substring(ValueDetails.indexOf('+')+1));

			TotalAdditional += AdditionalCost;
		}
	});
	finalPrice += TotalAdditional;
	finalPrice = addCommas(CurrencyFormatted(finalPrice));

	$('.finalPrice').fadeOut('fast');
	$('.finalPrice').html(finalPrice);
	$('.finalPrice').fadeIn('fast');

	//alert(TotalAdditional);
	//alert(finalPrice);
}

$('.cartNinjaForm select').change(function(event) {
  refreshPrice();
});

$('.cartNinjaForm input[type="radio"], .cartNinjaForm input[type="checkbox"]').change(function(event) {
  refreshPrice();
});


function CurrencyFormatted(amount)
{
	var i = parseFloat(amount);
	if(isNaN(i)) { i = 0.00; }
	var minus = '';
	if(i < 0) { minus = '-'; }
	i = Math.abs(i);
	i = parseInt((i + .005) * 100);
	i = i / 100;
	s = new String(i);
	if(s.indexOf('.') < 0) { s += '.00'; }
	if(s.indexOf('.') == (s.length - 2)) { s += '0'; }
	s = minus + s;
	return s;
} //End of Currency Formatter

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	finalNumber=x1 + x2;
	return finalNumber;
} // End of Comma Formatter

});
