
function setSupported() {
    if (typeof mapboxgl !== 'undefined') {
        return true;
    } 
}

$(document).ready(function() {
    if (setSupported()) {
        mapboxgl.accessToken = 'pk.eyJ1Ijoicm9uNTcxNDM1IiwiYSI6ImNtM2g3dmcxbzBjajIycnB5NWtwaG1xazMifQ.mhRMxfZ2WfgoAECkTXAXug';

        $.ajax({
            type: "GET",
            url: "/admin/get_map_locations",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                const locations = res;
                const simulation = res;
                const locationContainer = document.getElementById('map_locations');
                const simulationContainer = document.getElementById('map_simulation');
                if (locationContainer) {
                    // selectLocation();
                    displayMap(locations, mapboxgl);
                } else if (simulationContainer) {
                    mapSimulation(simulation, mapboxgl);
                }
            },
        });
    } 
});


function zoomOut(map) {
    map.flyTo({
        center: [121.423, 14.281],
        zoom: 3,
        speed: 1.2,
        curve: 1,
        easing(t) {
            return t;
        }
    });

} 
async function geoJsonData() {
    try {
        const response = await fetch('/geojson/laguna_map.geojson');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error loading GeoJSON:', error);
        throw error;
    }
}

// Function to generate a random polygon around a center point
function generateRandomPolygon(center, numPoints = 6, radius = 0.01) {
    const points = [];
    for (let i = 0; i < numPoints; i++) {
        const angle = (i / numPoints) * 2 * Math.PI;
        const randomRadius = radius * (0.5 + Math.random() * 0.5);
        const randomAngle = angle + (Math.random() - 0.5) * 0.5;
        const lon = center[0] + randomRadius * Math.cos(randomAngle);
        const lat = center[1] + randomRadius * Math.sin(randomAngle);
        points.push([lon, lat]);
    }
    points.push(points[0]); // Close the polygon
    return {
        type: 'Feature',
        geometry: {
            type: 'Polygon',
            coordinates: [points]
        },
        properties: {}
    };
}

