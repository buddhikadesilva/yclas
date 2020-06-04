$('#from_date').datepicker();
$('#to_date').datepicker();

$('#from_date').datepicker().on('changeDate', function(e){
	$('form[name="date"]').submit();
});
$('#to_date').datepicker().on('changeDate', function(e){
	$('form[name="date"]').submit();
});
