function setSupported() {
    if (typeof mapboxgl !== 'undefined') {
        return true;
    } 
}
const locationContainer = document.getElementById('map_locations');
const simulationContainer = document.getElementById('map_simulation');
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
                const simulation = res.diseases;
                if (locationContainer) {
                    locationContainer.innerHTML = "";
                    // selectLocation();
                    displayMap(farm_loc, locations, mapboxgl);
                } else if (simulationContainer) {
                    mapSimulation(farm_loc, simulation, mapboxgl);
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
            [121.0, 13.8],
            [122.0, 14.8]
        ]
    });
    let originalFarmPolygonsData = null;
    // Fixed interpolation function
    const interpolateZoom = (currentZoom, minZoom, maxZoom, minScale, maxScale) => {
        const progress = Math.min(1, Math.max(0, 
            (currentZoom - minZoom) / (maxZoom - minZoom)
        ));
        return maxScale + (minScale - maxScale) * progress;
    };
    const scalePolygon = (feature, center, scaleFactor) =>
        turf.transformScale(feature, scaleFactor, { origin: center });
    map_locations.on('load', async () => {
        // Load geojson data
        const data = await geoJsonData();
        // Add municipal boundaries
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

        // const farmFeatures = farm_loc.map(farm => ({
        //     type: 'Feature',
        //     geometry: {
        //         type: 'Point',
        //         coordinates: [parseFloat(farm.longitude), parseFloat(farm.latitude)]
        //     },
        //     properties: { 
        //         municipal: farm.municipal,
        //         id: farm.id // Add farm ID to properties
        //      }
        // }));

        // 1. Add feature IDs to farm points (crucial for targeting)
        const farmFeatures = farm_loc.map(farm => ({
            id: farm.id, // ◀⚡ Add feature-level ID
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [parseFloat(farm.longitude), parseFloat(farm.latitude)]
            },
            properties: {
                municipal: farm.municipal,
                id: farm.id // Add farm ID to properties
            }
        }));

        const farmPolygons = farmFeatures.map(farm => {
            // const originalFarm = simulate.find(f => f.id === farm.properties.farmId);
            // const { isVerified, progress } = getFarmState(originalFarm); // Pass duration

            const center = farm.geometry.coordinates;
            const polygon = generateRandomPolygon(center);
            return {
                ...polygon,
                properties: {
                    municipal: farm.properties.municipal,
                    originalCenter: center,
                    originalCoordinates: polygon.geometry.coordinates
                }
            };
        });
        originalFarmPolygonsData = {
            type: 'FeatureCollection',
            features: JSON.parse(JSON.stringify(farmPolygons))
        };
        // Add farm data sources
        map_locations.addSource('farms', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features: farmFeatures }
        });
        map_locations.addSource('farm-polygons', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features: farmPolygons }
        });
        // Add farm points layer
        map_locations.addLayer({
            id: 'farm-points',
            type: 'circle',
            source: 'farms',
            paint: {
                'circle-radius': 5,
                'circle-color': '#FFD700'
            }
        });
        // Add polygon visualization layers
        map_locations.addLayer({
            id: 'farm-polygons-fill',
            type: 'fill',
            source: 'farm-polygons',
            paint: {
                'fill-color': '#FFD700',
                'fill-opacity': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    3, 0.3,  // More visible when zoomed out
                    14, 0    // Fully hidden at zoom 14
                ]
            }
        });
        map_locations.addLayer({
            id: 'farm-polygons-outline',
            type: 'line',
            source: 'farm-polygons',
            paint: {
                'line-color': '#FFD700',
                'line-width': 2,
                'line-dasharray': [2, 2],
                'line-opacity': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    12, 1,  // Fully visible until zoom 12
                    16, 1    // Fade out at high zoom
                ]
            }
        });

        // 2. Update hover handlers with precise targeting
        let hoveredFarmId = null;

        map_locations.on('mousemove', 'farm-points', (e) => {
            // Increase detection radius to 20px around cursor
            const features = map_locations.queryRenderedFeatures(e.point, { 
                layers: ['farm-points'],
                radius: 20 // ◀⚡ Wider hover detection area
            });
            
            if (features.length > 0) {
                // Only update if new farm is hovered
                if (hoveredFarmId !== features[0].id) {
                    // Reset previous hover
                    if (hoveredFarmId) {
                        map_locations.setFeatureState(
                            { source: 'farms', id: hoveredFarmId },
                            { hover: false }
                        );
                    }
                    // Set new hover
                    hoveredFarmId = features[0].id;
                    map_locations.setFeatureState(
                        { source: 'farms', id: hoveredFarmId },
                        { hover: true }
                    );
                }
            }
        });

        map_locations.on('mouseleave', 'farm-points', () => {
            if (hoveredFarmId) {
                map_locations.setFeatureState(
                    { source: 'farms', id: hoveredFarmId },
                    { hover: false }
                );
                hoveredFarmId = null;
            }
        });

        // 3. Update layer paint to use feature state
        map_locations.setPaintProperty('farm-points', 'circle-color', [
            'case',
            ['boolean', ['feature-state', 'hover'], false],
            '#FFA500', // Hovered color
            '#FFD700'  // Default color
        ]);

        map_locations.setPaintProperty('farm-points', 'circle-radius', [
            'case',
            ['boolean', ['feature-state', 'hover'], false],
            8,  // Hovered size
            5   // Default size
        ]);

        // Add cursor pointer and hover effects to farm points
        // map_locations.on('mouseenter', 'farm-points', () => {
        //     map_locations.getCanvas().style.cursor = 'pointer'; // Pointer cursor on hover
        //     map_locations.setPaintProperty('farm-points', 'circle-color', '#FFA500'); // Orange color
        //     map_locations.setPaintProperty('farm-points', 'circle-radius', 7); // Slightly larger
        // });

        // map_locations.on('mouseleave', 'farm-points', () => {
        //     map_locations.getCanvas().style.cursor = ''; // Default cursor
        //     map_locations.setPaintProperty('farm-points', 'circle-color', '#FFD700'); // Gold color
        //     map_locations.setPaintProperty('farm-points', 'circle-radius', 5); // Original size
        // });

        let zoomAnimationFrame = null;
        map_locations.on('zoom', () => {
            if (zoomAnimationFrame) cancelAnimationFrame(zoomAnimationFrame);
            zoomAnimationFrame = requestAnimationFrame(() => {
                const zoom = map_locations.getZoom();
                const scaleFactor = interpolateZoom(zoom, 2, 15, 0.3, 1.7);
                const scaledFeatures = originalFarmPolygonsData.features.map(feature => 
                    scalePolygon(
                        JSON.parse(JSON.stringify(feature)),
                        feature.properties.originalCenter,
                        scaleFactor
                    )
                );
                map_locations.getSource('farm-polygons').setData({
                    type: 'FeatureCollection',
                    features: scaledFeatures
                });
            });
        });
        // Municipal selection dropdown
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
        // Location selection handler
        document.getElementById('location').addEventListener('change', (event) => {
            const selectedName = event.target.value;
            if (selectedName === 'All Municipal') {
                setTimeout(() => {
                    document.getElementById('details').style.display = 'none';
                    displayMap(farm_loc, locations, mapboxgl); 
                }, 500);
            }
            const filter = selectedName === 'All Municipal' 
                ? ['==', ['get', 'municipal'], '']
                : ['==', ['get', 'municipal'], selectedName];

            ['laguna-points', 'farm-points', 'farm-polygons-outline'].forEach(layer => {
                map_locations.setFilter(layer, filter);
            });
            const loc = locations.find(l => l.name === selectedName);
            if (loc) {
                document.getElementById('name').textContent = loc.name || 'N/A';
                document.getElementById('lot-area').textContent = loc.lot_area || 'N/A';
                document.getElementById('number-of-trees').textContent = loc.trees || 'N/A';
                document.getElementById('meters').textContent = loc.meters || 'N/A';
                document.getElementById('details').style.display = 'block';
            } else {
                document.getElementById('details').style.display = 'none';
            }
        });

        // Add this after creating the 'farm-points' layer in displayMap():
        map_locations.on('click', 'farm-points', (e) => {
            if (e.features.length > 0) {
                const farmId = e.features[0].properties.id;
                const farm = farm_loc.find(f => f.id === farmId);
                
                if (farm) {
                    document.getElementById('farm_name').textContent = farm.name || 'N/A';
                    document.getElementById('address').textContent = farm.address || 'N/A';
                    document.getElementById('district').textContent = farm.district || 'N/A';
                    document.getElementById('hectares').textContent = farm.hectares || 'N/A';
                    document.getElementById('tree_age').textContent = farm.tree_age || 'N/A';
                    document.getElementById('planted_coconuts').textContent = farm.planted_coconut || 'N/A';
                    document.getElementById('variety').textContent = farm.variety || 'N/A';
                }
            }
        });


        // farm_loc.forEach(element => {
        //     if (element.id === farm-points ) {
        //         document.getElementById('farm_name').textContent = element.name || 'N/A';
        //         document.getElementById('location').textContent = element.address || 'N/A';
        //         document.getElementById('district').textContent = element.district || 'N/A';
        //         document.getElementById('hectares').textContent = element.hectares || 'N/A';
        //         document.getElementById('tree_age').textContent = element.tree_age || 'N/A';
        //         document.getElementById('planted_coconuts').textContent = element.planted_coconut || 'N/A';
        //         document.getElementById('variety').textContent = element.variety || 'N/A';
        //         // document.getElementById('soil_type').style.display = 'block';
        //     }
          
        // });
        
        // Popup handling
        let popup = null;
        // Optional: Debounce for better UX (uncomment if desired)
        // let popupTimer = null;
        map_simulation.on('mouseenter', 'farm-points', (e) => {
            // if (popupTimer) { clearTimeout(popupTimer); popupTimer = null; }
            if (popup) { popup.remove(); popup = null; }
            if (e.features.length > 0) {
                const feature = e.features[0];
                const coordinates = feature.geometry.coordinates.slice();
                const farmId = feature.properties.farmId;
                const farm = farm_loc.find(f => String(f.id) === String(farmId));
                if (farm) {
                    // Build popup HTML with conditional fields
                    let detailsHTML = '';
                    if (farm.address) detailsHTML += `<p><strong>Address:</strong> ${farm.address}</p>`;
                    if (farm.district) detailsHTML += `<p><strong>District:</strong> ${farm.district}</p>`;
                    if (farm.municipal) detailsHTML += `<p><strong>Municipal:</strong> ${farm.municipal}</p>`;
                    if (farm.hectares) detailsHTML += `<p><strong>Hectares:</strong> ${farm.hectares}</p>`;
                    if (farm.tree_age) detailsHTML += `<p><strong>Tree Age:</strong> ${farm.tree_age}</p>`;
                    if (farm.planted_coconut) detailsHTML += `<p><strong>Planted Coconut:</strong> ${farm.planted_coconut}</p>`;
                    if (farm.variety) detailsHTML += `<p><strong>Variety:</strong> ${farm.variety}</p>`;
                    if (!detailsHTML) detailsHTML = '<p>No details available.</p>';
                    const htmlContent = `
                        <div class="custom-map-popup">
                            <h3>${farm.name || 'Farm Details'}</h3>
                            <div class="popup-details">
                                ${detailsHTML}
                            </div>
                        </div>
                    `;
                    popup = new mapboxgl.Popup({
                        closeButton: false,
                        closeOnClick: false,
                        offset: 15,
                        className: 'my-custom-mapbox-popup'
                    })
                    .setLngLat(coordinates)
                    .setHTML(htmlContent)
                    .addTo(map_simulation);
                }
            }
        });
        map_simulation.on('mouseleave', 'farm-points', () => {
            // if (popupTimer) { clearTimeout(popupTimer); popupTimer = null; }
            if (popup) { popup.remove(); popup = null; }
        });



    });
}

