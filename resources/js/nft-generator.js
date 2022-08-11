window.$ = require('jquery')
// https://github.com/SortableJS/Vue.Draggable
import draggable from 'vuedraggable'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

import { io } from "socket.io-client";
const socket = io("https://nft-generator.tnwebsolutions.nl", {
    withCredentials: true
});

export default {
    components: {
        draggable,
    },
    data() {
        return {
            generator: {
                total: 10,
                base: 'NFT #',
                description: '',
                layers: [],
                files: false,
                currentLayer: 0,
                uploadClasses: [],
                loader: {
                    progress: 0,
                    state: 'idle'
                }
            }
        }
    },
    mounted: function() {
        socket.on('generate-nfts', (response) => {
            this.handleSocketResponse(response)
            // document.getElementById('progress').style.width = msg;
        });
    },
    methods: {
        handleSocketResponse: function(response) {
            if (response.state == undefined) {
                // console.log(response)
            } else {
                this.generator.loader.state = response.state
                this.generator.loader.progress = response.value
            }
        },
        dragEnterUploader: function() {
            this.generator.uploadClasses = ['border-mintpad-300']
        },
        dragLeaveUploader: function() {
            this.generator.uploadClasses = []
        },
        toggleLayer: function(layer) {
            this.generator.currentLayer = layer
        },
        onDragEnd: function(data) {
            this.drag = false
            this.generator.currentLayer = data.newIndex
        },
        onDragStart: function(data) {
            this.drag = true
            this.generator.currentLayer = data.oldIndex
        },
        generateCollection: async function(e) {
            this.setButtonLoader(e)

            await this.uploadTraitJSON()
            await this.uploadTraitImages(this.generator.files)

            socket.emit('generate-nfts', {userID: 1, prefix: this.generator.base, description: this.generator.description, total: parseInt(this.generator.total)});
            this.resetButtonLoader()
        },
        uploadTraitJSON: function() {
            return axios.post('/generator/create', {layers: JSON.stringify(this.generator.layers)}).then((response) => {
                var data = response.data
                // console.log(data)
            })
        },
        uploadTraits: async function(event) {
            var files = event.target.files
            this.generator.files = files
            var traits = {}
            for (var i = 0; i < files.length; i++) {
                var file = files[i]
                var options = this.parseTraitFile(file)

                if (!options) continue
                if (traits[options.folder] == undefined) traits[options.folder] = []
                traits[options.folder].push(options)
            }
            this.createLayerList(traits)
        },
        uploadTraitImages: function(files) {
            var formData = new FormData()
            for (var i = 0; i < files.length; i++) {
                var file = files[i]
                if (file.type == 'image/png') {
                    formData.append('files[' + i + ']', file)
                }
            }

            return axios.post('/generator/upload', formData).then(function(response) {
                // console.log(response.data)
            })

        },
        parseTraitFile: function(file) {
            var structure = file.webkitRelativePath.split('/')
            
            // Validate image
            if (
                file.type !== 'image/png' ||
                structure.length != 3
            ) {
                return false
            }

            // Strip root folder
            if (structure.length == 3) {
                structure.shift()
            }

            // Set output
            return {
                folder: structure[0],
                image: structure.join('/'),
                weight: 1,
                value: structure[1].replace(/\.[^/.]+$/, "")
            }
        },
        createLayerList: function(traits) {
            var output = []
            Object.entries(traits).forEach(trait => {
                var options = trait[1]
                options.sort((a, b) => {
                    let fa = a.value.toLowerCase(),
                        fb = b.value.toLowerCase();

                    if (fa < fb) {
                        return -1;
                    }
                    if (fa > fb) {
                        return 1;
                    }
                    return 0;
                });
                output.push({
                    'type': trait[0],
                    'options': options
                })
            })
            this.generator.layers = output
        },
        // createUploadChunks: function(files, chunkSize) {
        //     var output = []
        //     for (let i = 0; i < files.length; i += chunkSize) {
        //         const chunk = files.slice(i, i + chunkSize)
        //         output.push(chunk)
        //     }
        //     return output
        // },
        // prepareCollectionForUpload: function(files) {
        //     var formData = new FormData() 
        //     for (var i = 0; i < files.length; i++) {
        //         var file = files[i]
        //         formData.append('files[' + i + ']', file)
        //     }
        //     return formData
        // }, 
    }
}