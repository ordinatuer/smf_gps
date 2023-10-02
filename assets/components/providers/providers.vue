<template>
    <div class="w75" id="providersMapBlock"></div>
    <div class="w25">
        <providers-list v-on:addressList="showPoints" />
    </div>
    <div class="clearfix"></div>
</template>

<script>
import 'leaflet'
import providersList from './providersList.vue'

export default {
    components: {
        providersList
    },
    data() {
        return {
            lat: 59.93863,
            lng: 30.31413,
            zoom: 10,
            maxZoom: 19,
            map: null,
            mapId: 'providersMapBlock',
            markers: null
        }
    },
    mounted() {
        this.map = new L.map(this.mapId).setView([this.lat, this.lng], this.zoom)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: this.maxZoom,
            attribution: '© OpenStreetMap'
        }).addTo(this.map)

        this.markers = L.layerGroup()
    },
    methods: {
        showPoints(data) {
            // const icon = L.divIcon({
            //     className: 'c-red',
            //     html: '<b>🚰</b>'
            // })
            // const myIcon = L.divIcon({className: 'div-icon'});
            //.bindTooltip(el.address.split(", ").slice(2).join(", "))

            let markers = []
            data.forEach(el => {
                const marker = L.marker([el.lat, el.lon], {title: el.address})
                // .bindTooltip(el.address)
                markers.push(marker)
            })

            let markersLayer = L.featureGroup(markers)

            this.markers.clearLayers()
            this.markers.addLayer(markersLayer)
            this.markers.addTo(this.map)

            this.map.fitBounds(markersLayer.getBounds())
        }
    }
}
</script>

<style scoped>
.w75 {
    width: 75%;
    height: 500px;
    float: left;
}
.w25 {
    width: 25%;
    height: 500px;
    float: right;
}
</style>