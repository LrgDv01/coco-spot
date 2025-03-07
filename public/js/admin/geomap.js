
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
                const farm_loc = res.farm_loc;
                const locations = res.locations;
                const simulation = res.farm_loc;
                const locationContainer = document.getElementById('map_locations');
                const simulationContainer = document.getElementById('map_simulation');
                if (locationContainer) {
                    // selectLocation();
                    displayMap(farm_loc, locations, mapboxgl);
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

async function displayMap(farm_loc, locations, mapboxgl) {

    const map_locations = new mapboxgl.Map({
        container: 'map_locations',
        style: 'mapbox://styles/mapbox/satellite-streets-v11',
        center: [121.37575102352582, 14.27976852821142], 
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

        map_locations.addSource('municipal', {
            type: 'geojson',
            data: data
        });

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
        const farmFeatures = farm_loc.map(farm => ({
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
                    displayMap(farm_loc, locations, mapboxgl); // Reload map to reset filters 
                }, 500);
            } else if (selectedName) {
                // Filter municipal points
                map_locations.setFilter('laguna-points', ['==', 'name', selectedName]);

                // Filter farm points based on municipal property
                map_locations.setFilter('farm-points', ['==', ['get', 'municipal'], selectedName]);

                // Filter farm polygons based on municipal property
                map_locations.setFilter('farm-polygons-outline', ['==', ['get', 'municipal'], selectedName]);

                // Update details (if applicable)
                locations.forEach(loc => {
                    if (loc.name === selectedName) { // Assuming 'name' might exist; if not, adjust logic
                        document.getElementById('name').textContent = loc.name || 'N/A';
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
            console.log('farm_loc', locations);
            locations.forEach(loc => {
                
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


// Function to generate a random polygon around a center point

function simulateGenerateRandomPolygon(center, numPoints = 6, radius = 0.01) {
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
        properties: { center } // Store center for scaling
    };
}

// Function to scale a polygon by factor k
function scalePolygon(originalPolygon, k) {
    const center = originalPolygon.properties.center;
    const points = originalPolygon.geometry.coordinates[0];
    const scaledPoints = points.map(point => {
        const newLon = center[0] + k * (point[0] - center[0]);
        const newLat = center[1] + k * (point[1] - center[1]);
        return [newLon, newLat];
    });
    return {
        type: 'Feature',
        geometry: {
            type: 'Polygon',
            coordinates: [scaledPoints]
        },
        properties: originalPolygon.properties
    };
}
async function mapSimulation(simulate, mapboxgl) {
    const map_simulation = new mapboxgl.Map({
        container: 'map_simulation',
        style: 'mapbox://styles/mapbox/satellite-streets-v11',
        center: [121.37575102352582, 14.27976852821142],
        zoom: 9,
        minZoom: 3,
        maxZoom: 16,
        maxBounds: [
            [121.0, 13.8], // Southwest
            [122.0, 14.8]  // Northeast
        ]
    });

    map_simulation.on('load', async () => {
        const data = await geoJsonData();

        map_simulation.addSource('municipal', {
            type: 'geojson',
            data: data
        });

        map_simulation.addLayer({
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
        map_simulation.loadImage('/img/municipal_loc.png', (error, image) => {
            if (error) throw error;
            map_simulation.addImage('custom-location-icon', image);
            map_simulation.addLayer({
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

        // Add farm locations from 'simulate' parameter with municipal property
        const farmFeatures = simulate.map(farm => ({
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [parseFloat(farm.longitude), parseFloat(farm.latitude)]
            },
            properties: {
                municipal: farm.municipal
            }
        }));

        // Generate initial farm polygons
        const initialFarmPolygons = farmFeatures.map(farm => {
            const center = farm.geometry.coordinates;
            return {
                ...simulateGenerateRandomPolygon(center),
                properties: {
                    municipal: farm.properties.municipal,
                    center // Store center in properties for animation
                }
            };
        });

        // Add farm points source and layer
        map_simulation.addSource('farms', {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: farmFeatures
            }
        });
        map_simulation.addLayer({
            id: 'farm-points',
            type: 'circle',
            source: 'farms',
            paint: {
                'circle-radius': 5,
                'circle-color': '#FF4500'
            }
        });

        // Add farm polygons source and layer with dashed lines
        map_simulation.addSource('farm-polygons', {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: initialFarmPolygons
            }
        });
        // map_simulation.addLayer({
        //     id: 'farm-polygons-outline',
        //     type: 'line',
        //     source: 'farm-polygons',
        //     paint: {
        //         'line-color': '#FF4500',
        //         'line-width': 2,
        //         'line-dasharray': [2, 2]
        //     }
        // });

        map_simulation.addLayer({
            id: 'farm-polygons-fill',
            type: 'fill',
            source: 'farm-polygons',
            paint: {
                'fill-color': '#FF4500', // Orange fill
                'fill-opacity': 0.4      // Semi-transparent
            }
        });

        // Animation setup
        let isAnimating = false;
        const duration = 5000; // 5 seconds
        const maxScale = 2; // Max scale factor

        function animatePolygons() {
            if (isAnimating) return; // Prevent multiple animations
            isAnimating = true;

            const startTime = Date.now();
            let currentPolygons = initialFarmPolygons.map(p => ({ ...p })); // Reset to initial state

            function step() {
                const currentTime = Date.now();
                const elapsedTime = currentTime - startTime;
                let k = 1 + (elapsedTime / duration) * (maxScale - 1); // Scale from 1 to maxScale

                if (k >= maxScale) {
                    k = maxScale; // Cap at max scale
                    isAnimating = false;
                } else {
                    requestAnimationFrame(step); // Continue animation
                }

                currentPolygons = initialFarmPolygons.map(polygon => scalePolygon(polygon, k));
                map_simulation.getSource('farm-polygons').setData({
                    type: 'FeatureCollection',
                    features: currentPolygons
                });
            }

            requestAnimationFrame(step);
        }

        // Add button event listener (assuming button exists in HTML)
        document.getElementById('silumate-button').addEventListener('click', () => {
            animatePolygons();
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
                    mapSimulation(simulate, mapboxgl); // Reload map to reset filters
                }, 500);
            } else if (selectedName) {
                map_simulation.setFilter('laguna-points', ['==', 'name', selectedName]);
                map_simulation.setFilter('farm-points', ['==', ['get', 'municipal'], selectedName]);
                map_simulation.setFilter('farm-polygons-outline', ['==', ['get', 'municipal'], selectedName]);
            }
        });

        let popup = null;
        map_simulation.on('mouseenter', 'laguna-points', (e) => {
            const coordinates = e.features[0].geometry.coordinates.slice();
            const properties = e.features[0].properties;
            simulate.forEach(loc => {
                if (loc.municipal === properties.name) { // Adjusted to use municipal if applicable
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
                        .addTo(map_simulation);
                }
            });
        });
        map_simulation.on('mouseleave', 'laguna-points', () => {
            if (popup) popup.remove();
        });
    });
}