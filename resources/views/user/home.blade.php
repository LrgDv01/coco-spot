@include('user.partials.__header')
@include('user.partials.__nav')

<style>
    .sticky-alert {
        position: fixed;
        top: 20px;
        left: -100%; 
        transform: translateY(0);
        z-index: 1050;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: slideInFromLeft 0.3s ease-in-out forwards;  
    }

    @keyframes slideInFromLeft {
        0% {
            left: -100%;  
            opacity: 0;
        }
        100% {
            left: 50%; 
            opacity: 1;
            transform: translateX(-50%);
        }
    }

    @keyframes slideOutToRight {
        0% {
            left: 50%; 
            opacity: 1;
        }
        100% {
            left: 100%;  
            opacity: 0;
        }
    }

    .form-check-input.background-green {
        background-color: #198754; 
        border-color: #198754;     
    }
    .form-check-input.background-red {
        background-color: #dc3545;
        border-color: #dc3545;   
    }

    .form-check-input.shadow-green {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); 
        outline-offset: 1px; 
        outline: 1px solid rgba(25, 135, 84, 0.6); 
        transition: outline-color 0.3s ease; 
    }

    .form-check-input.shadow-red {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25); 
        outline-offset: 1px;
        outline: 1px solid rgba(220, 53, 69, 0.6); 
        transition: outline-color 0.3s ease; 
    }
</style>


<style>
    .input-group {
        flex-wrap: nowrap;
    }
    .btn-primary {
        white-space: nowrap;
    }
    .map-container {
        width: 100%;
        height: 0;
        opacity: 0;
        margin-top: 0px;
        transition: all 0.5s ease-in-out;
        overflow: hidden;
    }
    .map-container.visible {
        height: 300px;
        opacity: 1;
        margin-top: 10px;
    }
    @media (max-width: 576px) {
        .form-control, .btn {
            font-size: 14px;
        }
        .map-container.visible {
            height: 200px;
        }
    }
</style>

<main id="main" class="main">
    <!-- Flash Messages -->
    @if (session('accepted'))
        <div class="alert alert-success alert-dismissible fade show sticky-alert" role="alert" id="flashMessage">
            {{ session('accepted') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('declined'))
        <div class="alert alert-warning alert-dismissible fade show sticky-alert" role="alert" id="flashMessage">
            {{ session('declined') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show sticky-alert" role="alert" id="flashMessage">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (auth()->user()->isFarmer())
    <div class="pagetitle mb-3 position-fixed bg-white text-center" 
                style="top:7%; right:0; left:0; z-index: 995; width: 100%;">
        <h1 class="fs-3 mt-3"><strong>My Farm</strong></h1>
        <div class="d-flex flex-start px-4 mb-3">
            <button type="button" class="btn btn-success rounded rounded-3" data-bs-toggle="modal" data-bs-target="#addFarmModal">
                <strong>Add Farm</strong>
            </button>
        </div>
    </div>
    <section class="section farm">
        <div class="container p-0 m-0">
            <div style="margin-top: 100px;">
            </div>
            <div id="farm-list">
                @foreach($farms as $farm)
                    <div class="card mb-2 p-0">
                        <div class="card-body">
                            <h5 class="card-title p-0 d-flex flex-row justify-content-between">
                                <strong>{{ $farm->name }}</strong> 
                            </h5>
                            <hr>
                            <p class="card-text text-truncate">
                                <strong>Address :</strong> {{ $farm->location }}<br>
                                <strong>Variety :</strong> {{ $farm->variety }}<br>
                                <strong>Hectares :</strong> {{ $farm->hectares }}<br>
                                <strong>Tree Age :</strong> {{ $farm->tree_age }}<br>
                                <strong>Planted Coconut :</strong> {{ $farm->planted_coconut }}<br>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @include('user.others.add_farm') 
    </section>
    @endif

    @if (auth()->user()->isTechnician())
        <section class="section notifications">
            <div class="container">
                @if($notifications->isEmpty())
                    <p class="text-center">No notifications available at the moment.</p>
                @else
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white sticky-top" style="z-index: 1;">
                            <h4>Notifications</h4>
                        </div>
                        <div class="card-body text-truncate p-0" style="max-height: 80vh; overflow-y: auto;">
                            <ul class="list-group">
                                @foreach ($notifications as $notifs)
                                    <li class="list-group-item d-flex flex-column align-items-start shadow-sm p-3">
                                        <div class="mb-2">
                                            <h5 class="mb-1">New Service Request from Farmer</h5>
                                            <p class="mb-1">
                                                <strong>Request ID:</strong> {{ $notifs->request_id }}<br>
                                                <strong>Date/Time:</strong> {{ $notifs->created_at->format('F d, Y | h:i A') }}
                                            </p>
                                        </div>
                                        <div class="mt-3 align-self-end">
                                            <a href="{{ route('user.viewDetails', $notifs->id) }}" class="btn btn-success">
                                                View Details
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>  
                @endif
            </div>
        </section>
    @endif
</main>
@include('user.partials.__footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.classList.remove('show');
                flashMessage.style.animation = 'slideOutToRight 0.5s ease-in-out forwards';  
            }, 3000); 
        }
    });
