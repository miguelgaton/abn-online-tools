/**/
(function($)
{
	// string length
	$('#sct_string_length_sub').click(function()
	{
		$('#restool').html('Detectados <strong>'+ $('#sct_string_length_ta').val().length +' caracteres</strong>.').addClass('showme').fadeIn();
	});

	// word count
	$('#sct_word_count_sub').click(function()
	{
		$('#restool').html('Detectadas <strong>'+ $('#sct_word_count_ta').val().replace(/^[\s,.;]+/, "").replace(/[\s,.;]+$/, "").split(/[\s,.;]+/).length +' palabras</strong>.').addClass('showme').fadeIn();
	});

	// add colorpicker
	$('.color-picker').iris({
		width: 400,
		hide: false
	});

	// RGB to HEX
	$('#sct_rgb_hex_sub').click(function()
	{
		var c = $('#sct_rgb_hex_i').val().split(',');
		console.log(c);
		$('#restool').html('En HEX es <strong>'+ rgbToHex(parseInt(c[0]), parseInt(c[1]), parseInt(c[2])) +'</strong>.').addClass('showme').fadeIn();
	});

	// HEX to RGB
	$('#sct_hex_rgb_sub').click(function()
	{
		$('#restool').html('En RGB es <strong>'+ hexToRgb($('#sct_hex_rgb_i').val()) +'</strong>.').addClass('showme').fadeIn();
	});
/**/
})(jQuery); // document ready

function componentToHex(c)
{
	var hex = c.toString(16);
	return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b)
{
	 return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function hexToRgb(hex)
{
	return [(bigint = parseInt(hex, 16)) >> 16 & 255, bigint >> 8 & 255, bigint & 255].join();
}