async function displayMap(locate, mapboxgl) {
    const map_locations = new mapboxgl.Map({
        container: 'map_locations',
        style: 'mapbox://styles/mapbox/satellite-streets-v11',
        center: [121.37575102352582, 14.27976852821142], // Sta. Cruz, Laguna
        zoom: 9,
        minZoom: 3,
        maxZoom: 16,
        maxBounds: [
            [121.0, 13.8], // Southwest
            [122.0, 14.8]  // Northeast
        ]
    });

    map_locations.on('load', async () => {
        const data = await geoJsonData();

        // Add municipal GeoJSON source (polygons and points)
        map_locations.addSource('municipal', {
            type: 'geojson',
            data: data
        });

        // Add municipal polygon layer as dashed lines
        map_locations.addLayer({
            id: 'laguna-boundary',
            type: 'line',
            source: 'municipal',
            filter: ['==', '$type', 'Polygon'],
            paint: {
                'line-color': '#FFD700',
                'line-width': 3,
                'line-dasharray': [2, 2]
            }
        });

        // Add municipal points layer
        map_locations.loadImage('/img/municipal_loc.png', (error, image) => {
            if (error) throw error;
            map_locations.addImage('custom-location-icon', image);
            map_locations.addLayer({
                id: 'laguna-points',
                type: 'symbol',
                source: 'municipal',
                filter: ['==', '$type', 'Point'],
                // layout: {
                //     'icon-image': 'custom-location-icon',
                //     'icon-size': 0.1,
                //     'icon-allow-overlap': true
                // }
            });
        });

        // Add farm locations from 'locate' parameter with municipal property
        const farmFeatures = locate.map(farm => ({
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [parseFloat(farm.longitude), parseFloat(farm.latitude)]
            },
            properties: {
                municipal: farm.municipal // Add municipal property for filtering
            }
        }));

        // Generate random polygons around farm points
        const farmPolygons = farmFeatures.map(farm => {
            const center = farm.geometry.coordinates;
            return {
                ...generateRandomPolygon(center),
                properties: {
                    municipal: farm.properties.municipal // Add municipal property to polygons
                }
            };
        });

        // Add farm points source and layer
        map_locations.addSource('farms', {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: farmFeatures
            }
        });
        map_locations.addLayer({
            id: 'farm-points',
            type: 'circle',
            source: 'farms',
            paint: {
                'circle-radius': 5,
                'circle-color': '#FF4500'
            }
        });

        // Add farm polygons source and layer with dashed lines
        map_locations.addSource('farm-polygons', {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: farmPolygons
            }
        });
        map_locations.addLayer({
            id: 'farm-polygons-outline',
            type: 'line',
            source: 'farm-polygons',
            paint: {
                'line-color': '#FF4500',
                'line-width': 2,
                'line-dasharray': [2, 2]
            }
        });

        // Populate dropdown
        const dropdown = document.getElementById('location');
        const names = data.features
            .filter(feature => feature.properties.name)
            .map(feature => feature.properties.name)
            .sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
        names.forEach(name => {
            const option = document.createElement('option');
            option.value = name;
            option.textContent = name;
            dropdown.appendChild(option);
        });

        // Dropdown change event with filtering for farm locations
        document.getElementById('location').addEventListener('change', (event) => {
            const selectedName = event.target.value;
            if (selectedName === 'All Municipal') {
                setTimeout(() => {
                    document.getElementById('details').style.display = 'none';
                    displayMap(locate, mapboxgl); // Reload map to reset filters
                }, 500);
            } else if (selectedName) {
                // Filter municipal points
                map_locations.setFilter('laguna-points', ['==', 'name', selectedName]);

                // Filter farm points based on municipal property
                map_locations.setFilter('farm-points', ['==', ['get', 'municipal'], selectedName]);

                // Filter farm polygons based on municipal property
                map_locations.setFilter('farm-polygons-outline', ['==', ['get', 'municipal'], selectedName]);

                // Update details (if applicable)
                locate.forEach(loc => {
                    if (loc.name === selectedName) { // Assuming 'name' might exist; if not, adjust logic
                        document.getElementById('lot-area').textContent = loc.lot_area || 'N/A';
                        document.getElementById('number-of-trees').textContent = loc.trees || 'N/A';
                        document.getElementById('meters').textContent = loc.meters || 'N/A';
                        document.getElementById('details').style.display = 'block';
                    }
                });
            }
        });

        let popup = null;
        // Tooltips for municipal points
        map_locations.on('mouseenter', 'laguna-points', (e) => {
            const coordinates = e.features[0].geometry.coordinates.slice();
            const properties = e.features[0].properties;
            locate.forEach(loc => {
                if (loc.name === properties.name) {
                    const description = `
                        <div style="background-color: #28a745; padding: 15px; border-radius: 5px; color: white;">
                            <strong>${properties.name}</strong><br>
                            Lot Area: ${loc.lot_area || 'N/A'}<br>
                            Trees: ${loc.trees || 'N/A'}<br>
                            Meters: ${loc.meters || 'N/A'}
                        </div>
                    `;
                    popup = new mapboxgl.Popup({ closeButton: false, closeOnClick: false })
                        .setLngLat(coordinates)
                        .setHTML(description)
                        .addTo(map_locations);
                }
            });
        });
        map_locations.on('mouseleave', 'laguna-points', () => {
            if (popup) popup.remove();
        });
    });
}

