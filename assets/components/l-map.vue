<template>
    <h4>{{title}}</h4>
    <span>Всего точек {{countPoints}}</span>
    <div id="LMapInner"></div>
</template>

<script>
import 'leaflet'
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
    },
    methods: {
        getMapBounds() {
            let bounds = this.map.getBounds()
            
            axios.post('/gpsmap-bounds', {
                'side': 'client',
                'bounds': bounds
            })
            .then((response) => {
                this.countPoints = response.data.queryResult
            })
            .catch(function(error) {
                console.log(error)
            })
            .then(function() {
                console.log('Axios query sended')
                console.log([
                    bounds._northEast.lat,
                    bounds._northEast.lng,
                    bounds._southWest.lat,
                    bounds._southWest.lng
                ])
            })
        }
    }
}
</script>
