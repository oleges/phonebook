var toggleFormDisplay = function() {
  var prompt = $("#prompt");
  var formAdd = $("#form-add");
  if ($.trim(prompt.html())) {
    formAdd.hide();
    $("#add-first-contact").click(function() {
      prompt.remove();
      formAdd.show();
    });
  }
}

var getStreets = function() {
  var selectedCity = $(this).val();
  var formAddStreet = $("#form-add-street");
  if (selectedCity == "") {
    formAddStreet.val("");
    formAddStreet.prop("disabled", true);
  } else {
    formAddStreet.prop("disabled", false);
    $.ajax({
      url: "ajax-get-streets.php",
      method: "POST",
      data: { city_id : selectedCity },
      dataType: "html",
      success: function(responseData) {
        if (responseData == '') {
          alert('Ошибка при обращении к базе данных');
        } else {
          formAddStreet.html(responseData);
        }
      },
      error: function (xhr, errorType, thrownError) {
        alert('Ошибка при отправке запроса: ' + xhr.status + ' ' + thrownError);
      }
    });
  }
}

$(document).ready(function() {
  toggleFormDisplay();
  $('#form-add-city').change(getStreets);
});
