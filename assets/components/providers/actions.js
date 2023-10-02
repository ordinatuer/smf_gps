import axios from "axios"

export function getFiles() {
    return axios.get('/providers_files')
}

export function getAddressList(fileId) {
    return axios.get('/providers_address_list', {params: {
        file_id: fileId
    }})
}