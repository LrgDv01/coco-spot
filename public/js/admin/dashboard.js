$(document).ready(function () {
    $.ajax({
      type: "GET",
      url: "/admin/get_dashboard_info",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        $('#count_today_technicians').text(response.count_techicians);
        $('#count_today_farmers').text(response.count_farmers);
        $('#count_today_farms').text(response.count_farms);
        $('#count_doa').text(response.count_doa);
      }
    });
});