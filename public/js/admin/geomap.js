
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
                const locations = res.locations;
                const simulation = res.simulation;
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

async function geoJsonData() {
    try {
        const response = await fetch('/geojson/laguna_map.geojson');
        const data = await response.json();
        return data;
        
    } catch (error) {
        console.error('Error loading or sorting GeoJSON:', error);
        throw error;
    }
}

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

async function displayMap(locate, mapboxgl) { 

    var map_locations = new mapboxgl.Map({
        container: 'map_locations', 
        style: 'mapbox://styles/mapbox/satellite-streets-v11', // Realistic satellite-streets view
        center: [121.37575102352582,
            14.27976852821142], // Coordinates for Sta. Cruz, Laguna
        zoom: 3, // Set zoom level suitable for the area
        minZoom: 3, // Minimum zoom level
        maxZoom: 16, // Maximum zoom level
        maxBounds: [
            [121.0, 13.8],  // Southwest corner (lower latitude for the southern part of Laguna)
            [122.0, 14.8]   // Northeast corner (higher latitude for the northern part of Laguna)
        ]
    });
   
    map_locations.on('load', async function () {
        // Fit the map view to the bounds of Laguna
        const data = await geoJsonData();
        map_locations.addSource('municipal', {
            type: 'geojson',
            data: data
        });

        // Add polygon layer
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

        map_locations.loadImage('/img/municipal_loc.png', function(error, image) {
            if (error) throw error;

            // Add the image to the map
            map_locations.addImage('custom-location-icon', image);

            // Add points layer
            map_locations.addLayer({
                id: 'laguna-points',
                type: 'symbol',
                source: 'municipal',
                filter: ['==', '$type', 'Point'],
                layout: {
                    'icon-image': 'custom-location-icon',  // Use the custom icon
                    'icon-size': 0.1,                      // Adjust size if needed
                    'icon-allow-overlap': true
                }
            });
        });
      
        // Populate the dropdown
        const dropdown = document.getElementById('location');
        const names = data.features
            .map(feature => feature.properties.name) 
            .filter(name => name)  
            .sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));

        names.forEach(name => {
            const option = document.createElement('option');
            option.value = name;
            option.textContent = name;
            dropdown.appendChild(option);
        }); 

        document.getElementById("location").addEventListener("change", (event) => {
            const selectedName = event.target.value; // Get the selected value from the dropdown
            if (map_locations.getLayer('laguna-points')) {
                if (selectedName === "All Municipal") {
                    setTimeout(function() {
                        document.getElementById('details').style.display = 'none';
                        displayMap(locate, mapboxgl); // Recursive call to reload the map
                    }, 500);
               
                } else if (selectedName) {
                    locate.forEach(loc =>{
                        if (loc.name === selectedName) {
                            document.getElementById('lot-area').textContent = loc.lot_area;
                            document.getElementById('number-of-trees').textContent = loc.trees;
                            document.getElementById('meters').textContent = loc.meters;
                            document.getElementById('details').style.display = 'block';
                        }
                    });
                    map_locations.setFilter('laguna-points', ['==', 'name', selectedName]);
                } else {
                    console.error("Invalid selection value.");
                }
            } else {
                console.error("The 'laguna-points' layer is not available.");
            }
        });
        
        let popup = null;
        // Add tooltips for the points
        map_locations.on('mouseenter', 'laguna-points', (e) => {
            const coordinates = e.features[0].geometry.coordinates.slice();
            const properties = e.features[0].properties;

            locate.forEach(loc =>{
                if (loc.name === properties.name) {
               
                    const description = `
                    <div style="background-color: #28a745; padding: 15px; border-radius: 5px; color: white; border: none;">
                        <strong>${properties.name}</strong><br>
                        Lot Area: ${loc.lot_area}<br>
                        Trees: ${loc.trees}<br>
                        Meters: ${loc.meters}
                    </div>
                `;
                popup = new mapboxgl.Popup({ closeButton: false, closeOnClick: false })
                .setLngLat(coordinates)
                .setHTML(description)
                .addTo(map_locations);
                }
            })
        
        });

        map_locations.on('mouseleave', 'laguna-points', () => {
            if (popup) {
                popup.remove(); 
            }
            map_locations.getCanvas().style.cursor = '';
        });
    });
}

// async function mapSimulation(simulate, mapboxgl) {
//     const map_simulation = new mapboxgl.Map({
//         container: 'map_simulation', 
//         style: 'mapbox://styles/mapbox/satellite-streets-v11', 
//         center: [121.423, 14.281], 
//         zoom: 3, 
//         minZoom: 3, 
//         maxZoom: 16, 
//         maxBounds: [ 
//             [120.99, 13.8],  // Southwest corner (lower latitude for Laguna)
//             [122.10, 14.7]   // Northeast corner (higher latitude for Laguna)
//         ]
//     });

   
//     map_simulation.on('load', async function () {
//         // Load geoJSON data
//         const data = await geoJsonData();
//         // Define a geofence (example polygon)
//         const geofence = {
//             type: 'Feature',
//             geometry: {
//                 type: 'Polygon',
//                 coordinates: [[
//                     [-123.145, 49.300], // Northwest
//                     [-123.100, 49.300], // Northeast
//                     [-123.100, 49.275], // Southeast
//                     [-123.145, 49.275], // Southwest
//                     [-123.145, 49.300]  // Close the loop
//                 ]]
//             }
//         };

//         // Add geofence to the map
//         map_simulation.addSource('geofence', {
//             type: 'geojson',
//             data: data
//         });
//         map_simulation.addLayer({
//             id: 'geofence-layer',
//             type: 'fill',
//             source: 'geofence',
//             paint: {
//                 'fill-color': '#cb5e65',
//                 'fill-opacity': 0.4
//             }
//         });
//         map_simulation.addLayer({
//             id: 'geofence-outline',
//             type: 'line',
//             source: 'geofence',
//             paint: {
//                 'line-color': '#cb5e65',
//                 'line-width': 2
//             }
//         });

//         // Add a draggable marker
//         const marker = new mapboxgl.Marker({ draggable: true })
//             .setLngLat([-123.1207, 49.2827]) // Inside geofence
//             .addTo(map_simulation);

//         // Convert geofence to line for snapping
//         const geofenceLine = turf.polygonToLine(geofence);

//         // Geofencing logic on drag end
//         marker.on('dragend', () => {
//             const lngLat = marker.getLngLat();
//             const point = turf.point([lngLat.lng, lngLat.lat]);
//             const isInside = turf.booleanPointInPolygon(point, geofence);

//             if (!isInside) {
//                 const nearest = turf.nearestPointOnLine(geofenceLine, point);
//                 marker.setLngLat(nearest.geometry.coordinates);
//                 console.log('Marker snapped back to boundary');
//             } else {
//                 console.log('Marker is within geofence');
//             }
//         });
//     });
// }

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
