$(document).ready(function () {
    $.ajax({
      type: "GET",
      url: "/admin/get_dashboard_info",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        $('#count_today_farmers').text(response.count_farmers);
        $('#count_today_farms').text(response.count_farms);
        $('#count_today_technicians').text(response.count_techicians);
        $('#count_correct_diagnosis').text(response.count_correct_diagnosis);
        $('#count_incorrect_diagnosis').text(response.count_incorrect_diagnosis);
        $('#count_healthy_farms').text(response.count_healthy_farms);
        $('#count_infected_farms').text(response.count_infected_farms);
        $('#affected_rate').text(response.affected_rate + ' %');
        $('#count_treated_farms').text(response.count_treated_farms);
      }
    });
});