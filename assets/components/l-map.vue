<template>
    <h4>{{title}}</h4>
    <span>Всего точек {{countPoints}}</span>
    <div id="gpsMap"></div>
</template>

<script>
import 'leaflet'
import 'leaflet.markercluster'
import {getPointData, getBoundsData} from './geo.js'

export default {
    data() {
        return {
            title: 'Leaflet map in vue component',
            lat: 59.93863,
            lng: 30.31413,
            zoom: 16,
            map: null,
            countPoints: 0,
            markers: false
        }
    },
    mounted() {
        this.map = L.map('gpsMap').setView([this.lat, this.lng], this.zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(this.map)

        this.markers = L.markerClusterGroup()
        this.markers.on('click', (a) => {
            const point = getPointData(a.layer._pid)

            point
                .then(result => console.log(result.data))
                .catch(error => console.log(error))
        })

        this.map.on('moveend', () => {
            this.getMapBounds()
        })

        this.getMapBounds()
    },
    methods: {
        getMapBounds() {
            let bounds = this.map.getBounds()
            const boundsData = getBoundsData(bounds)
            
            boundsData
                .then((response) => {
                    let nPoints = response.data.n
                    this.countPoints = nPoints

                    if (!response.data.data) {
                        this.title = "Counted"
                    } else if (response.data.cluster) {
                        this.title = "Show Clusters"
                    } else {
                        this.title = "Show Points"

                        this._renderMarkers(response.data.data)
                    }
                })
                .catch(function(error) {
                    console.log(error)
                })
        },
        _renderMarkers(data) {
            let markerList = []

            data.forEach((point) => {
                const marker = L.marker([point.location.latitude, point.location.longitude]).bindPopup('Point ID:' + point.id)
                marker._pid = point.id

                markerList.push(marker)
            })

            this.markers.clearLayers()
            this.markers.addLayers(markerList)
            this.map.addLayer(this.markers)
        }
    }
}
</script>
