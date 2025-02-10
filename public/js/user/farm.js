$(document).ready(function() {
    $(document).on('submit', '#add-farm-form',  function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: "/user/add_farm",
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false, 
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                    if (data.success) {
                        $('#addFarmModal').modal('hide'); 
                        $('#add-farm-form')[0].reset(); 
                        $('.modal-backdrop').remove(); 
                        $('body').removeClass('modal-open'); 
                        location.reload();
                    }     
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while adding the farm. Please try again.');
            }
        });
    });
});
  
  