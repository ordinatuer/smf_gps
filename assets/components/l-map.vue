<template>
    <h4>{{title}}</h4>
    <span>Всего точек {{countPoints}}</span>
    <div id="gpsMap"></div>
</template>

<script>
import 'leaflet'
import 'leaflet.markercluster'
import axios from 'axios'

export default {
    data() {
        return {
            title: 'Leaflet map in vue component',
            lat: 59.93863,
            lng: 30.31413,
            zoom: 13,
            map: null,
            countPoints: 0
        }
    },
    mounted() {
        this.map = L.map('gpsMap').setView([this.lat, this.lng], this.zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(this.map)

        this.map.on('moveend', () => {
            this.getMapBounds()
        })

        this.getMapBounds()
    },
    methods: {
        getMapBounds() {
            let bounds = this.map.getBounds()
            
            axios.post('/gpsmap-bounds', {
                'bounds': bounds
            })
            .then((response) => {
                let nPoints = response.data.n
                this.countPoints = nPoints

                if (!response.data.data) {
                    this.title = "Counted"
                } else if (response.data.cluster) {
                    this.title = "Show Clusters"
                } else {
                    this.title = "Show Points"
                }
            })
            .catch(function(error) {
                console.log(error)
            })
        }
    }
}
</script>
