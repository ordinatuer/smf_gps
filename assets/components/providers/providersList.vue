<template>
    <ul class="files-list">
        <li v-for="(file, i) in files">
            <span v-on:click="showFile(file.id)">{{ file.description}}</span>
            <i><a v-bind:href="getFileHref(file.id)"> &dHar; </a></i>
        </li>
    </ul>
</template>

<script>
import {getFiles, getAddressList} from './actions.js'

export default {
    data() {
        return {
            files: [],
            currentFile: 0,
            list: []
        }
    },
    mounted() {
        let files = getFiles()

        files.then((response) => {
            this.files = response.data.files
        }).catch(function(error) {
            console.log(error)
        });
    },
    methods: {
        showFile(fileId) {
            this.currentFile = fileId

            let addressList = getAddressList(this.currentFile)
            addressList.then((response) => {
                this.list = response.data.list
                this.$emit('addressList', this.list)
            }).catch(function(error) {
                console.log(error)
            })
        },
        getFileHref(fileId) {
            return '/providers_get_file?file_id=' + fileId
        }
    },
}
</script>

<style scoped>
.files-list {
    list-style-type: none;
}
.files-list li {
    cursor: pointer;
    margin-bottom: 2px;
}
.files-list li span {
    text-decoration: underline;
}
.files-list li span:hover {
    color: #2e1f1f;
}
.files-list li i:hover {
    color: red;
}
</style>