function simulateGenerateRandomPolygon(center, numPoints = 9, radius = 0.01) {
    const points = [];
    for (let i = 0; i < numPoints; i++) {
        const angle = (i / numPoints) * 2 * Math.PI;
        const randomRadius = radius * (0.5 + Math.random() * 0.5);
        const randomAngle = angle + (Math.random() - 0.5) * 0.5;
        const lon = center[0] + randomRadius * Math.cos(randomAngle);
        const lat = center[1] + randomRadius * Math.sin(randomAngle);
        points.push([lon, lat]);
    }
    points.push(points[0]);
    return {
        type: 'Feature',
        geometry: {
            type: 'Polygon',
            coordinates: [points]
        },
        properties: { center }
    };
}

function scalePolygon(polygon, k, zoomFactor = 1) {
    const center = polygon.properties.center;
    const points = polygon.geometry.coordinates[0];
    const scaledPoints = points.map(point => {
        const newLon = center[0] + k * zoomFactor * (point[0] - center[0]);
        const newLat = center[1] + k * zoomFactor * (point[1] - center[1]);
        return [newLon, newLat];
    });
    return {
        type: 'Feature',
        geometry: {
            type: 'Polygon',
            coordinates: [scaledPoints]
        },
        properties: polygon.properties
    };
}

