$(document).on('submit', '#update_profile', function(e) {
  e.preventDefault();
  
  let formData = new FormData(this);
  showLoading();
  $.ajax({
      url: "/user/update-profile",
      method: "POST",
      data: formData,
      dataType: 'JSON',
      contentType: false,
      processData: false,
      success: function(response) {
        hideLoading();
        global_showalert(response.message, 'Congrats', 'green', '/user/home');
      },
      error: function(response) {
        hideLoading();
          let errors = response.responseJSON.errors;
          let errorMsg = '';
          $.each(errors, function(key, value) {
              errorMsg += value[0] + '\n';
          });
          global_showalert(errorMsg, 'Alert!', 'red');
      }
  });
});

$(document).on('click', '.directtologout', function (e) { 
  $('#logoutform').trigger('submit');
  location.reload();
});




