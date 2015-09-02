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
		$('#restool').html('En HEX es <strong>'+ rgbToHex(parseInt(c[0]), parseInt(c[1]), parseInt(c[2])) +'</strong>.').addClass('showme').fadeIn();
	});

	// HEX to RGB
	$('#sct_hex_rgb_sub').click(function()
	{
		$('#restool').html('En RGB es <strong>'+ hexToRgb($('#sct_hex_rgb_i').val()) +'</strong>.').addClass('showme').fadeIn();
	});

	// RGB to CMYK
	$('#sct_rgb_cmyk_sub').click(function()
	{
		var c = $('#sct_rgb_cmyk_i').val().split(',');
		$('#restool').html('En CMYK es <strong>'+ rgb2cmyk(parseInt(c[0]), parseInt(c[1]), parseInt(c[2])) +'</strong>.').addClass('showme').fadeIn();
	});

	// CMYK to RGB
	$('#sct_cmyk_rgb_sub').click(function()
	{
		var c = $('#sct_rgb_cmyk_i').val().split(',');
		$('#restool').html('En RGB es <strong>'+ cmyk2rgb(parseInt(c[0]).toFixed(2)) +'</strong>.').addClass('showme').fadeIn();
	});
/**/
})(jQuery); // document ready

function componentToHex(c)
{
	var hex = c.toString(16);
	return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r,g,b)
{
	//remove spaces from input RGB values, convert to int
	var r = filter_color_values(r);
	var g = filter_color_values(g);
	var b = filter_color_values(b);

	return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function hexToRgb(hex)
{
	return [(bigint = parseInt(hex, 16)) >> 16 & 255, bigint >> 8 & 255, bigint & 255].join();
}

function rgb2cmyk(r,g,b)
{
	var computedC = 0;
	var computedM = 0;
	var computedY = 0;
	var computedK = 0;

	//remove spaces from input RGB values, convert to int
	var r = filter_color_values(r);
	var g = filter_color_values(g);
	var b = filter_color_values(b);

	// BLACK
	if( r==0 && g==0 && b==0 )
	{
		computedK = 1;
		return [0,0,0,1];
	}

	computedC = 1 - (r/255);
	computedM = 1 - (g/255);
	computedY = 1 - (b/255);

	var minCMY = Math.min(computedC, Math.min(computedM,computedY));

	computedC = Math.round(((computedC - minCMY) / (1 - minCMY))*10000)/10000;
	computedM = Math.round(((computedM - minCMY) / (1 - minCMY))*10000)/10000;
	computedY = Math.round(((computedY - minCMY) / (1 - minCMY))*10000)/10000;
	computedK = Math.round((minCMY)*10000)/10000;

	return [computedC,computedM,computedY,computedK];
}

function cmyk2rgb(c, m, y, k, normalized)
{
    c = (c / 100);
    m = (m / 100);
    y = (y / 100);
    k = (k / 100);

    c = c * (1 - k) + k;
    m = m * (1 - k) + k;
    y = y * (1 - k) + k;

    var r = 1 - c;
    var g = 1 - m;
    var b = 1 - y;

    if( !normalized )
    {
        r = Math.round(255 * r);
        g = Math.round(255 * g);
        b = Math.round(255 * b);
    }

    return [r,g,b]
}

function filter_color_values(x)
{
	// remove spaces from input RGB values, convert to int
	var x = parseInt((''+r).replace(/\s/g, ''), 10);

	if( x==null || isNaN(x) )
	{
		alert('Los valores RGB no son numéricos.');
		return;
	}

	if( x<0 || x>255 )
	{
		alert('Los valores RGB deben estar en el rango 0 a 255.');
		return;
	}

	return x;
}