$(document).ready(function(){

	//	*	*	*	*	*	*	*	*	*	*
	// Display Table
	//	*	*	*	*	*	*	*	*	*	*
	
	$('table').each(function () {
		$(this).find('tr:odd').addClass('odd');
		$(this).find('tr:even').addClass('even');
	});

	$('table tr').each(function () {
		$(this).find('td:eq(0)').addClass('column-first');
		$(this).find('td:eq(1)').addClass('column-second');
		$(this).find('td:eq(2)').addClass('column-third');
		$(this).find('td:eq(3)').addClass('column-fourth');
		$(this).find('td:eq(4)').addClass('column-fifth');
		$(this).find('td:eq(5)').addClass('column-sixth');
		$(this).find('td:eq(6)').addClass('column-seventh');
		$(this).find('td:eq(7)').addClass('column-eighth');
	});
	
	
	
	//	*	*	*	*	*	*	*	*	*	*
	// Check Page
	//	*	*	*	*	*	*	*	*	*	*
	
	// Select all content in textarea for easier copy & paste
	$('.selectAll').click(function () {
		$(this).select();
	});
	
	$('div.question:even').addClass('even');
	
	
	//	*	*	*	*	*	*	*	*	*	*
	// Check Page
	//	*	*	*	*	*	*	*	*	*	*
	
	var $totalMatch = 0;
	var $totalError = 0;
	
	// Checking if answers and difficulty levels match
	$('table.check tbody tr').find('td.screenResult').each(function() {
		var $result = $(this).text();
		
		if($result == 'Pass') {
			$(this).addClass('match')
			++$totalMatch;
		} else {
			$(this).addClass('error');
			++$totalError;
		}
	});
	
	$numRows = $totalMatch + $totalError; 
	
	$('span#totalMatch').text($totalMatch);
	$('span#totalError').text($totalError);
	$('span#numRows').text($numRows);
	
	
	$('table.check tbody tr').find('td.hasKeyword').each(function() {
		var $result = $(this).text();
		
		if($result !== '0') {
			$(this).addClass('keyword')
		} else {
			$(this).addClass('noKeyword')
		}
	});
});