<!-- Add Farm Modal -->
<div class="modal fade" id="addFarmModal" tabindex="-1" aria-labelledby="addFarmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">  <!-- Add modal-dialog-centered here -->
        <div class="modal-content">
            <form id="add-farm-form" method="POST" action="{{ route('farm.store') }}"> 
                <div class="modal-header">
                    <h5 class="modal-title" id="addFarmModalLabel"><strong>New Farm</strong></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label fw-bold my-0">Location</label><br>
                        <small class="form-text text-muted">Tap "Find my farm", click the
                            area where your farm is located to get Coordinates. </small>
                        <div class="input-group">
                            <input type="text" class="form-control" id="location" name="location" required>
                            <input type="hidden" id="address_input" name="address">
                            <button type="button" class="btn btn-outline-success fw-bold" id="find_farm" onclick="getUserLocation()">Find my farm</button>
                        </div>
                        {{--        <div id="map" class="map-container"></div>
                            <div id="address" class="mt-2 text-muted" style="font-size: 14px;"></div> --}}
                    </div>
                    <div class="mb-3">
                            <label for="municipal" class="form-label fw-bold">Municipal</label>
                            <select class="form-select" id="municipal" name="municipal" required>
                                <option value="" disabled selected hidden>Select Municipal</option>
                                <option value="Alaminos">Alaminos</option>
                                <option value="Calauan">Calauan</option>
                                <option value="Cavinti">Cavinti</option>
                                <option value="Famy">Famy</option>
                                <option value="Kalayaan">Kalayaan</option>
                                <option value="Liliw">Liliw</option>
                                <option value="Luisiana">Luisiana</option>
                                <option value="Lumban">Lumban</option>
                                <option value="Mabitac">Mabitac</option>
                                <option value="Magdalena">Magdalena</option>
                                <option value="Majayjay">Majayjay</option>
                                <option value="Nagcarlan">Nagcarlan</option>
                                <option value="Paete">Paete</option>
                                <option value="Pagsanjan">Pagsanjan</option>
                                <option value="Pakil">Pakil</option>
                                <option value="Pangil">Pangil</option>
                                <option value="Pila">Pila</option>
                                <option value="Rizal">Rizal</option>
                                <option value="San Pablo">San Pablo</option>
                                <option value="Santa Cruz">Santa Cruz</option>
                                <option value="Santa Maria">Santa Maria</option>
                                <option value="Siniloan">Siniloan</option>
                                <option value="Victoria">Victoria</option>
                            </select>
                        </div>
                    <div class="mb-3">
                        <label for="district" class="form-label fw-bold">District</label>
                        <select class="form-select" id="district" name="district" required>
                            <option value="" disabled selected hidden>Select District</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hectares" class="form-label fw-bold">Hectares</label>
                        <input type="number" class="form-control" id="hectares" name="hectares" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="tree_age" class="form-label fw-bold">Tree Age</label>
                        <input type="number" class="form-control" id="tree_age" name="tree_age" required>
                    </div>
                    <div class="mb-3">
                        <label for="planted_coconut" class="form-label fw-bold">Planted Coconut</label>
                        <input type="number" class="form-control" id="planted_coconut" name="planted_coconut" required>
                    </div>
                    <div class="mb-3">
                        <label for="variety" class="form-label fw-bold">Variety</label>
                        <select class="form-select" id="variety" name="variety" required>
                            <option value="" disabled selected hidden>Select Variety</option>
                            <option value="Laguna Tall">Laguna Tall</option>
                            <option value="Dwarf Coconut">Dwarf Coconut</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary fw-100 rounded rounded-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success fw-100 rounded rounded-3">Add Farm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
                             
                            </script>

<script>

    function openMapApp() {
        const isAndroid = /android/i.test(navigator.userAgent);
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);

        if (isIOS) {
            // Opens Apple Maps on iOS
            window.location.href = 'maps://';
        } 
        else if (isAndroid) {
            // Opens Google Maps on Android
            window.location.href = 'geo:0,0'; // Opens at user's current location
        } 
        else {
            // Fallback for other devices
            window.open('https://maps.google.com', '_blank');
        }
    }

    function getUserLocation() {
        openMapApp();
    }

    $(document).ready(function() {
        $('#location').on('input', function() {
            let value = $(this).val();
            value = value.replace(/[°NEWS]/g, '')
                        .replace(/\s*,\s*/g, ',')
                        .trim();
            $(this).val(value);
            if ($(this).val()) {
                $('#find_farm').prop('disabled', true);
            } else {
                $('#find_farm').prop('disabled', false);
            }

            const coordinates = $(this).val().trim();
            if (coordinates) {
                const [lat, lng] = coordinates.split(',').map(coord => parseFloat(coord.trim()));
                if (!isNaN(lat) && !isNaN(lng)) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                $('#address_input').val(data.display_name);
                            } else {
                                $('#address_input').val('Address not found');
                            }
                        })
                        .catch(() => {
                            $('#address_input').val('Error fetching address');
                        });
                } else {
                    $('#address_input').val('');
                }
            } else {
                $('#address_input').val('');
            }
        });


    });

    // $('#location').on('input', function() {
        
    // });

    $(document).ready(function() {
        $('#hectares, #planted_coconut').on('input', function() {
            const hectares = parseFloat($('#hectares').val()) || 0;
            const plantedCoconut = parseInt($('#planted_coconut').val()) || 0;
            const maxCoconuts = hectares * 250;
            if (plantedCoconut > maxCoconuts) {
                $('#planted_coconut').val('');
                $('#planted_coconut')
                    .after(`<small class="text-danger" id="coconut-warning">Planted Coconut exceeds the limit of ${maxCoconuts} for the entered of ${hectares} hectares.</small>`);
            } else {
                $('#coconut-warning').remove();
            }
        });
    });
</script>


