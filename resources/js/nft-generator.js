window.$ = require('jquery')
// https://github.com/SortableJS/Vue.Draggable
import draggable from 'vuedraggable'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

// import { io } from "socket.io-client";
// const socket = io("https://nft-generator.tnwebsolutions.nl", {
//     withCredentials: true
// });

export default {
    components: {
        draggable,
    },
    data() {
        return {
            generator: {
                total: 10,
                base: '',
                description: '',
                layers: [],
                files: false,
                currentLayer: 0,
                uploadClasses: []
            }
        }
    },
    methods: {
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
        generateCollection: function(e) {
            this.setButtonLoader(e)

            this.uploadTraitJSON()
            this.uploadTraitImages(this.generator.files)
            
            // socket.emit('generate-nfts', {userID: 1, prefix: 'NFT #', description: 'Awesome NFT collection', total: 10});
            this.resetButtonLoader()
        },
        uploadTraitJSON: async function() {
            await axios.post('/generator/create', {layers: JSON.stringify(this.generator.layers)}).then((response) => {
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
        uploadTraitImages: async function(files) {
            var formData = new FormData()
            for (var i = 0; i < files.length; i++) {
                var file = files[i]
                if (file.type == 'image/png') {
                    formData.append('files[' + i + ']', file)
                }
            }

            await axios.post('/generator/upload', formData).then(function(response) {
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
                output.push({
                    'type': trait[0],
                    'options': trait[1]
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