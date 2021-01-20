if( $('#message').length) {
  $('#message').toggle(1000).delay(3500).toggle(1000);
}
$("#form_close").on('click', function(){
  window.location.href="/callendar/cancelEvent";
})
$("#form_delete").on('click', function(){
  window.location.href="/deleteEvent/"+$(this).attr('data_id');
})
$("#file_delete").on('click', function(){
  window.location.href="/deleteFile/"+$(this).attr('file_id')+"/"+$(this).attr('event_id');
})
$('#form_file').on('change',function(e){
  var fileName =  e.target.files[0].name;;
  $(this).next('.custom-file-label').html(fileName);
})

$('input[type="date"]').on("change", function(){
  if($("#form_startDate").val()<=$("#form_endDate").val()) $("#form_save").prop("disabled", false);
  else $("#form_save").prop("disabled", true);
})