async function mapSimulation(farm_loc, simulate, mapboxgl) {
    const simulationContainer = document.getElementById('simulationContainer');
    if (simulationContainer) simulationContainer.innerHTML = "";
    const map_simulation = new mapboxgl.Map({
        container: 'map_simulation',
        style: 'mapbox://styles/mapbox/satellite-streets-v11',
        center: [121.37575102352582, 14.27976852821142],
        zoom: 0,
        minZoom: 0,
        maxZoom: 15,
        maxBounds: [
            [120.70, 13.92],
            [121.95, 14.65]
        ]
    });
    map_simulation.on('load', async () => {
        const data = await geoJsonData();
        map_simulation.addSource('municipal', { type: 'geojson', data });
        map_simulation.addLayer({
            id: 'laguna-boundary',
            type: 'line',
            source: 'municipal',
            filter: ['==', ['geometry-type'], 'Polygon'],
            paint: {
                'line-color': '#FFD700',
                'line-width': 3,
                'line-dasharray': [2, 2],
            }
        });

        map_simulation.addSource('farms', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features: [] }
        });

        map_simulation.addLayer({
            id: 'farm-points',
            type: 'circle',
            source: 'farms',
            paint: {
                'circle-radius': ['match', ['get', 'pointType'], 'technician', 3, 5],
                'circle-color': ['get', 'pointColor']
            }
        });

        map_simulation.addSource('farm-polygons', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features: [] }
        });

        map_simulation.addLayer({
            id: 'farm-polygons-fill',
            type: 'fill',
            source: 'farm-polygons',
            paint: {
                'fill-color': ['get', 'color'],
                'fill-opacity': 0.4
            }
        });

        map_simulation.addSource('technician-geofences', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features: [] }
        });

        map_simulation.addLayer({
            id: 'technician-geofences-fill',
            type: 'fill',
            source: 'technician-geofences',
            paint: {
            'fill-color': '#ff6666', // Slightly red
            'fill-opacity': 0.3 // Initial opacity
            }
        });
     
        map_simulation.on('zoom', () => {
            const zoom = map_simulation.getZoom();
            const zoomFactor = Math.pow(2, (9 - zoom)) * 0.5;
            const currentFarmData = map_simulation.getSource('farm-polygons')._data;
            const currentGeofenceData = map_simulation.getSource('technician-geofences')._data;

            const scaledFarmFeatures = currentFarmData.features.map(feature => {
                return scalePolygon(feature, 1, zoomFactor);
            });

            const scaledGeofenceFeatures = currentGeofenceData.features.map(feature => {
                return scalePolygon(feature, 1, zoomFactor);
            });

            map_simulation.getSource('farm-polygons').setData({
                type: 'FeatureCollection',
                features: scaledFarmFeatures
            });

            map_simulation.getSource('technician-geofences').setData({
                type: 'FeatureCollection',
                features: scaledGeofenceFeatures
            });
        });

        // Convert RGB to HSL
        function rgbToHsl(r, g, b) {
            r /= 255; g /= 255; b /= 255;
            const max = Math.max(r, g, b), min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;
            if (max === min) {
                h = s = 0; // Achromatic
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            return [h, s, l];
        }

        // Convert HSL back to RGB
        function hslToRgb(h, s, l) {
            let r, g, b;
            if (s === 0) {
                r = g = b = l; // Achromatic
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1 / 6) return p + (q - p) * 6 * t;
                    if (t < 1 / 2) return q;
                    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                    return p;
                };
                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;
                r = hue2rgb(p, q, h + 1 / 3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1 / 3);
            }
            return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
        }

        // Interpolate between two colors using HSL
        function treatedInterpolateColor(progress, colors) {
            const [start, end] = colors.map(c => rgbToHsl(...c));
            const h = start[0] + progress * (start[0] - end[0] );
            const s = start[1] + progress * (start[1] - end[1] );
            const l = start[2] + progress * (start[2] - end[2] );
            return hslToRgb(h, s, l);
        }

        function unTreatedInterpolateColor(progress, colors) {
            const [start, end] = colors.map(c => rgbToHsl(...c));
            const h = start[0] + progress * (end[0] - start[0] );
            const s = start[1] + progress * (end[1] - start[1] );
            const l = start[2] + progress * (end[2] - start[2] );
            return hslToRgb(h, s, l);
        }
        // Helper function to interpolate size (base radius for polygons)
        function interpolateSize(progress, minSize, maxSize) {
            return minSize + (maxSize - minSize) * progress;
        }

        function getFarmState(selectedDisease, farm) {
 
            const now = new Date();
            const sevenDays = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds
            const threeMonths = 3 * 30 * 24 * 60 * 60 * 1000; // 3 months in milliseconds (approx. 30 days per month)
            const tenDays = 10 * 24 * 60 * 60 * 1000; // 10 days in milliseconds
            const threeDays = 3 * 24 * 60 * 60 * 1000; // 3 days in milliseconds
            const latestFarmerReport = farm.farmer_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
            const latestTechnicianReport = farm.technician_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
            const condition = farm.technician_reports.some(techReport => 
                                farm.farmer_services.some(farmerService => 
                                    techReport.farm_id === farmerService.farm_id && 
                                    (techReport.diagnosis_disease === selectedDisease || 'All_Diseases' === selectedDisease) && 
                                    farmerService.status === 'Treated'
                                )
                            );

            // filteredFarms = simulate.filter(farm => {
            //     return farm.technician_reports.some(techReport => 
            //         farm.farmer_services.some(farmerService => 
            //             techReport.farm_id === farmerService.farm_id &&
            //             !!(techReport.diagnosis_disease === selectedDisease && farmerService.status === 'Treated')
            //         )
            //     );
            // });

            if (selectedDisease === 'All_Diseases') {
                // farm.farmer_reports.forEach(report => { 
                // });
                if (!condition) {
                    if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
                            const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                            // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                            if (latestTechnicianReport.diagnosis_disease === 'Stem Bleeding') {
                                if (timeSinceTreatment > threeDays) {
                                    return { isVerified: true, isTreated: false, progress: 1 };
                                } else if (timeSinceTreatment) {
                                    const progress = Math.min(timeSinceTreatment / threeDays, 1);
                                    return { isVerified: true, isTreated: false, progress };
                                }
                            } 
                            if (latestTechnicianReport.diagnosis_disease === 'Lethal Yellowing') {
                                if (timeSinceTreatment > threeMonths) {
                                    return { isVerified: true, isTreated: false, progress: 1 };
                                } else if (timeSinceTreatment) {
                                    const progress = Math.min(timeSinceTreatment / threeMonths, 1);
                                    return { isVerified: true, isTreated: false, progress };
                                }
                            }
                            if (latestTechnicianReport.diagnosis_disease === 'Leaf Spot') {
                                if (timeSinceTreatment > tenDays) {
                                    return { isVerified: true, isTreated: false, progress: 1 };
                                } else if (timeSinceTreatment) {
                                    const progress = Math.min(timeSinceTreatment / tenDays, 1);
                                    return { isVerified: true, isTreated: false, progress };
                                }
                            }
                        } 
                        // else if (latestFarmerReport) {
                        //     // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                        //     // const progress = Math.min(timeSinceReport / sevenDays, 1);
                        //     // return { isVerified: false, progress };
                        //     // return { isVerified: false, progress: 0 };
                        //     // return { isVerified: false, progress : 1  };
                        //       return { isVerified: false, isTreated: true, progress: 1 };
                        // } 
                } else {

                    // if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
                            const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                            // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                            if (latestTechnicianReport.diagnosis_disease === 'Stem Bleeding') {
                                if (timeSinceTreatment > threeDays) {
                                    return { isVerified: false, isTreated: true, progress: 1};
                                } else if (timeSinceTreatment) {
                                    const progress = Math.min(timeSinceTreatment / threeDays, 1);
                                    return { isVerified: false, isTreated: true, progress };
                                }
                            } 
                            if (latestTechnicianReport.diagnosis_disease === 'Lethal Yellowing') {
                                if (timeSinceTreatment > threeMonths) {
                                    return { isVerified: false, isTreated: true, progress: 1 };
                                } else if (timeSinceTreatment) {
                                    const progress = Math.min(timeSinceTreatment / threeMonths, 1);
                                    return { isVerified: false, isTreated: true, progress };
                                }
                            }
                            if (latestTechnicianReport.diagnosis_disease === 'Leaf Spot') {
                                if (timeSinceTreatment > tenDays) {
                                    return { isVerified: false, isTreated: true, progress: 1};
                                } else if (timeSinceTreatment) {
                                    const progress = Math.min(timeSinceTreatment / tenDays, 1);
                                    return { isVerified: false, isTreated: true, progress };
                                }
                            }
                        // } 
                        // else if (latestFarmerReport) {
                        //     // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                        //     // const progress = Math.min(timeSinceReport / sevenDays, 1);
                        //     // return { isVerified: false, progress };
                        //     // return { isVerified: false, progress: 0 };
                        //     // return { isVerified: false, progress : 1  };
                        //       return { isVerified: false, isTreated: true, progress: 1 };
                        // } 
                    // return { isVerified: false, isTreated: true, progress: 1 };
                    // return { isVerified: false, progress: 0 };
                }
            }
       
            else if (selectedDisease === 'Stem Bleeding') {
                if (!condition) {
                    if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
                        const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                        if (timeSinceTreatment > threeDays) {
                             return { isVerified: true, isTreated: false, progress: 1 };
                        } else if (timeSinceTreatment) {
                            const progress = Math.min(timeSinceTreatment / threeDays, 1);
                             return { isVerified: true, isTreated: false, progress };
                        }
                    } 
                    // else if (latestFarmerReport) {
                    //     // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                    //     // const progress = Math.min(timeSinceReport / threeDays, 1);
                    //     // return { isVerified: false, progress };
                    //     // return { isVerified: false, progress : 1  };
                    //     return { isVerified: false, isTreated: true, progress: 1 };
                    // } 
                }else {
                    // return { isTreated: false, progress: 1 };
                    // return { isVerified: false, isTreated: true, progress: 1 };
                    const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                    // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                    if (latestTechnicianReport.diagnosis_disease === selectedDisease) {
                        if (timeSinceTreatment > threeDays) {
                            return { isVerified: false, isTreated: true, progress: 1};
                        } else if (timeSinceTreatment) {
                            const progress = Math.min(timeSinceTreatment / threeDays, 1);
                            return { isVerified: false, isTreated: true, progress };
                        }
                    } 
             
                }

            } else if (selectedDisease === 'Lethal Yellowing') {
                if (!condition) {
                    if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
                        const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                        if (timeSinceTreatment > threeMonths) {
                            return { isVerified: true, progress: 1 };
                        } else if (timeSinceTreatment) {
                            const progress = Math.min(timeSinceTreatment / threeMonths, 1);
                            return { isVerified: true, progress };
                        }
                    } else if (latestFarmerReport) {
                        // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                        // const progress = Math.min(timeSinceReport / threeMonths, 1);
                        // return { isVerified: false, progress };
                        return { isVerified: false, progress : 1  };
                    } 
                } else {
                    // return { isTreated: false, progress: 1 };
                    // return { isVerified: false, progress: 0 };
                    const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                    if (latestTechnicianReport.diagnosis_disease === selectedDisease) {
                        if (timeSinceTreatment > threeMonths) {
                            return { isVerified: false, isTreated: true, progress: 1 };
                        } else if (timeSinceTreatment) {
                            const progress = Math.min(timeSinceTreatment / threeMonths, 1);
                            return { isVerified: false, isTreated: true, progress };
                        }
                    }
                         
                }

            } else if (selectedDisease === 'Leaf Spot') {
                if (!condition) {
                    if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
                        const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                        if (timeSinceTreatment > tenDays) {
                            // return { isVerified: true, progress: 1 };
                              return { isVerified: true, isTreated: false, progress: 1 };
                        } else if (timeSinceTreatment) {
                            const progress = Math.min(timeSinceTreatment / tenDays, 1);
                            // return { isVerified: true, progress };
                              return { isVerified: true, isTreated: false, progress };
                        }
                    } 
                    // else if (latestFarmerReport) {
                    //     // const timeSinceReport = now - new Date(latestFarmerReport.created_at);
                    //     // const progress = Math.min(timeSinceReport / tenDays, 1);
                    //     // return { isVerified: false, progress };
                    //     //    return { isVerified: false, progress : 1  };
                    //     return { isVerified: false, isTreated: true, progress: 1 };
                    // } 
                } else {
                    // return { isTreated: true, progress: 1 };
                    // return { isVerified: false, isTreated: true, progress: 1 };
                    const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
                    if (latestTechnicianReport.diagnosis_disease === selectedDisease) {
                        if (timeSinceTreatment > tenDays) {
                            return { isVerified: false, isTreated: true, progress: 1};
                        } else if (timeSinceTreatment) {
                            const progress = Math.min(timeSinceTreatment / tenDays, 1);
                            return { isVerified: false, isTreated: true, progress };
                        }
                    }
                }

            } 
        }

        function updateFarmData(selected, condition) {
            const currentDate = new Date();
            const dayIndex = currentDate.getDay();
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];
            let filteredFarms;
            if (selected === 'All_Diseases') {
                 if (condition === 'not_treated') {
                    filteredFarms = simulate.filter(farm => {
                        return farm.technician_reports.some(techReport => 
                            farm.farmer_services.some(farmerService => 
                                techReport.farm_id === farmerService.farm_id && 
                                (farmerService.status === 'Verified' || farmerService.status === 'False Detection')
                            ) 
                        );
                    });
                } else if (condition === 'treated') {
                    filteredFarms = simulate.filter(farm => {
                        return farm.technician_reports.some(techReport => 
                            farm.farmer_services.some(farmerService => 
                                techReport.farm_id === farmerService.farm_id && farmerService.status === 'Treated'
                            )
                        );
                    });
                } 
                else {
                   filteredFarms = simulate; 
                }
            }
            else {
                if (condition === 'not_treated') {
                    filteredFarms = simulate.filter(farm => {
                        return farm.technician_reports.some(techReport => 
                            farm.farmer_services.some(farmerService => 
                                techReport.diagnosis_disease === selected  &&
                                techReport.farm_id === farmerService.farm_id && 
                                (farmerService.status === 'Verified' || farmerService.status === 'False Detection')
                            ) 
                        );
                    });
                } else if (condition === 'treated') {
                    filteredFarms = simulate.filter(farm => {
                        return farm.technician_reports.some(techReport => 
                            farm.farmer_services.some(farmerService => 
                                techReport.farm_id === farmerService.farm_id &&
                                techReport.diagnosis_disease === selected && farmerService.status === 'Treated'
                            )
                        );
                    });
                } else {
                    filteredFarms = simulate.filter(farm => {
                        return farm.technician_reports.some(report => report.diagnosis_disease === selected) 
                    });
                }
            }
            const farmFeatures = [];
            const technicianGeofences = [];
            filteredFarms.forEach(farm => {
                const longitude = parseFloat(farm.longitude);
                const latitude = parseFloat(farm.latitude);
                if (isNaN(longitude) || isNaN(latitude)) {
                    console.warn(`Invalid coordinates for farm ${farm.id}: ${farm.longitude}, ${farm.latitude}`);
                    return;
                }
                const { isVerified, isTreated, progress: farmProgress } = getFarmState(selected, farm) ?? { isVerified: false, isTreated: false, progress: 0 };
                // const { isVerified, isTreated, progress: farmProgress } = getFarmState(selected, farm);
                let pointColor;
                // if (farm.technician_reports.length === 0 || farm.technician_reports.length === 1) {
                if (farm.farmer_reports.length === 0 && farm.technician_reports.length === 0) {
                    pointColor = '#00FF00';
                } else if (isVerified) {
                    // pointColor = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 0, 0]]);
                    pointColor = unTreatedInterpolateColor(farmProgress, [[255, 0, 0], [255, 0, 0]]);
                } 
                else if (isTreated) {
                    pointColor = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 0, 0]]);
                }
                else {
                    pointColor = '#00FF00';
                }
                // Add farm point
                farmFeatures.push({
                    type: 'Feature',
                    geometry: { type: 'Point', coordinates: [longitude, latitude] },
                    properties: {
                        municipal: farm.municipal,
                        disease: farm.technician_reports[0]?.diagnosis_disease || farm.farmer_reports[0]?.disease || 'Unknown',
                        farmId: farm.id,
                        pointColor,
                        pointType: 'farm'
                    }
                });
                // Add technician points and create geofence
                if (farm.technician_reports && Array.isArray(farm.technician_reports)) {
                    const technicianCoords = [];
                    farm.technician_reports.forEach(report => {
                        if (report.coordinates && Array.isArray(report.coordinates)) {
                            report.coordinates.forEach(coord => {
                                const [lat, lon] = coord.split(',').map(Number);
                                if (!isNaN(lat) && !isNaN(lon)) {
                                    technicianCoords.push([lon, lat]);
                                    farmFeatures.push({
                                        type: 'Feature',
                                        geometry: { type: 'Point', coordinates: [lon, lat] },
                                        properties: {
                                            municipal: farm.municipal,
                                            disease: report.diagnosis_disease || 'Unknown',
                                            farmId: farm.id,
                                            pointColor,
                                            pointType: 'technician'
                                        }
                                    });
                                }
                            });
                        }
                    });

                    if (technicianCoords.length > 0) {
                        // Calculate centroid of technician coordinates
                        const centroid = technicianCoords.reduce(
                            (acc, coord) => [acc[0] + coord[0] / technicianCoords.length, acc[1] + coord[1] / technicianCoords.length],
                            [0, 0]
                        );
                        const minRadius = 0.01;
                        const maxRadius = 0.02;
                        let baseRadius, color;
                        // REMOVE: const isTreated = false;

                        // if (farm.farmer_reports.length === 0 && farm.technician_reports.length === 0) {
                        //      baseRadius = 0.00;
                        // }
                         if (isVerified) {
                            // if (farmProgress > 0.6 || farmProgress > 0.2) {
                            //     // baseRadius = interpolateSize(1 - farmProgress, 0, maxRadius);
                            //     // color = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 0, 0]]);
                            //     baseRadius = interpolateSize(farmProgress, minRadius, maxRadius);
                            //     color = unTreatedInterpolateColor(farmProgress, [[255, 0, 0], [0, 255, 0]]);
                            // } else {
                                // baseRadius = interpolateSize(farmProgress, minRadius, maxRadius);
                                // color = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 0, 0]]);
                                baseRadius = interpolateSize(farmProgress, minRadius, maxRadius);
                                color = unTreatedInterpolateColor(farmProgress, [[255, 0, 0], [0, 255, 0]]); 
                            // }
                        }
                        else if (isTreated) {
                            // baseRadius = interpolateSize(0.5 + farmProgress, minRadius, maxRadius);
                            // color = unTreatedInterpolateColor(farmProgress, [[255, 0, 0], [255, 0, 0]]);
                            // baseRadius = interpolateSize(farmProgress, minRadius, maxRadius);
                             if (farmProgress > 0.6 || farmProgress > 0.2) {
                                // baseRadius = interpolateSize(1 - farmProgress, 0, maxRadius);
                                baseRadius = 0.00;
                                color = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 255, 0]]);
                            } else {
                                baseRadius = interpolateSize(farmProgress, minRadius, maxRadius);
                                // baseRadius = 0.00;
                                color = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 255, 0]]);
                            }
                        }
                        else {
                            // baseRadius = interpolateSize(1 - farmProgress, minRadius, maxRadius);
                            baseRadius = 0.00;
                            color = '#00FF00';
                            // color = unTreatedInterpolateColor(farmProgress, [[255, 0, 0], [0, 255, 0]]);
                        }
                        const zoom = map_simulation.getZoom();
                        const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5;
                        const geofence = simulateGenerateRandomPolygon(centroid, 9, baseRadius);
                        technicianGeofences.push({
                            ...scalePolygon(geofence, 1, zoomFactor),
                            properties: {
                                farmId: farm.id,
                                disease: farm.technician_reports[0]?.diagnosis_disease || 'Unknown',
                                center: centroid,
                                color: `rgb(${color[0]}, ${color[1]}, ${color[2]})`, 
                                baseRadius
                            }
                        });
                    }
                }
            });

            const farmPolygons = farmFeatures
                .filter(f => f.properties.pointType === 'farm')
                .map(farm => {
                    const center = farm.geometry.coordinates;
                    const originalFarm = simulate.find(f => f.id === farm.properties.farmId);
                    // const { isVerified, progress: farmProgress } = getFarmState(selected, originalFarm) ?? { isVerified: false, progress: 0 };
                    const { isVerified, isTreated, progress: farmProgress } = getFarmState(selected, originalFarm) ?? { isVerified: false, isTreated: false, progress: 0 };
                    // const { isVerified, isTreated, progress: farmProgress } = getFarmState(selected, originalFarm);
                    // const originalFarm = simulate.find(f => f.id === farm.properties.farmId);
                    // const { isVerified, progress: farmProgress } = getFarmState(originalFarm, durationMs);
                    if (originalFarm.farmer_reports.length === 0 && originalFarm.technician_reports.length === 0) {
                        return null;
                    }
                    const minRadius = 0.00;
                    const maxRadius = 0.010;
                    let baseRadius, color;
                    if (isVerified) {
                        baseRadius = interpolateSize(0.5 + farmProgress, minRadius, maxRadius);
                        color = unTreatedInterpolateColor(farmProgress, [[255, 0, 0], [255, 0, 0]]); 
                    } 
                    else if (isTreated) {
                        if (farmProgress > 0.6 || farmProgress > 0.2) {
                            baseRadius = interpolateSize(1 - farmProgress, minRadius, maxRadius);
                            color = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 255, 0]]);
                        } else {
                            baseRadius = interpolateSize(1- farmProgress, minRadius, maxRadius);
                            color = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 255, 0]]);
                        }
                    }
                    else {
                        baseRadius = 0.00;
                        color = treatedInterpolateColor(farmProgress, [[255, 255, 0], [255, 0, 0]]);
                    }

                    const zoom = map_simulation.getZoom();
                    const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5;
                    const polygon = simulateGenerateRandomPolygon(center, 9, baseRadius);
                    return {
                        ...scalePolygon(polygon, 1, zoomFactor),
                        properties: {
                            municipal: farm.properties.municipal,
                            disease: farm.properties.disease,
                            farmId: farm.properties.farmId,
                            center,
                            color: `rgb(${color[0]}, ${color[1]}, ${color[2]})`,
                            baseRadius
                        }
                    };
                }).filter(p => p !== null && p.properties.color);

            map_simulation.getSource('farms').setData({
                type: 'FeatureCollection',
                features: farmFeatures
            });

            map_simulation.getSource('farm-polygons').setData({
                type: 'FeatureCollection',
                features: farmPolygons
            });

            map_simulation.getSource('technician-geofences').setData({
                type: 'FeatureCollection',
                features: technicianGeofences
            });

            return { farmPolygons, orderedDays };
        }

        function animatePolygons(selectedDisease, farmPolygons) { 
            let isAnimating = false;
            const duration = 7000;
            const maxScale = 2;
            if (isAnimating) return;
            isAnimating = true;
            const startTime = Date.now();
            // Store initial states
            const initialPolygons = farmPolygons.map(p => ({ ...p }));
            const initialGeofences = map_simulation.getSource('technician-geofences')._data.features.map(g => ({ ...g }));

            function step() {
                const elapsedTime = Date.now() - startTime;
                const animationProgress = Math.min(elapsedTime / duration, 1);
                const zoom = map_simulation.getZoom();
                const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5;

                const processedData = [...initialPolygons, ...initialGeofences].map(feature => {
                    const farm = simulate.find(f => f.id === feature.properties.farmId);
                    const { isVerified, progress: farmProgress } = farm ? getFarmState(selectedDisease, farm) : { isVerified: false, progress: 0 };
                    const isGeofence = feature.source === 'technician-geofences';
                    let progress, color, scale;
                    const isTreated = false;
                    if (isVerified) {   
                             progress = farmProgress + (animationProgress * (1 + farmProgress));
                        color = unTreatedInterpolateColor(progress, [[255, 0, 0], [255, 0, 0]]);
                        scale = 1 + animationProgress;
                        const scaledFeature = scalePolygon(feature, scale, zoomFactor);
                            return {
                            ...scaledFeature,
                            properties: {
                                ...scaledFeature.properties,
                                color: `rgb(${color[0]}, ${color[1]}, ${color[2]})`,
                                sourceType: isGeofence ? 'geofence' : 'polygon'
                            }
                        };
                    }
                    else if (isTreated) {
                        progress = farmProgress + (animationProgress * (1 - farmProgress));
                        color = treatedInterpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
                        scale = 1 - animationProgress;
                        const scaledFeature = scalePolygon(feature, scale, zoomFactor);
                        return {
                            ...scaledFeature,
                            properties: {
                                ...scaledFeature.properties,
                                color: `rgb(${color[0]}, ${color[1]}, ${color[2]})`,
                                sourceType: isGeofence ? 'geofence' : 'polygon'
                            }
                        };
                    } else {
                        return {
                            ...feature,
                            properties: {
                                ...feature.properties,
                                color: feature.properties.color || '#ff6666',
                                sourceType: isGeofence ? 'geofence' : 'polygon'
                            }
                        };
                    }
                });
                // Split processed data back into polygons and geofences
                const currentPolygons = processedData.filter(f => f.properties.sourceType === 'polygon');
                const currentGeofences = processedData.filter(f => f.properties.sourceType === 'geofence');
                // Update all sources
                map_simulation.getSource('farm-polygons').setData({
                    type: 'FeatureCollection',
                    features: currentPolygons
                });
                map_simulation.getSource('technician-geofences').setData({
                    type: 'FeatureCollection',
                    features: currentGeofences
                });
                // Update farm points colors
                const farmFeatures = map_simulation.getSource('farms')._data.features.map(f => {
                    // if (selectedCondition === 'not_treated') {
                    //     f.properties.pointColor = '#00FF00';
                    // } else if (selectedCondition === 'treated') {
                    //     f.properties.pointColor = '#FF0000';
                    // } 
                    const polygon = currentPolygons.find(p => p.properties.farmId === f.properties.farmId);
                    return polygon ? { ...f, properties: { ...f.properties, pointColor: polygon.properties.color } } : f;
                });
                map_simulation.getSource('farms').setData({
                    type: 'FeatureCollection',
                    features: farmFeatures
                });
                if (animationProgress >= 1) {
                    isAnimating = false;
                } else {
                    requestAnimationFrame(step);
                }
            }
            requestAnimationFrame(step);
        }
   
        const descriptions = {
            'Stem Bleeding': `
                <h6 class="fw-bold">What is Stem Bleeding? (Ano ang Stem Bleeding?)</h6>
                <ul>
                    <li>Stem Bleeding is a coconut disease where red-brown liquid leaks from the trunk, leading to rot and weakness of the tree.
                        (Ang Stem Bleeding ay isang sakit ng niyog kung saan may tumutulong pulang likido mula sa katawan ng puno, na nagiging 
                        sanhi ng pagkabulok at paghina ng puno.)</li>
                    <li>Causes (Sanhi) - Caused by a soil fungus called Thielaviopsis paradoxa, especially when the trunk is wounded.
                        (Dulot ito ng fungus sa lupa na tinatawag na Thielaviopsis paradoxa, lalo na kapag may sugat ang puno.)</li>
                </ul>
            `,
            'Lethal Yellowing': `
               <h6 class="fw-bold">What is Lethal Yellowing? (Ano ang Lethal Yellowing?)</h6>
                <ul>
                    <li>Lethal Yellowing is a serious coconut disease where the fruits and flowers die early, and the whole tree eventually collapses.
                        (Ang Lethal Yellowing ay isang malalang sakit ng niyog kung saan maaga pang nalalaglag ang bunga at namamatay ang mga bulaklak,
                         hanggang sa tuluyang bumagsak ang buong puno.)</li>
                    <li>Causes (Sanhi) - It is caused by a phytoplasma (a type of bacteria) carried by insects like planthoppers.
                        (Dulot ito ng phytoplasma (uri ng bacteria) na ikinakalat ng mga insekto tulad ng planthopper.)</li>
                </ul>
            `,
            'Leaf Spot': `
                <h6 class="fw-bold">What is Leaf Spot? (Ano ang Leaf Spot?)</h6>
                <ul>
                    <li>Leaf Spot is a common disease in coconut trees where small spots appear on the leaves. These spots usually
                        start yellow or brown and can spread, making the leaves weak and dry. (Ang Leaf Spot ay isang karaniwang sakit
                        sa puno ng niyog kung saan lumilitaw ang maliliit na batik sa mga dahon. Karaniwan, nagsisimula itong dilaw o
                        kayumanggi at kumakalat, kaya humihina at natutuyo ang mga dahon.)</li>
                    <li>Causes (Sanhi) - It is caused by fungi, like Pestalotiopsis palmarum, especially when the weather is wet or humid.
                        (Dulot ito ng fungi o amag, tulad ng Pestalotiopsis palmarum, lalo na kapag basa o mahalumigmig ang panahon.)</li>
                </ul>
            `
        };
        const descriptionText = document.getElementById('description-text');
        descriptionText.innerHTML = `<p class="mt-2 text-center">Please select a disease to view its descriptions.</p>`;
        const treatments = {
            'Stem Bleeding': `
                <ul>
                    <li>Carve the affected tissue and then burn it.<br>(Pag-ukit ng apektadong tissue at pagkatapos ay sunugin ito.)</li>
                    <li>Spray using a copper-based fungicide (captan).<br>(Pagspray gamit ang copper-based na fungicide (captan).)</li>
                    <li>Paint the treated wounds with coal tar.<br>(Pagpipinta ng ginamot na sugat gamit ang coal tar.)</li>
                    <li>Ensure adequate drainage to prevent changes in soil moisture.<br>(Ang sapat na drainage upang maiwasan ang pagbabago sa moisture ng lupa.)</li>
                </ul>
            `,
            'Lethal Yellowing': `
                <ul>
                    <li>Remove and dispose of infected trees.<br>(Pag-alis at pagkakalat ng infected na puno.)</li>
                    <li>Avoid overcrowding of coconut trees to prevent rapid disease spread.<br>(Iwasan ang overcrowding ng mga puno ng niyog upang maiwasan ang mabilis na pagkalat ng sakit.)</li>
                    <li>Regularly inspect the trees to promptly detect early symptoms of Lethal Yellowing.<br>(Regular na inspeksyunin ang mga puno upang madaliang matukoy ang mga unang sintomas ng Lethal Yellowing.)</li>
                    <li>Improve overall tree health.<br>(Pagpapabuti ng kalusugan ng puno.)</li>
                    <li>Consult local agronomists or coconut experts for proper measures and technical support in managing Lethal Yellowing.<br>(Makipag-ugnayan sa mga lokal na agronomist o mga eksperto sa niyog para sa mga tamang hakbang at teknikal na suporta sa pamamahala ng Lethal Yellowing.)</li>
                </ul>
            `,
            'Leaf Spot': `
                <ul>
                    <li>Regularly remove affected leaves and dispose of them properly to prevent disease spread.<br>(Regular na alisin ang mga apektadong dahon at itapon ang mga ito nang maayos upang maiwasan ang pagkalat ng sakit.)</li>
                    <li>Do not dispose of them on the plantation soil to avoid re-infection by fungi.<br>(Huwag itong itapon sa lupa ng taniman upang hindi maging sanhi ng muling pagkalat ng fungi.)</li>
                    <li>Spray with fungicides such as copper-based fungicides.<br>(Pag-spray ng mga fungicide tulad ng copper-based fungicides.)</li>
                    <li>Ensure that the coconut trees receive proper nutrition and care to boost their resistance against diseases.<br>(Siguraduhin na ang mga puno ng niyog ay may tamang nutrisyon at sapat na pag-aalaga upang mapalakas ang kanilang resistensya laban sa mga sakit.)</li>
                    <li>Clean the area around the coconut trees from fallen leaves, branches, and other plant debris that may harbor fungal spores.<br>(Linisin ang paligid ng puno ng niyog mula sa mga natirang dahon, sanga, at iba pang plant debris na maaaring maglaman ng mga spora ng fungus.)</li>
                    <li>Regularly inspect the trees to detect early symptoms of the disease.<br>(Regular na inspeksyunin ang mga puno upang makita ang mga maagang sintomas ng sakit.)</li>
                </ul>
            `
        };
        const treatmentText = document.getElementById('treatment-text');
        treatmentText.innerHTML = `<p class="mt-2 text-center">Please select a disease to view its treatment recommendations.</p>`;
        const { farmPolygons: initialFarmPolygons } = updateFarmData('All_Diseases', null);
        let intervalId;
        function interval(simulate) {
            if (intervalId) {
                clearInterval(intervalId);
            }
            const updateFunction = () => updateFarmData(document.getElementById('disease').value, document.getElementById('condition').value);
            // const updateFunction = () => updateFarmData(document.getElementById('disease').value);
            if (simulate === 'simulate') {
                document.getElementById('disease').disabled = true; 
                setTimeout(() => {
                    updateFunction();
                    intervalId = setInterval(updateFunction, 1000);
                    document.getElementById('disease').disabled = false; 
                }, 7000);
            } else {
                intervalId = setInterval(updateFunction, 1000); 
            }
        }
        // document.getElementById('condition').disabled = true;
        // document.getElementById('condition').prop('disabled', true);
        $('#condition').prop('disabled', false);
        document.getElementById('condition').style.opacity = null;
        document.getElementById('disease').addEventListener('change', (event) => {
            const selectedDisease = event.target.value;
            descriptionText.innerHTML =  descriptions[selectedDisease] || `<p class="mt-2 text-center">Please select a disease to view its descriptions.</p>`;
            const { farmPolygons } = updateFarmData(selectedDisease, null);
            console.log('farmPolygons', farmPolygons);
            // const { farmPolygons } = updateFarmData(selectedDisease);
            if ((farmPolygons.length > 0) || (selectedDisease === 'All_Diseases')) {
                document.getElementById('condition').disabled = false;
                document.getElementById('condition').style.opacity = null;
                map_simulation.setZoom(0);
            } else if ( farmPolygons.length === 0) {
                map_simulation.setZoom(0);
                document.getElementById('condition').disabled = true;
                document.getElementById('condition').style.opacity = 0.7;
            } 
            // else {
            //     document.getElementById('condition').disabled = false;
            //     document.getElementById('condition').style.opacity = null;
            // }
            document.getElementById('condition').addEventListener('change', (event) => {
                const selectedCondition = event.target.value;
                const { farmPolygons } = updateFarmData(selectedDisease, selectedCondition);
                if (farmPolygons.length > 0 && (selectedCondition !== 'All_Conditions')) {
                }
                if (selectedCondition === 'All_Conditions') {
                    treatmentText.innerHTML = `<p class="mt-3 text-center">All Farms that are both not treated and treated with the selected disease.</p>`;
                    map_simulation.setZoom(0);
                }
                else if (selectedCondition === 'not_treated') {
                    treatmentText.innerHTML = `<p class="mt-3 text-center">All Farms that are not yet treated with the selected disease.</p>`;
                } else {
                    treatmentText.innerHTML = treatments[selectedDisease] || `<p class="mt-2 text-center">Please select a disease to view its treatment recommendations.</p>`;
                }
            });

        }); 
        document.getElementById('simulate-button').addEventListener('click', () => { 
            const selectedDisease = document.getElementById('disease').value;
            const selectedCondition = document.getElementById('condition').value;
            const { farmPolygons } = selectedDisease === 'All_Diseases'
                ? { farmPolygons: initialFarmPolygons }
                : updateFarmData(selectedDisease, selectedCondition);
            animatePolygons(selectedDisease, farmPolygons);
            const progressBar = document.getElementById('week-progress');
            function startProgress() {
                progressBar.style.transition = 'none'; 
                progressBar.style.width = '0%';
                void progressBar.offsetWidth;
                $('#simulate-button').text('Simulating . .').prop('disabled', true);
                progressBar.style.transition = 'width 7s linear';
                progressBar.style.width = '100%';
                // progressBar.style.transition = `width ${duration}ms linear`;
                // progressBar.style.transition = `width ${durationMs}ms linear`;
                // progressBar.style.width = '100%';
            }
            // Initial start
            startProgress();
            progressBar.addEventListener('transitionend', function handler(e) {
                if (e.propertyName === 'width' && progressBar.style.width === '100%') {
                    // progressBar.style.transition = 'none'; 
                    progressBar.style.transition = 'width 1s linear';
                    progressBar.style.width = '0%';
                    $('#simulate-button').text('Simulate').prop('disabled', false);
                }
            }, { once: false }); 
        
            interval('simulate');
        });
        interval();
        
        // Popup handling
        let popup = null;
        let popupTimer = null;
        map_simulation.on('mouseenter', 'farm-points', (e) => {
            if (popupTimer) { clearTimeout(popupTimer); popupTimer = null; }
            if (popup) { popup.remove(); popup = null; }
            if (e.features.length > 0) {
                const feature = e.features[0];
                const coordinates = feature.geometry.coordinates.slice();
                const farmId = feature.properties.farmId;
                const farm = simulate.find(f => String(f.id) === String(farmId));
                const condition = farm.technician_reports.some(techReport => 
                    farm.farmer_services.some(farmerService => 
                        techReport.farm_id === farmId && farmerService.status === 'Treated'
                    )
                );
                if (!condition && farm) {
                    let detailsHTML = '';
                    if (Array.isArray(farm.technician_reports) && farm.technician_reports.length > 0) {
                        const tech = farm.technician_reports[0];
                        if (tech.diagnosis_disease) {
                            if (farm.address) detailsHTML += `<p><strong>Address:</strong> ${farm.address}</p>`;
                            detailsHTML += `<p><strong>Status: <span class="text-danger">Infected</span></strong> </p>`;
                            detailsHTML += `<p><strong>Disease: <span class="text-danger"> ${tech.diagnosis_disease}</span></strong> </p>`;
                            if (tech.no_of_infected !== undefined && tech.no_of_infected !== null) {
                                detailsHTML += `<p><strong>Trees Infected: <span class="text-danger">${tech.no_of_infected}</span></strong></p>`;
                                const htmlContent = `
                                    <div class="custom-map-popup">
                                        <h5><strong> ${farm.name || 'Farm Details'} </strong></h5>
                                        <hr>
                                        <div class="popup-details">
                                            ${detailsHTML}
                                        </div>
                                    </div>
                                `;
                                popup = new mapboxgl.Popup({
                                    closeButton: false,
                                    closeOnClick: false,
                                    offset: 15,
                                    className: 'my-custom-mapbox-popup'
                                })
                                .setLngLat(coordinates)
                                .setHTML(htmlContent)
                                .addTo(map_simulation);
                            }
                        }
                    }
                    // if (!detailsHTML) detailsHTML = '<p>No details available.</p>';
                }
            }
        });
        map_simulation.on('mouseleave', 'farm-points', () => {
            if (popupTimer) { clearTimeout(popupTimer); popupTimer = null; }
            if (popup) { popup.remove(); popup = null; }
        });
    });
}