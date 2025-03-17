
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
                console.log('simulation', simulation);
                if (locationContainer) {
                    locationContainer.innerHTML = "";
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

        const farmFeatures = farm_loc.map(farm => ({
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [parseFloat(farm.longitude), parseFloat(farm.latitude)]
            },
            properties: {
                municipal: farm.municipal 
            }
        }));

        const farmPolygons = farmFeatures.map(farm => {
            const center = farm.geometry.coordinates;
            return {
                ...generateRandomPolygon(center),
                properties: {
                    municipal: farm.properties.municipal 
                }
            };
        });

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
                'circle-color': '#FFD700'
            }
        });

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
                'line-color': '#FFD700',
                'line-width': 2,
                'line-dasharray': [2, 2]
            }
        });

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

        document.getElementById('location').addEventListener('change', (event) => {
            const selectedName = event.target.value;
            if (selectedName === 'All Municipal') {
                setTimeout(() => {
                    document.getElementById('details').style.display = 'none';
                    displayMap(farm_loc, locations, mapboxgl); 
                }, 500);
            } else if (selectedName) {
                map_locations.setFilter('laguna-points', ['==', 'name', selectedName]);
                map_locations.setFilter('farm-points', ['==', ['get', 'municipal'], selectedName]);
                map_locations.setFilter('farm-polygons-outline', ['==', ['get', 'municipal'], selectedName]);
                locations.forEach(loc => {
                    if (loc.name === selectedName) { 
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
        map_locations.on('mouseenter', 'laguna-points', (e) => {
            const coordinates = e.features[0].geometry.coordinates.slice();
            const properties = e.features[0].properties;
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

// function simulateGenerateRandomPolygon(center, numPoints = 9, radius = 0.01) {
//     const points = [];
//     for (let i = 0; i < numPoints; i++) {
//         const angle = (i / numPoints) * 2 * Math.PI;
//         const randomRadius = radius * (0.5 + Math.random() * 0.5);
//         const randomAngle = angle + (Math.random() - 0.5) * 0.5;
//         const lon = center[0] + randomRadius * Math.cos(randomAngle);
//         const lat = center[1] + randomRadius * Math.sin(randomAngle);
//         points.push([lon, lat]);
//     }
//     points.push(points[0]);
//     return {
//         type: 'Feature',
//         geometry: {
//             type: 'Polygon',
//             coordinates: [points]
//         },
//         properties: { center }
//     };
// }

// function scalePolygon(polygon, k, zoomFactor = 1) {
//     const center = polygon.properties.center;
//     const points = polygon.geometry.coordinates[0];
//     const scaledPoints = points.map(point => {
//         const newLon = center[0] + k * zoomFactor * (point[0] - center[0]);
//         const newLat = center[1] + k * zoomFactor * (point[1] - center[1]);
//         return [newLon, newLat];
//     });
//     return {
//         type: 'Feature',
//         geometry: {
//             type: 'Polygon',
//             coordinates: [scaledPoints]
//         },
//         properties: polygon.properties
//     };
// }


// Helper function to convert RGB to HSL

// Interpolate between two colors using HSL
// function interpolateColor(progress, colors) {
//     const [start, end] = colors.map(c => rgbToHsl(...c));
//     const h = start[0] + progress * (end[0] - start[0]);
//     const s = start[1] + progress * (end[1] - start[1]);
//     const l = start[2] + progress * (end[2] - start[2]);
//     return hslToRgb(h, s, l);
// }




// Generate a random polygon around a center point
// function simulateGenerateRandomPolygon(center, numPoints = 9, radius = 0.01) {
//     const points = [];
//     for (let i = 0; i < numPoints; i++) {
//         const angle = (i / numPoints) * 2 * Math.PI;
//         const randomRadius = radius * (0.5 + Math.random() * 0.5);
//         const randomAngle = angle + (Math.random() - 0.5) * 0.5;
//         const lon = center[0] + randomRadius * Math.cos(randomAngle);
//         const lat = center[1] + randomRadius * Math.sin(randomAngle);
//         points.push([lon, lat]);
//     }
//     points.push(points[0]);
//     return {
//         type: 'Feature',
//         geometry: { type: 'Polygon', coordinates: [points] },
//         properties: { center }
//     };
// }

// Scale the polygon based on factor k and zoomFactor
// function scalePolygon(polygon, k, zoomFactor = 1) {
//     const center = polygon.properties.center;
//     const points = polygon.geometry.coordinates[0];
//     const scaledPoints = points.map(point => {
//         const newLon = center[0] + k * zoomFactor * (point[0] - center[0]);
//         const newLat = center[1] + k * zoomFactor * (point[1] - center[1]);
//         return [newLon, newLat];
//     });
//     return {
//         type: 'Feature',
//         geometry: { type: 'Polygon', coordinates: [scaledPoints] },
//         properties: polygon.properties
//     };
// }

// Main map simulation function
// async function mapSimulation(simulate, mapboxgl) {
//     const map_simulation = new mapboxgl.Map({
//         container: 'map_simulation',
//         style: 'mapbox://styles/mapbox/satellite-streets-v11',
//         center: [121.37575102352582, 14.27976852821142],
//         zoom: 9,
//         minZoom: 0,
//         maxZoom: 15,
//         maxBounds: [[120.70, 13.92], [121.95, 14.65]]
//     });

//     map_simulation.on('load', async () => {
//         const data = await geoJsonData();
//         map_simulation.addSource('municipal', { type: 'geojson', data });
//         map_simulation.addLayer({
//             id: 'laguna-boundary',
//             type: 'line',
//             source: 'municipal',
//             filter: ['==', ['geometry-type'], 'Polygon'],
//             paint: {
//                 'line-color': '#FFD700',
//                 'line-width': 3,
//                 'line-dasharray': [2, 2]
//             }
//         });

//         map_simulation.addSource('farms', {
//             type: 'geojson',
//             data: { type: 'FeatureCollection', features: [] }
//         });

//         map_simulation.addLayer({
//             id: 'farm-points',
//             type: 'circle',
//             source: 'farms',
//             paint: {
//                 'circle-radius': 5,
//                 'circle-color': ['get', 'pointColor']
//             }
//         });

//         map_simulation.addSource('farm-polygons', {
//             type: 'geojson',
//             data: { type: 'FeatureCollection', features: [] }
//         });

//         map_simulation.addLayer({
//             id: 'farm-polygons-fill',
//             type: 'fill',
//             source: 'farm-polygons',
//             paint: {
//                 'fill-color': ['get', 'color'],
//                 'fill-opacity': 0.4,
//                 'fill-outline-width': [
//                     'interpolate', ['linear'], ['zoom'],
//                     0, 0.5, 11, 2
//                 ]
//             }
//         });

//         // Zoom listener to adjust polygon size based on zoom level
//         map_simulation.on('zoom', () => {
//             const zoom = map_simulation.getZoom();
//             const zoomFactor = Math.pow(2, (9 - zoom)) * 0.5;
//             const currentData = map_simulation.getSource('farm-polygons')._data;
//             const scaledFeatures = currentData.features.map(feature => scalePolygon(feature, 1, zoomFactor));
//             map_simulation.getSource('farm-polygons').setData({
//                 type: 'FeatureCollection',
//                 features: scaledFeatures
//             });
//         });

//         // Get farm state based on latest reports
//         function getFarmState(farm) {
//             const now = new Date();
//             const sevenDays = 7 * 24 * 60 * 60 * 1000;

//             const latestFarmerReport = farm.farmer_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
//             const latestTechnicianReport = farm.technician_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

//             if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
//                 const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
//                 const progress = Math.min(timeSinceTreatment / sevenDays, 1);
//                 return { isTreated: true, progress };
//             } else if (latestFarmerReport) {
//                 const timeSinceReport = now - new Date(latestFarmerReport.created_at);
//                 const progress = Math.min(timeSinceReport / sevenDays, 1);
//                 return { isTreated: false, progress };
//             } else {
//                 return { isTreated: false, progress: 0 };
//             }
//         }

//         // Update farm data based on selected disease
//         function updateFarmData(selectedDisease) {
//             let filteredFarms = selectedDisease === 'All' ? simulate : simulate.filter(farm =>
//                 farm.technician_reports.some(report => report.diagnosis_disease === selectedDisease) ||
//                 farm.farmer_reports.some(report => report.disease === selectedDisease)
//             );

//             const farmFeatures = filteredFarms.map(farm => {
//                 const longitude = parseFloat(farm.longitude);
//                 const latitude = parseFloat(farm.latitude);
//                 if (isNaN(longitude) || isNaN(latitude)) return null;

//                 const { isTreated, progress } = getFarmState(farm);
//                 let pointColor;

//                 if (farm.farmer_reports.length === 0 && farm.technician_reports.length === 0) {
//                     pointColor = '#00FF00'; // Green for no reports
//                 } else if (isTreated) {
//                     const rgb = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
//                     pointColor = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
//                 } else {
//                     const rgb = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
//                     pointColor = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
//                 }

//                 return {
//                     type: 'Feature',
//                     geometry: { type: 'Point', coordinates: [longitude, latitude] },
//                     properties: {
//                         municipal: farm.municipal,
//                         disease: farm.technician_reports[0]?.diagnosis_disease || farm.farmer_reports[0]?.disease || 'Unknown',
//                         farmId: farm.id,
//                         pointColor
//                     }
//                 };
//             }).filter(feature => feature !== null);

//             const farmPolygons = farmFeatures.map(farm => {
//                 const center = farm.geometry.coordinates;
//                 const originalFarm = simulate.find(f => f.id === farm.properties.farmId);
//                 const { isTreated, progress } = getFarmState(originalFarm);

//                 if (originalFarm.farmer_reports.length === 0 && originalFarm.technician_reports.length === 0) return null;

//                 const minRadius = 0.01;
//                 const maxRadius = 0.02;
//                 let baseRadius, color;

//                 if (isTreated) {
//                     const rgb = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
//                     color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
//                     baseRadius = interpolateSize(1 - progress, 0, maxRadius);
//                 } else {
//                     const rgb = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
//                     color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
//                     baseRadius = interpolateSize(progress, minRadius, maxRadius);
//                 }

//                 const zoom = map_simulation.getZoom();
//                 const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5;
//                 const polygon = simulateGenerateRandomPolygon(center, 9, baseRadius);
//                 return {
//                     ...scalePolygon(polygon, 1, zoomFactor),
//                     properties: {
//                         municipal: farm.properties.municipal,
//                         disease: farm.properties.disease,
//                         farmId: farm.properties.farmId,
//                         center,
//                         color,
//                         baseRadius
//                     }
//                 };
//             }).filter(p => p !== null && p.properties.color);

//             map_simulation.getSource('farms').setData({
//                 type: 'FeatureCollection',
//                 features: farmFeatures
//             });

//             map_simulation.getSource('farm-polygons').setData({
//                 type: 'FeatureCollection',
//                 features: farmPolygons
//             });

//             return { farmPolygons };
//         }

//         // Helper function to interpolate size
//         function interpolateSize(progress, minSize, maxSize) {
//             return minSize + progress * (maxSize - minSize);
//         }

//         // Animate polygons for simulation
//         function animatePolygons(farmPolygons) {
//             const duration = 7000; // 7 seconds
//             const startTime = Date.now();
//             let isAnimating = false;

//             if (isAnimating) return;
//             isAnimating = true;

//             function step() {
//                 const elapsed = Date.now() - startTime;
//                 const animationProgress = Math.min(elapsed / duration, 1);

//                 const updatedPolygons = farmPolygons.map(polygon => {
//                     const farm = simulate.find(f => f.id === polygon.properties.farmId);
//                     const { isTreated, progress: farmProgress } = getFarmState(farm);

//                     if (isTreated && farmProgress >= 1) {
//                         polygon.properties.color = 'rgb(0, 255, 0)'; // Fully treated
//                     } else {
//                         let color;
//                         if (isTreated) {
//                             const rgb = interpolateColor(animationProgress, [[255, 0, 0], [0, 255, 0]]);
//                             color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
//                         } else {
//                             const rgb = interpolateColor(animationProgress, [[255, 255, 0], [255, 0, 0]]);
//                             color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
//                         }
//                         polygon.properties.color = color;
//                     }
//                     return polygon;
//                 });

//                 map_simulation.getSource('farm-polygons').setData({
//                     type: 'FeatureCollection',
//                     features: updatedPolygons
//                 });

//                 if (animationProgress < 1) {
//                     requestAnimationFrame(step);
//                 } else {
//                     isAnimating = false;
//                 }
//             }
//             requestAnimationFrame(step);
//         }

//         // Event listener for disease selection
//         document.getElementById('disease').addEventListener('change', (event) => {
//             const selectedDisease = event.target.value;
//             updateFarmData(selectedDisease);
//         });

//         // Event listener for simulate button
//         document.getElementById('silumate-button').addEventListener('click', () => {
//             const selectedDisease = document.getElementById('disease').value;
//             const { farmPolygons } = updateFarmData(selectedDisease);
//             animatePolygons(farmPolygons);

//             const progressBar = document.getElementById('week-progress');
//             progressBar.style.transition = 'width 7s linear';
//             progressBar.style.width = '100%';

//             setTimeout(() => {
//                 progressBar.style.width = '0%';
//             }, 7000);
//         });

//         // Initial data load
//         updateFarmData('All');
//     });
// }



// async function mapSimulation(simulate, mapboxgl) {
//     const simulationContainer = document.getElementById('simulationContainer');
//     if (simulationContainer) simulationContainer.innerHTML = "";

//     const map_simulation = new mapboxgl.Map({
//         container: 'map_simulation',
//         style: 'mapbox://styles/mapbox/satellite-streets-v11',
//         center: [121.37575102352582, 14.27976852821142],
//         zoom: 0,
//         minZoom: 0,
//         maxZoom: 15,
//         maxBounds: [
//             [120.70, 13.92],
//             [121.95, 14.65]
//         ]
//     });

//     map_simulation.on('load', async () => {
//         const data = await geoJsonData();

//         map_simulation.addSource('municipal', { type: 'geojson', data });
//         map_simulation.addLayer({
//             id: 'laguna-boundary',
//             type: 'line',
//             source: 'municipal',
//             // filter: ['==', '$type', 'Polygon'],
//             filter: ['==', ['geometry-type'], 'Polygon'],
//             paint: {
//                 'line-color': '#FFD700',
//                 'line-width': 3,
//                 'line-dasharray': [2, 2],
//             }
//         });

//         // map_simulation.addLayer({
//         //     id: 'laguna-boundary',
//         //     type: 'fill',
//         //     source: 'municipal',
//         //     // filter: ['==', '$type', 'Polygon'],
//         //     filter: ['==', ['geometry-type'], 'Polygon'],
//         //     paint: {
//         //         'fill-color': 'darkblue',
//         //         'fill-opacity': 0.3
//         //     }
//         // });

//         map_simulation.addSource('farms', {
//             type: 'geojson',
//             data: { type: 'FeatureCollection', features: [] }
//         });

//         map_simulation.addLayer({
//             id: 'farm-points',
//             type: 'circle',
//             source: 'farms',
//             paint: {
//                 'circle-radius': 5, 
//                 'circle-color': ['get', 'pointColor']
//             }
//         });

//         map_simulation.addSource('farm-polygons', {
//             type: 'geojson',
//             data: { type: 'FeatureCollection', features: [] } 
//         });

//         map_simulation.addLayer({
//             id: 'farm-polygons-fill',
//             type: 'fill',
//             source: 'farm-polygons',
//             paint: {
//                 'fill-color': ['get', 'color'],
//                 'fill-opacity': 0.4,
//                 'fill-outline-width': [
//                     'interpolate',
//                     ['linear'],
//                     ['zoom'],
//                     0, 0.5,  // Small outline at low zoom
//                     11, 2    // Larger outline at max zoom
//                 ]
//             }
//         });

//         // Updated zoom listener
//         map_simulation.on('zoom', () => {
//             const zoom = map_simulation.getZoom();
//             const zoomFactor = Math.pow(2, (9 - zoom)) * 0.5; // Reverse scaling
//             const currentData = map_simulation.getSource('farm-polygons')._data;
            
//             const scaledFeatures = currentData.features.map(feature => {
//                 return scalePolygon(feature, 1, zoomFactor);
//             });

//             map_simulation.getSource('farm-polygons').setData({
//                 type: 'FeatureCollection',
//                 features: scaledFeatures
//             });
//         });


//         // function rgbToHsl(r, g, b) {
//         //     r /= 255; g /= 255; b /= 255;
//         //     const max = Math.max(r, g, b), min = Math.min(r, g, b);
//         //     let h, s, l = (max + min) / 2;
//         //     if (max === min) {
//         //         h = s = 0; // Achromatic
//         //     } else {
//         //         const d = max - min;
//         //         s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
//         //         switch (max) {
//         //             case r: h = (g - b) / d + (g < b ? 6 : 0); break;
//         //             case g: h = (b - r) / d + 2; break;
//         //             case b: h = (r - g) / d + 4; break;
//         //         }
//         //         h /= 6;
//         //     }
//         //     return [h, s, l];
//         // }
        
//         // Helper function to convert HSL back to RGB
//         // function hslToRgb(h, s, l) {
//         //     let r, g, b;
//         //     if (s === 0) {
//         //         r = g = b = l; // Achromatic
//         //     } else {
//         //         const hue2rgb = (p, q, t) => {
//         //             if (t < 0) t += 1;
//         //             if (t > 1) t -= 1;
//         //             if (t < 1 / 6) return p + (q - p) * 6 * t;
//         //             if (t < 1 / 2) return q;
//         //             if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
//         //             return p;
//         //         };
//         //         const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
//         //         const p = 2 * l - q;
//         //         r = hue2rgb(p, q, h + 1 / 3);
//         //         g = hue2rgb(p, q, h);
//         //         b = hue2rgb(p, q, h - 1 / 3);
//         //     }
//         //     return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
//         // }
        

//         // Convert RGB to HSL
// // function rgbToHsl(r, g, b) {
// //     r /= 255; g /= 255; b /= 255;
// //     const max = Math.max(r, g, b), min = Math.min(r, g, b);
// //     let h, s, l = (max + min) / 2;
// //     if (max === min) {
// //         h = s = 0; // Achromatic
// //     } else {
// //         const d = max - min;
// //         s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
// //         switch (max) {
// //             case r: h = (g - b) / d + (g < b ? 6 : 0); break;
// //             case g: h = (b - r) / d + 2; break;
// //             case b: h = (r - g) / d + 4; break;
// //         }
// //         h /= 6;
// //     }
// //     return [h, s, l];
// // }

// // // Convert HSL back to RGB
// // function hslToRgb(h, s, l) {
// //     let r, g, b;
// //     if (s === 0) {
// //         r = g = b = l; // Achromatic
// //     } else {
// //         const hue2rgb = (p, q, t) => {
// //             if (t < 0) t += 1;
// //             if (t > 1) t -= 1;
// //             if (t < 1 / 6) return p + (q - p) * 6 * t;
// //             if (t < 1 / 2) return q;
// //             if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
// //             return p;
// //         };
// //         const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
// //         const p = 2 * l - q;
// //         r = hue2rgb(p, q, h + 1 / 3);
// //         g = hue2rgb(p, q, h);
// //         b = hue2rgb(p, q, h - 1 / 3);
// //     }
// //     return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
// // }

// // Interpolate between two colors using HSL
// // function interpolateColor(progress, colors) {
// //     const [start, end] = colors.map(c => rgbToHsl(...c));
// //     const h = start[0] + progress * (end[0] - start[0]);
// //     const s = start[1] + progress * (end[1] - start[1]);
// //     const l = start[2] + progress * (end[2] - start[2]);
// //     return hslToRgb(h, s, l);
// // }


// // function interpolateColor(progress, colors) {
// //     const [start, end] = colors;
// //     const r = Math.round(start[0] + (end[0] - start[0]) * progress);
// //     const g = Math.round(start[1] + (end[1] - start[1]) * progress);
// //     const b = Math.round(start[2] + (end[2] - start[2]) * progress);
// //     return [r, g, b];
// // }

//         // // Helper function to interpolate colors
//         function interpolateColor(progress, colors) {
//             const [start, end] = colors;
//             const r = Math.round(start[0] + (end[0] - start[0]) * progress);
//             const g = Math.round(start[1] + (end[1] - start[1]) * progress);
//             const b = Math.round(start[2] + (end[2] - start[2]) * progress);
//             return `rgb(${r}, ${g}, ${b})`;
//         }

//         // Helper function to interpolate size (base radius for polygons)
//         function interpolateSize(progress, minSize, maxSize) {
//             return minSize + (maxSize - minSize) * progress;
//         }


// function getFarmState(farm) {
//     const now = new Date();
//     const sevenDays = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds

//     const latestFarmerReport = farm.farmer_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
//     const latestTechnicianReport = farm.technician_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

//     if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
//         const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
//         const progress = Math.min(timeSinceTreatment / sevenDays, 1);
//         return { isTreated: true, progress };
//     } else if (latestFarmerReport) {
//         const timeSinceReport = now - new Date(latestFarmerReport.created_at);
//         const progress = Math.min(timeSinceReport / sevenDays, 1);
//         return { isTreated: false, progress };
//     } else {
//         return { isTreated: false, progress: 0 };
//     }
// }

// // function getFarmState(farm) {
// //     const now = new Date();
// //     const sevenDays = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds

// //     // Get the latest reports
// //     const latestFarmerReport = farm.farmer_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
// //     const latestTechnicianReport = farm.technician_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

// //     if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
// //         const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
// //         if (timeSinceTreatment > sevenDays) {
// //             // Farm treated over 7 days ago: fully healthy
// //             return { isTreated: true, progress: 1 };
// //         } else {
// //             // Farm still in treatment process
// //             const progress = Math.min(timeSinceTreatment / sevenDays, 1);
// //             return { isTreated: true, progress };
// //         }
// //     } else if (latestFarmerReport) {
// //         // Untreated farm based on farmer report
// //         const timeSinceReport = now - new Date(latestFarmerReport.created_at);
// //         const progress = Math.min(timeSinceReport / sevenDays, 1);
// //         return { isTreated: false, progress };
// //     } else {
// //         // No reports
// //         return { isTreated: false, progress: 0 };
// //     }
// // }



//         // Function to update farm data with zoom-dependent size
//         function updateFarmData(selectedDisease) {
//             const currentDate = new Date();
//             const dayIndex = currentDate.getDay();
//             const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
//             const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];

//             let filteredFarms = selectedDisease === 'All'
//                 ? simulate
//                 : simulate.filter(farm =>
//                     farm.technician_reports.some(report => report.diagnosis_disease === selectedDisease) ||
//                     farm.farmer_reports.some(report => report.disease === selectedDisease)
//                   );

//             const farmFeatures = filteredFarms.map(farm => {
//                 const longitude = parseFloat(farm.longitude);
//                 const latitude = parseFloat(farm.latitude);
//                 if (isNaN(longitude) || isNaN(latitude)) {
//                     console.warn(`Invalid coordinates for farm ${farm.id}: ${farm.longitude}, ${farm.latitude}`);
//                     return null;
//                 }

//                 const { isTreated, progress } = getFarmState(farm);
//                 let pointColor;

//                 if (farm.farmer_reports.length === 0 && farm.technician_reports.length === 0) {
//                     pointColor = '#00FF00';
//                 } else if (isTreated) {
//                     pointColor = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
//                 } else {
//                     pointColor = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
//                 }

//                 return {
//                     type: 'Feature',
//                     geometry: { type: 'Point', coordinates: [longitude, latitude] },
//                     properties: {
//                         municipal: farm.municipal,
//                         disease: farm.technician_reports[0]?.diagnosis_disease || farm.farmer_reports[0]?.disease || 'Unknown',
//                         farmId: farm.id,
//                         pointColor
//                     }
//                 };
//             }).filter(feature => feature !== null);

//             const farmPolygons = farmFeatures.map(farm => {
//                 // Existing logic...
//                 const center = farm.geometry.coordinates;
//                 const originalFarm = simulate.find(f => f.id === farm.properties.farmId);
//                 const { isTreated, progress } = getFarmState(originalFarm);
//                 if (originalFarm.farmer_reports.length === 0 && originalFarm.technician_reports.length === 0) {
//                     return null;
//                 }
//                 const minRadius = 0.01;
//                 const maxRadius = 0.02;
//                 let baseRadius, color;
//                 if (isTreated) {
//                     baseRadius = interpolateSize(1 - progress, 0, maxRadius);
//                     color = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
//                 } else {
//                     baseRadius = interpolateSize(progress, minRadius, maxRadius);
//                     color = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
//                 }
//                 const zoom = map_simulation.getZoom();
//                 const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5; // Apply reverse scaling initially
//                 const polygon = simulateGenerateRandomPolygon(center, 9, baseRadius);
//                 return {
//                     ...scalePolygon(polygon, 1, zoomFactor),
//                     properties: {
//                         municipal: farm.properties.municipal,
//                         disease: farm.properties.disease,
//                         farmId: farm.properties.farmId,
//                         center,
//                         color,
//                         baseRadius
//                     }
//                 };
//             }).filter(p => p !== null && p.properties.color);

//             map_simulation.getSource('farms').setData({
//                 type: 'FeatureCollection',
//                 features: farmFeatures
//             });

//             map_simulation.getSource('farm-polygons').setData({
//                 type: 'FeatureCollection',
//                 features: farmPolygons
//             });

//             return { farmPolygons, orderedDays };
//         }


//         function animatePolygons(farmPolygons) {
//             const duration = 7000; // 7 seconds for the animation
//             const startTime = Date.now();
        
//             function step() {
//                 const elapsed = Date.now() - startTime;
//                 const animationProgress = Math.min(elapsed / duration, 1);
        
//                 const updatedPolygons = farmPolygons.map(polygon => {
//                     const farm = simulate.find(f => f.id === polygon.properties.farmId);
//                     const { isTreated, progress: farmProgress } = getFarmState(farm);
        
//                     let color;
//                     if (isTreated) {
//                         if (farmProgress >= 1) {
//                             color = [0, 255, 0]; // Green for fully treated farms
//                         } else {
//                             // Red to green transition
//                             color = interpolateColor(farmProgress, [[255, 0, 0], [0, 255, 0]]);
//                         }
//                     } else {
//                         // Yellow to red transition
//                         color = interpolateColor(farmProgress, [[255, 255, 0], [255, 0, 0]]);
//                     }
        
//                     polygon.properties.color = `rgb(${color[0]}, ${color[1]}, ${color[2]})`;
//                     return polygon;
//                 });
        
//                 map_simulation.getSource('farm-polygons').setData({
//                     type: 'FeatureCollection',
//                     features: updatedPolygons
//                 });
        
//                 if (animationProgress < 1) {
//                     requestAnimationFrame(step);
//                 }
//             }
//             requestAnimationFrame(step);
//         }


// // function animatePolygons(farmPolygons) {
// //     let isAnimating = false;
// //     const duration = 7000; // 7 seconds
// //     const maxScale = 2;

// //     if (isAnimating) return;
// //     isAnimating = true;
// //     const startTime = Date.now();
// //     let currentPolygons = farmPolygons.map(p => ({ ...p }));

// //     function step() {
// //         const elapsedTime = Date.now() - startTime;
// //         const animationProgress = Math.min(elapsedTime / duration, 1);

// //         currentPolygons = farmPolygons.map(polygon => {
// //             const farm = simulate.find(f => f.id === polygon.properties.farmId);
// //             const { isTreated, progress: farmProgress } = getFarmState(farm);

// //             if (isTreated && farmProgress === 1) {
// //                 // Fully treated farm (over 7 days): stay green, no animation
// //                 polygon.properties.color = 'rgb(0, 255, 0)';
// //                 return polygon;
// //             }

// //             let scale, color;
// //             if (isTreated) {
// //                 // Treated farm in progress
// //                 scale = 1 - animationProgress; // Shrink
// //                 const rgb = interpolateColor(farmProgress, [[255, 0, 0], [0, 255, 0]]);
// //                 color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
// //             } else {
// //                 // Untreated farm
// //                 scale = 1 + animationProgress * (maxScale - 1); // Grow
// //                 const rgb = interpolateColor(farmProgress, [[255, 255, 0], [255, 0, 0]]);
// //                 color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
// //             }

// //             polygon.properties.color = color;
// //             return scale > 0 ? scalePolygon(polygon, scale, /* zoomFactor */) : null;
// //         }).filter(p => p !== null);

// //         map_simulation.getSource('farm-polygons').setData({
// //             type: 'FeatureCollection',
// //             features: currentPolygons
// //         });

// //         if (animationProgress < 1) {
// //             requestAnimationFrame(step);
// //         } else {
// //             isAnimating = false;
// //         }
// //     }
// //     requestAnimationFrame(step);
// // }

       

//         const treatments = {
//             'Stem Bleeding': `
//                 <ul>
//                     <li>Carve the affected tissue and then burn it.<br>(Pag-ukit ng apektadong tissue at pagkatapos ay sunugin ito.)</li>
//                     <li>Spray using a copper-based fungicide (captan).<br>(Pagspray gamit ang copper-based na fungicide (captan).)</li>
//                     <li>Paint the treated wounds with coal tar.<br>(Pagpipinta ng ginamot na sugat gamit ang coal tar.)</li>
//                     <li>Ensure adequate drainage to prevent changes in soil moisture.<br>(Ang sapat na drainage upang maiwasan ang pagbabago sa moisture ng lupa.)</li>
//                 </ul>
//             `,
//             'Lethal Yellowing': `
//                 <ul>
//                     <li>Remove and dispose of infected trees.<br>(Pag-alis at pagkakalat ng infected na puno.)</li>
//                     <li>Avoid overcrowding of coconut trees to prevent rapid disease spread.<br>(Iwasan ang overcrowding ng mga puno ng niyog upang maiwasan ang mabilis na pagkalat ng sakit.)</li>
//                     <li>Regularly inspect the trees to promptly detect early symptoms of Lethal Yellowing.<br>(Regular na inspeksyunin ang mga puno upang madaliang matukoy ang mga unang sintomas ng Lethal Yellowing.)</li>
//                     <li>Improve overall tree health.<br>(Pagpapabuti ng kalusugan ng puno.)</li>
//                     <li>Consult local agronomists or coconut experts for proper measures and technical support in managing Lethal Yellowing.<br>(Makipag-ugnayan sa mga lokal na agronomist o mga eksperto sa niyog para sa mga tamang hakbang at teknikal na suporta sa pamamahala ng Lethal Yellowing.)</li>
//                 </ul>
//             `,
//             'Leaf Spot': `
//                 <ul>
//                     <li>Regularly remove affected leaves and dispose of them properly to prevent disease spread.<br>(Regular na alisin ang mga apektadong dahon at itapon ang mga ito nang maayos upang maiwasan ang pagkalat ng sakit.)</li>
//                     <li>Do not dispose of them on the plantation soil to avoid re-infection by fungi.<br>(Huwag itong itapon sa lupa ng taniman upang hindi maging sanhi ng muling pagkalat ng fungi.)</li>
//                     <li>Spray with fungicides such as copper-based fungicides.<br>(Pag-spray ng mga fungicide tulad ng copper-based fungicides.)</li>
//                     <li>Ensure that the coconut trees receive proper nutrition and care to boost their resistance against diseases.<br>(Siguraduhin na ang mga puno ng niyog ay may tamang nutrisyon at sapat na pag-aalaga upang mapalakas ang kanilang resistensya laban sa mga sakit.)</li>
//                     <li>Clean the area around the coconut trees from fallen leaves, branches, and other plant debris that may harbor fungal spores.<br>(Linisin ang paligid ng puno ng niyog mula sa mga natirang dahon, sanga, at iba pang plant debris na maaaring maglaman ng mga spora ng fungus.)</li>
//                     <li>Regularly inspect the trees to detect early symptoms of the disease.<br>(Regular na inspeksyunin ang mga puno upang makita ang mga maagang sintomas ng sakit.)</li>
//                 </ul>
//             `
//         };

//         const treatmentText = document.getElementById('treatment-text');
//         treatmentText.innerHTML = `<p class="mt-5 text-center">Please select a disease to view treatment recommendations.</p>`;
//         const { farmPolygons: initialFarmPolygons } = updateFarmData('All');
//         let intervalId;
//         function interval(simulate) {
//             if (intervalId) {
//                 clearInterval(intervalId);
//             }
//             const updateFunction = () => updateFarmData(document.getElementById('disease').value);
//             if (simulate === 'simulate') {
//                 setTimeout(() => {
//                     updateFunction();
//                     intervalId = setInterval(updateFunction, 1000);
//                 }, 7000);
//             } else {
//                 intervalId = setInterval(updateFunction, 1000);
//             }
//         }
  
//         document.getElementById('disease').addEventListener('change', (event) => {
//             const selectedDisease = event.target.value;
//             treatmentText.innerHTML = treatments[selectedDisease] || `<p class="mt-5 text-center">Please select a disease to view treatment recommendations.</p>`;
//             const { farmPolygons } = updateFarmData(selectedDisease);
//             if (farmPolygons.length > 0) {
//                 const coordinates = farmPolygons.map(p => p.properties.center);
//                 const bounds = coordinates.reduce((bounds, coord) => bounds.extend(coord), new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
//                 map_simulation.fitBounds(bounds, { padding: 50 });
//             } else if (selectedDisease === 'All') {
//                 map_simulation.setZoom(9);
//             }
//         });

//         document.getElementById('silumate-button').addEventListener('click', () => {
//             const selectedDisease = document.getElementById('disease').value;
//             const { farmPolygons } = selectedDisease === 'All'
//                 ? { farmPolygons: initialFarmPolygons }
//                 : updateFarmData(selectedDisease);
//             animatePolygons(farmPolygons);
//             const progressBar = document.getElementById('week-progress');
//             function startProgress() {
//                 progressBar.style.transition = 'none'; 
//                 progressBar.style.width = '0%';

//                 void progressBar.offsetWidth;
//                 $('#silumate-button').text('Simulating . .').prop('disabled', true);
//                 // setTimeout(() => {
//                     progressBar.style.transition = 'width 7s linear';
//                     progressBar.style.width = '100%';
//                 // }, 50); // Small delay to ensure transition applies
//             }
//             // Initial start
//             startProgress();
        
//             progressBar.addEventListener('transitionend', function handler(e) {
//                 if (e.propertyName === 'width' && progressBar.style.width === '100%') {
//                     // progressBar.style.transition = 'none'; 
//                     progressBar.style.transition = 'width 1s linear';
//                     progressBar.style.width = '0%';
//                     $('#silumate-button').text('Simulate').prop('disabled', false);
//                 }
//             }, { once: false }); 
        
//             interval('simulate');
//         });
//         interval();
//     });
// }



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

async function mapSimulation(simulate, mapboxgl) {
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
            // filter: ['==', '$type', 'Polygon'],
            filter: ['==', ['geometry-type'], 'Polygon'],
            paint: {
                'line-color': '#FFD700',
                'line-width': 3,
                'line-dasharray': [2, 2],
            }
        });

        // map_simulation.addLayer({
        //     id: 'laguna-boundary',
        //     type: 'fill',
        //     source: 'municipal',
        //     // filter: ['==', '$type', 'Polygon'],
        //     filter: ['==', ['geometry-type'], 'Polygon'],
        //     paint: {
        //         'fill-color': 'darkblue',
        //         'fill-opacity': 0.3
        //     }
        // });

        map_simulation.addSource('farms', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features: [] }
        });

        map_simulation.addLayer({
            id: 'farm-points',
            type: 'circle',
            source: 'farms',
            paint: {
                'circle-radius': 5, 
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
                'fill-opacity': 0.4,
                // 'fill-outline-width': [
                //     'interpolate',
                //     ['linear'],
                //     ['zoom'],
                //     0, 0.5,  // Small outline at low zoom
                //     11, 2    // Larger outline at max zoom
                // ]
            }
        });

        // Updated zoom listener
        map_simulation.on('zoom', () => {
            const zoom = map_simulation.getZoom();
            const zoomFactor = Math.pow(2, (9 - zoom)) * 0.5; // Reverse scaling
            const currentData = map_simulation.getSource('farm-polygons')._data;
            
            const scaledFeatures = currentData.features.map(feature => {
                return scalePolygon(feature, 1, zoomFactor);
            });

            map_simulation.getSource('farm-polygons').setData({
                type: 'FeatureCollection',
                features: scaledFeatures
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

// // Interpolate between two colors using HSL
function interpolateColor(progress, colors) {
    const [start, end] = colors.map(c => rgbToHsl(...c));
    const h = start[0] + progress * (end[0] - start[0]);
    const s = start[1] + progress * (end[1] - start[1]);
    const l = start[2] + progress * (end[2] - start[2]);
    return hslToRgb(h, s, l);
}

        // Helper function to interpolate colors
        // function interpolateColor(progress, colors) {
        //     const [start, end] = colors;
        //     const r = Math.round(start[0] + (end[0] - start[0]) * progress);  
        //     const g = Math.round(start[1] + (end[1] - start[1]) * progress);
        //     const b = Math.round(start[2] + (end[2] - start[2]) * progress);
        //     return `rgb(${r}, ${g}, ${b})`;
        // }

        // Helper function to interpolate size (base radius for polygons)
        function interpolateSize(progress, minSize, maxSize) {
            return minSize + (maxSize - minSize) * progress;
        }

        // Function to determine farm state and progress
        // function getFarmState(farm) {
        //     const now = new Date();
        //     const sevenDays = 7 * 24 * 60 * 60 * 1000;

        //     const latestFarmerReport = farm.farmer_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
        //     const latestTechnicianReport = farm.technician_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
        //     let isTreated = false;
        //     let progress = 0;

        //     if (latestFarmerReport && (!latestTechnicianReport || new Date(latestFarmerReport.created_at) > new Date(latestTechnicianReport.created_at))) {
        //         const timeElapsed = now - new Date(latestFarmerReport.created_at);
        //         progress = Math.min(timeElapsed / sevenDays, 1);
        //     } if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
        //         isTreated = true;
        //         const timeElapsed = now - new Date(latestTechnicianReport.created_at);
        //         progress = Math.min(timeElapsed / sevenDays, 1);
        //     }

        //     return { isTreated, progress };
        // }

        
// function getFarmState(farm) {
//     const now = new Date();
//     const sevenDays = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds

//     const latestFarmerReport = farm.farmer_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
//     const latestTechnicianReport = farm.technician_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

//     if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
//         const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
//         const progress = Math.min(timeSinceTreatment / sevenDays, 1);
//         return { isTreated: true, progress };
//     } else if (latestFarmerReport) {
//         const timeSinceReport = now - new Date(latestFarmerReport.created_at);
//         const progress = Math.min(timeSinceReport / sevenDays, 1);
//         return { isTreated: false, progress };
//     } else {
//         return { isTreated: false, progress: 0 };
//     }
// }


function getFarmState(farm) {
    const now = new Date();
    const sevenDays = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds

    // Get the latest reports
    const latestFarmerReport = farm.farmer_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
    const latestTechnicianReport = farm.technician_reports.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];

    if (latestTechnicianReport && (!latestFarmerReport || new Date(latestTechnicianReport.created_at) > new Date(latestFarmerReport.created_at))) {
        const timeSinceTreatment = now - new Date(latestTechnicianReport.created_at);
        if (timeSinceTreatment > sevenDays) {
            // Farm treated over 7 days ago: fully healthy
            return { isTreated: true, progress: 1 };
        } else {
            // Farm still in treatment process
            const progress = Math.min(timeSinceTreatment / sevenDays, 1);
            return { isTreated: true, progress };
        }
    } else if (latestFarmerReport) {
        // Untreated farm based on farmer report
        const timeSinceReport = now - new Date(latestFarmerReport.created_at);
        const progress = Math.min(timeSinceReport / sevenDays, 1);
        return { isTreated: false, progress };
    } else {
        // No reports
        return { isTreated: false, progress: 0 };
    }
}



        // Function to update farm data with zoom-dependent size
        // function updateFarmData(selectedDisease) {
        //     const currentDate = new Date();
        //     const dayIndex = currentDate.getDay();
        //     const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        //     const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];

        //     let filteredFarms = selectedDisease === 'All'
        //         ? simulate
        //         : simulate.filter(farm =>
        //             farm.technician_reports.some(report => report.diagnosis_disease === selectedDisease) ||
        //             farm.farmer_reports.some(report => report.disease === selectedDisease)
        //           );

        //     const farmFeatures = filteredFarms.map(farm => {
        //         const longitude = parseFloat(farm.longitude);
        //         const latitude = parseFloat(farm.latitude);
        //         if (isNaN(longitude) || isNaN(latitude)) {
        //             console.warn(`Invalid coordinates for farm ${farm.id}: ${farm.longitude}, ${farm.latitude}`);
        //             return null;
        //         }

        //         const { isTreated, progress } = getFarmState(farm);
        //         let pointColor;

        //         if (farm.farmer_reports.length === 0 && farm.technician_reports.length === 0) {
        //             pointColor = '#00FF00';
        //         } else if (isTreated) {
        //             pointColor = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
        //         } else {
        //             pointColor = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
        //         }

        //         return {
        //             type: 'Feature',
        //             geometry: { type: 'Point', coordinates: [longitude, latitude] },
        //             properties: {
        //                 municipal: farm.municipal,
        //                 disease: farm.technician_reports[0]?.diagnosis_disease || farm.farmer_reports[0]?.disease || 'Unknown',
        //                 farmId: farm.id,
        //                 pointColor
        //             }
        //         };
        //     }).filter(feature => feature !== null);

        //     const farmPolygons = farmFeatures.map(farm => {
        //         // Existing logic...
        //         const center = farm.geometry.coordinates;
        //         const originalFarm = simulate.find(f => f.id === farm.properties.farmId);
        //         const { isTreated, progress } = getFarmState(originalFarm);
        //         if (originalFarm.farmer_reports.length === 0 && originalFarm.technician_reports.length === 0) {
        //             return null;
        //         }
        //         const minRadius = 0.01;
        //         const maxRadius = 0.02;
        //         let baseRadius, color;
        //         if (isTreated) {
        //             baseRadius = interpolateSize(1 - progress, 0, maxRadius);
        //             color = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
        //         } else {
        //             baseRadius = interpolateSize(progress, minRadius, maxRadius);
        //             color = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
        //         }
        //         const zoom = map_simulation.getZoom();
        //         const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5; // Apply reverse scaling initially
        //         const polygon = simulateGenerateRandomPolygon(center, 9, baseRadius);
        //         return {
        //             ...scalePolygon(polygon, 1, zoomFactor),
        //             properties: {
        //                 municipal: farm.properties.municipal,
        //                 disease: farm.properties.disease,
        //                 farmId: farm.properties.farmId,
        //                 center,
        //                 color,
        //                 baseRadius
        //             }
        //         };
        //     }).filter(p => p !== null && p.properties.color);

        //     map_simulation.getSource('farms').setData({
        //         type: 'FeatureCollection',
        //         features: farmFeatures
        //     });

        //     map_simulation.getSource('farm-polygons').setData({
        //         type: 'FeatureCollection',
        //         features: farmPolygons
        //     });

        //     return { farmPolygons, orderedDays };
        // }


        

        // Function to update farm data with zoom-dependent size
        function updateFarmData(selectedDisease) {
            const currentDate = new Date();
            const dayIndex = currentDate.getDay();
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const orderedDays = [...days.slice(dayIndex), ...days.slice(0, dayIndex)];

            let filteredFarms = selectedDisease === 'All'
                ? simulate
                : simulate.filter(farm =>
                    farm.technician_reports.some(report => report.diagnosis_disease === selectedDisease) ||
                    farm.farmer_reports.some(report => report.disease === selectedDisease)
                  );

            const farmFeatures = filteredFarms.map(farm => {
                const longitude = parseFloat(farm.longitude);
                const latitude = parseFloat(farm.latitude);
                if (isNaN(longitude) || isNaN(latitude)) {
                    console.warn(`Invalid coordinates for farm ${farm.id}: ${farm.longitude}, ${farm.latitude}`);
                    return null;
                }

                const { isTreated, progress } = getFarmState(farm);
                let pointColor;

                if (farm.farmer_reports.length === 0 && farm.technician_reports.length === 0) {
                    pointColor = '#00FF00';
                } else if (isTreated) {
                    pointColor = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
                } else {
                    pointColor = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
                }

                return {
                    type: 'Feature',
                    geometry: { type: 'Point', coordinates: [longitude, latitude] },
                    properties: {
                        municipal: farm.municipal,
                        disease: farm.technician_reports[0]?.diagnosis_disease || farm.farmer_reports[0]?.disease || 'Unknown',
                        farmId: farm.id,
                        pointColor
                    }
                };
            }).filter(feature => feature !== null);

            const farmPolygons = farmFeatures.map(farm => {
                // Existing logic...
                const center = farm.geometry.coordinates;
                const originalFarm = simulate.find(f => f.id === farm.properties.farmId);
                const { isTreated, progress } = getFarmState(originalFarm);
                if (originalFarm.farmer_reports.length === 0 && originalFarm.technician_reports.length === 0) { 
                    return null;
                }
                const minRadius = 0.01;
                const maxRadius = 0.02;
                let baseRadius, color;
                if (isTreated) {
                    baseRadius = interpolateSize(1 - progress, 0, maxRadius);
                    color = interpolateColor(progress, [[255, 0, 0], [0, 255, 0]]);
                } else {
                    baseRadius = interpolateSize(progress, minRadius, maxRadius);
                    color = interpolateColor(progress, [[255, 255, 0], [255, 0, 0]]);
                }
                const zoom = map_simulation.getZoom();
                const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5; // Apply reverse scaling initially
                const polygon = simulateGenerateRandomPolygon(center, 9, baseRadius);
                return {
                    ...scalePolygon(polygon, 1, zoomFactor),
                    properties: {
                        municipal: farm.properties.municipal,
                        disease: farm.properties.disease,
                        farmId: farm.properties.farmId,
                        center,
                        color,
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

            return { farmPolygons, orderedDays };
        }

        // function animatePolygons(farmPolygons) {
        //     let isAnimating = false;
        //     const duration = 7000;
        //     const maxScale = 2;
        //     const shrinkColors = [[255, 0, 0], [255, 255, 0], [0, 255, 0]];

        //     if (isAnimating) return;
        //     isAnimating = true;
        //     const startTime = Date.now();
        //     let currentPolygons = farmPolygons.map(p => ({ ...p }));

        //     function step() {
        //         const currentTime = Date.now();
        //         const elapsedTime = currentTime - startTime;
        //         const progress = Math.min(elapsedTime / duration, 1);
        //         const zoom = map_simulation.getZoom();
        //         const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5;

        //         currentPolygons = farmPolygons.map(polygon => {
        //             const farm = simulate.find(f => f.id === polygon.properties.farmId);
        //             const { isTreated } = getFarmState(farm);
        //             let k, color;

        //             if (isTreated) {
        //                 k = 1 - progress;
        //                 color = interpolateColor(progress, [shrinkColors[0], shrinkColors[2]]);
        //             } else {
        //                 k = 1 + progress * (maxScale - 1);
        //                 color = '#FF0000';
        //             }
 
        
        //             polygon.properties.color = color;
        //             const farmFeatures = map_simulation.getSource('farms')._data.features;
        //             const matchingFarm = farmFeatures.find(f =>
        //                 f.geometry.coordinates[0] === polygon.properties.center[0] &&
        //                 f.geometry.coordinates[1] === polygon.properties.center[1]
        //             );
        //             if (matchingFarm) {
        //                 matchingFarm.properties.pointColor = progress >= 1 && isTreated ? '#00FF00' : color;
        //             }

        //             return k > 0 ? scalePolygon(polygon, k, zoomFactor) : null;
        //         }).filter(p => p !== null);

        //         map_simulation.getSource('farm-polygons').setData({
        //             type: 'FeatureCollection',
        //             features: currentPolygons
        //         });
        //         map_simulation.getSource('farms').setData({
        //             type: 'FeatureCollection',
        //             features: map_simulation.getSource('farms')._data.features
        //         });

        //         if (progress < 1) {
        //             requestAnimationFrame(step);
        //         } else {
        //             isAnimating = false;
        //         }
        //     }
        //     requestAnimationFrame(step);
        // }


function animatePolygons(farmPolygons) {
    let isAnimating = false;
    const duration = 7000; // 7 seconds
    const maxScale = 1.5;
    const shrinkColors = [[255, 0, 0], [255, 255, 0], [0, 255, 0]]; // added

    if (isAnimating) return;
    isAnimating = true;
    const startTime = Date.now();
    let currentPolygons = farmPolygons.map(p => ({ ...p }));

    function step() {
        const elapsedTime = Date.now() - startTime;
        const animationProgress = Math.min(elapsedTime / duration, 1);

        // added
        const zoom = map_simulation.getZoom();
        const zoomFactor = Math.pow(2, (9 - zoom)) * 1.5;
        //.

        currentPolygons = farmPolygons.map(polygon => {
            const farm = simulate.find(f => f.id === polygon.properties.farmId);
            const { isTreated, progress: farmProgress } = getFarmState(farm);
            // const { isTreated } = getFarmState(farm);

 

            if (isTreated && farmProgress === 1) {
                // Fully treated farm (over 7 days): stay green, no animation
                polygon.properties.color = 'rgb(0, 255, 0)';
                return polygon;
            }

            let scale, color;
            if (isTreated) {
                // Treated farm in progress
                scale = 1 - animationProgress; // Shrink
                const rgb = interpolateColor(farmProgress + animationProgress, [[255, 0, 0], [0, 255, 0]]);
                // color = interpolateColor(animationProgress, [shrinkColors[0], shrinkColors[2]]); // added
                color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
            } else {
                // Untreated farm
                scale = 1 + animationProgress * (maxScale - 1); // Grow
                const rgb = interpolateColor(farmProgress + animationProgress, [[255, 255, 0], [255, 0, 0]]);
                // color = interpolateColor(animationProgress, [shrinkColors[0], shrinkColors[2]]); // added
                color = `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
            }  

            polygon.properties.color = color;
            // added
            const farmFeatures = map_simulation.getSource('farms')._data.features;
            const matchingFarm = farmFeatures.find(f =>
                f.geometry.coordinates[0] === polygon.properties.center[0] &&
                f.geometry.coordinates[1] === polygon.properties.center[1]
            );
            if (matchingFarm) {
                matchingFarm.properties.pointColor = animationProgress >= 1 && isTreated ? '#00FF00' : color;
            }
            //.
            return scale > 0 ? scalePolygon(polygon, scale, zoomFactor) : null;
            // return scale > 0 ? scalePolygon(polygon, scale, /* zoomFactor */ ) : null;
        }).filter(p => p !== null);
         

        map_simulation.getSource('farm-polygons').setData({
            type: 'FeatureCollection',
            features: currentPolygons
        });
        map_simulation.getSource('farms').setData({
            type: 'FeatureCollection',
            features: map_simulation.getSource('farms')._data.features
        });


        if (animationProgress < 1) {
            requestAnimationFrame(step);
        } else {
            isAnimating = false;
        }
    }
    requestAnimationFrame(step);
}

       


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
        treatmentText.innerHTML = `<p class="mt-5 text-center">Please select a disease to view treatment recommendations.</p>`;
        const { farmPolygons: initialFarmPolygons } = updateFarmData('All');
        let intervalId;
        function interval(simulate) {
            if (intervalId) {
                clearInterval(intervalId);
            }
            const updateFunction = () => updateFarmData(document.getElementById('disease').value);
            if (simulate === 'simulate') {
                setTimeout(() => {
                    updateFunction();
                    intervalId = setInterval(updateFunction, 1000);
                }, 7000);
            } else {
                intervalId = setInterval(updateFunction, 1000);
            }
        }
  
        document.getElementById('disease').addEventListener('change', (event) => {
            const selectedDisease = event.target.value;
            treatmentText.innerHTML = treatments[selectedDisease] || `<p class="mt-5 text-center">Please select a disease to view treatment recommendations.</p>`;
            const { farmPolygons } = updateFarmData(selectedDisease);
            if (farmPolygons.length > 0 && (selectedDisease !== 'All')) {
                const coordinates = farmPolygons.map(p => p.properties.center);
                const bounds = coordinates.reduce((bounds, coord) => bounds.extend(coord), new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                map_simulation.fitBounds(bounds, { padding: 50 });
            } else if (selectedDisease === 'All') {
                map_simulation.setZoom(0);
            }
        });

        document.getElementById('silumate-button').addEventListener('click', () => {
            const selectedDisease = document.getElementById('disease').value;
            const { farmPolygons } = selectedDisease === 'All'
                ? { farmPolygons: initialFarmPolygons }
                : updateFarmData(selectedDisease);
            animatePolygons(farmPolygons);
            const progressBar = document.getElementById('week-progress');
            function startProgress() {
                progressBar.style.transition = 'none'; 
                progressBar.style.width = '0%';

                void progressBar.offsetWidth;
                $('#silumate-button').text('Simulating . .').prop('disabled', true);
                // setTimeout(() => {
                    progressBar.style.transition = 'width 7s linear';
                    progressBar.style.width = '100%';
                // }, 50); // Small delay to ensure transition applies
            }
            // Initial start
            startProgress();
        
            progressBar.addEventListener('transitionend', function handler(e) {
                if (e.propertyName === 'width' && progressBar.style.width === '100%') {
                    // progressBar.style.transition = 'none'; 
                    progressBar.style.transition = 'width 1s linear';
                    progressBar.style.width = '0%';
                    $('#silumate-button').text('Simulate').prop('disabled', false);
                }
            }, { once: false }); 
        
            interval('simulate');
        });
        interval();
    });
}
