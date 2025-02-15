<!-- Modal for Viewing More Info -->
<div class="modal fade" id="viewMoreInfoModal" tabindex="-1" aria-labelledby="viewMoreInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMoreInfoModalLabel"><strong>Technician Information</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <h5 class="card-title p-0 text-center" id="full-name"></h5>
                    <hr>
                    <p><strong>Address :</strong> <span id="address"></span></p>
                    <p><strong>Contact:</strong> <span id="contact"></span></p>    
                    <p><strong>Years in Sevice:</strong> <span id="years-in-sevice"></span> years</p>    
                    <p><strong>Services:</strong> <span id="services"></span></p>    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-info[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const technicianId = this.getAttribute('data-id');
            getInfo(technicianId);
        });
    });

    function getInfo(technicianId) {
        $.ajax({
            url: `/technician/${technicianId}`, 
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    const technician = data.technician;
                    $('#full-name').text(technician.full_name || 'Not available');
                    $('#address').text(technician.address || 'Not available');
                    $('#contact').text(technician.contact || 'Not available');
                    $('#years-in-sevice').text(technician.years_in_service || 'Not available');
                    $('#services').text(technician.services || 'Not available');
                } else {
                    console.error('Technician not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching technician data:', error);
            }
        });
    }
</script>

