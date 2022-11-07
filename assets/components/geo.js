import axios from 'axios'

export function getPointData(point_id) {
    return axios.get('/gpsmap-point', {params: {
        'point_id': point_id
    }})
}

export function getBoundsData(bounds) {
    return axios.post('/gpsmap-bounds', {
        'bounds': bounds
    })
}