</script>


<script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoicm9uNTcxNDM1IiwiYSI6ImNtM2g3dmcxbzBjajIycnB5NWtwaG1xazMifQ.mhRMxfZ2WfgoAECkTXAXug';
    let map = null;
    let marker = null;

    async function getAddressFromCoordinates(lng, lat) {
        const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
        try {
            const response = await fetch(url);
            const data = await response.json();
            if (data.features && data.features.length > 0) {
                const address = data.features[0].place_name;
                document.getElementById('address').textContent = `Address: ${address}`;
                document.getElementById('address_input').value = address; 
            } else {
                document.getElementById('address').textContent = 'Address: Not found';
                document.getElementById('address_input').value = '';
            }
        } catch (error) {
            console.error('Error fetching address:', error);
            document.getElementById('address').textContent = 'Address: Unable to retrieve';
            document.getElementById('address_input').value = '';
        }
    }

    function initializeMap(lng, lat) {
        if (!map) {
            map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/satellite-streets-v12',
                center: [lng, lat],
                zoom: 7,
                interactive: true
            });
            map.addControl(new mapboxgl.NavigationControl());
            map.on('click', (e) => {
                const coordinates = e.lngLat;
                const longitude = coordinates.lng.toFixed(6);
                const latitude = coordinates.lat.toFixed(6);
                document.getElementById('location').value = `${latitude}, ${longitude}`;
                if (marker) {
                    marker.setLngLat([longitude, latitude]);
                } else {
                    marker = new mapboxgl.Marker()
                        .setLngLat([longitude, latitude])
                        .addTo(map);
                }
                getAddressFromCoordinates(longitude, latitude);
            });

            setTimeout(() => {
                document.getElementById('map').classList.add('visible');
            }, 100);
        } else {
            map.setCenter([lng, lat]);
        }
    }

    function getUserLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    // document.getElementById('location').value = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                    initializeMap(longitude, latitude);
                    // if (!marker) {
                    //     marker = new mapboxgl.Marker()
                    //         .setLngLat([longitude, latitude])
                    //         .addTo(map);
                    // } else {
                    //     marker.setLngLat([longitude, latitude]);
                    // }
                    // Get initial address
                    // getAddressFromCoordinates(longitude, latitude);
                },
                (error) => {
                    console.error('Error:', error);
                    if (error.code === error.PERMISSION_DENIED) {
                        alert('Please allow location access in your browser or device settings.');
                    } else {
                        alert('Unable to retrieve location.');
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            alert('Geolocation not supported by your browser.');
        }
    }
    
    window.addEventListener('resize', () => {
        if (map) map.resize();
    });
</script>
