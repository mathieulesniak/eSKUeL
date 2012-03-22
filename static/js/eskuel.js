$(document).ready(function(){

	$('#nav a').tooltip({
		'placement': 'bottom'
	});

	$('tr').click(function(){
		var check = $(this).find('input[type=checkbox]').first();
		check.attr('checked', !check.attr('checked'));
		if ( !check.attr('checked') ) {
			$(this).removeClass('selected');
		}
		else
		{
			$(this).addClass('selected');
		}
	});

	$('table.editable td').dblclick(function(){
		alert('edit ' + $(this).attr('class') );
	});

	var table = $(".datas");
	var c = $("thead", table).clone();
	$('#nav').append( c.attr('id', 'float').addClass('datas table table-condensed') );
	$("thead th", table).each(function(i){
		var v = $('th', c)[i];
		var c = $("td", $("tbody tr", table)[0] )[i];
		$(v).width( $(this).width() );
		$(c).width( $(this).width() );
	});
	$("thead", table).hide();

	$('body').css('padding-top', $('#nav').height() );

	$("a[data-toggle='modal']").click(function()
	{
		target = $( $(this).attr('data-target') );
		url = $(this).attr('href');
		if ( target.length )
		{
			var html = '';
			if ( icon = $(this).attr('data-icon') )
			{
				html = '<i class="icon-large ' + icon + '"></i> ';
			}
			if ( title = $(this).attr('data-header') )
			{
				html += title
				$('.modal-header h3', target).html( html );
			};
			$('.modal-body', target).load(url, {'format': 'html'});
			console.log( target )
		}
	});

});