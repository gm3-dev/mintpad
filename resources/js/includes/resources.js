window.$ = require('jquery')

// Includes
import { initSentry, resportError } from './sentry'

// Config
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

export default {
    data() {
        return {
            resources: {}
        }
    },
    methods: {
        setResource: function(name) {
            if (!this.resources[name]) {
                this.resources[name] = {classes: [], loading: false}
            }
        },
        loadingResource: function(name) {
            return this.resources[name] && this.resources[name].loading === true
        },
        uploadResource: async function(name, data) {
            this.setResource(name)
            this.resources[name].loading = true

            return axios.post('/resources/'+this.collectionID+'/upload', data);
        },
        deleteResource: async function(name) {
            var data = {data: {name: name}}
            return axios.delete('/resources/'+this.collectionID+'/delete', data);
        },
        dragEnterUploadResource: function(name) {
            this.setResource(name)
            this.resources[name].classes = ['border-mintpad-300']
        },
        dragLeaveUploadResource: function(name) {
            this.setResource(name)
            this.resources[name].classes = []
        },
    }
}