async function mapSimulation(simulate, mapboxgl) {
    const map_simulation = new mapboxgl.Map({
        container: 'map_simulation',
        style: 'mapbox://styles/mapbox/satellite-streets-v11',
        center: [121.423, 14.281],
        zoom: 10, // Adjusted for better visibility of Laguna
        minZoom: 3,
        maxZoom: 16,
        maxBounds: [
            [120.99, 13.8],
            [122.10, 14.7]
        ]
    });

    map_simulation.on('load', async function () {
        // Load point data from geoJsonData
        const pointData = await geoJsonData();
        console.log('geoJsonData output:', pointData);

        // Add points as markers
        map_simulation.addSource('towns', {
            type: 'geojson',
            data: pointData
        });
        map_simulation.addLayer({
            id: 'towns-layer',
            type: 'circle',
            source: 'towns',
            paint: {
                'circle-radius': 5,
                'circle-color': '#007cbf'
            }
        });

        // Generate random polygons around each point
        function generateRandomPolygon(center, numPoints = 6, radius = 0.01) {
            const points = [];
            for (let i = 0; i < numPoints; i++) {
                const angle = (i / numPoints) * 2 * Math.PI;
                const randomRadius = radius * (0.5 + Math.random() * 0.5); 
                const randomAngle = angle + (Math.random() - 0.5) * 0.5; 
                const lon = center[0] + randomRadius * Math.cos(randomAngle);
                const lat = center[1] + randomRadius * Math.sin(randomAngle);
                points.push([lon, lat]);
            }
            points.push(points[0]); // Close the polygon
            return {
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [points]
                },
                properties: { center: center }
            };
        }

        // Create initial geofences for each point
        const initialGeofences = pointData.features.map(feature => {
            const center = feature.geometry.coordinates;
            return generateRandomPolygon(center);
        });
        let currentGeofences = initialGeofences.map(g => ({ ...g })); // Deep copy for animation

        // Function to calculate the center of a polygon
        function calculateCenter(polygon) {
            let points = polygon.geometry.coordinates[0];
            let sumLon = 0, sumLat = 0;
            for (let i = 0; i < points.length - 1; i++) {
                sumLon += points[i][0];
                sumLat += points[i][1];
            }
            let numPoints = points.length - 1;
            return [sumLon / numPoints, sumLat / numPoints];
        }

        // Function to scale a polygon by factor k
        function scalePolygon(originalPolygon, k) {
            const center = originalPolygon.properties.center; // Use original point as center
            let points = originalPolygon.geometry.coordinates[0];
            let scaledPoints = points.map(point => {
                let newLon = center[0] + k * (point[0] - center[0]);
                let newLat = center[1] + k * (point[1] - center[1]);
                return [newLon, newLat];
            });
            if (scaledPoints[0][0] !== scaledPoints[scaledPoints.length - 1][0] ||
                scaledPoints[0][1] !== scaledPoints[scaledPoints.length - 1][1]) {
                scaledPoints.push(scaledPoints[0]);
            }
            return {
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [scaledPoints]
                },
                properties: originalPolygon.properties
            };
        }


        let startTime = Date.now();
        let duration = 5000; 
        let max_k = 2;

 
        function animate() {
            let currentTime = Date.now();
            let elapsedTime = currentTime - startTime;
            let k = 1 + (elapsedTime / duration) * (max_k - 1);
            if (k >= max_k) {
                k = max_k;
                return; 
            }
            currentGeofences = initialGeofences.map(g => scalePolygon(g, k));
            map_simulation.getSource('geofences').setData({
                type: 'FeatureCollection',
                features: currentGeofences
            });
            requestAnimationFrame(animate);
        }

        map_simulation.addSource('geofences', {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: initialGeofences
            }
        });
        map_simulation.addLayer({
            id: 'geofence-layer',
            type: 'fill',
            source: 'geofences',
            paint: {
                'fill-color': '#cb5e65',
                'fill-opacity': 0.4
            }
        });
        map_simulation.addLayer({
            id: 'geofence-outline',
            type: 'line',
            source: 'geofences',
            paint: {
                'line-color': '#cb5e65',
                'line-width': 2
            }
        });

        const marker = new mapboxgl.Marker({ draggable: true })
            .setLngLat([121.423, 14.281])
            .addTo(map_simulation);

        marker.on('dragend', () => {
            const lngLat = marker.getLngLat();
            const point = turf.point([lngLat.lng, lngLat.lat]);
            let isInsideAny = false;
            let nearestBoundary = null;
            let minDistance = Infinity;

            currentGeofences.forEach(geofence => {
                if (turf.booleanPointInPolygon(point, geofence)) {
                    isInsideAny = true;
                } else {
                    const geofenceLine = turf.polygonToLine(geofence);
                    const nearest = turf.nearestPointOnLine(geofenceLine, point);
                    const distance = turf.distance(point, nearest);
                    if (distance < minDistance) {
                        minDistance = distance;
                        nearestBoundary = nearest.geometry.coordinates;
                    }
                }
            });

            if (!isInsideAny && nearestBoundary) {
                marker.setLngLat(nearestBoundary);
                console.log('Marker snapped back to nearest geofence boundary');
            } else if (isInsideAny) {
                console.log('Marker is within a geofence');
            }
        });

        // Start the animation
        animate();
    });
}
