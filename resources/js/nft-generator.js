window.$ = require('jquery')
// https://github.com/SortableJS/Vue.Draggable
import draggable from 'vuedraggable'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
import { io } from "socket.io-client";
const socket = io(process.env.MIX_GENERATOR_URL, {
    'withCredentials': true,
    'reconnection': true,
    'reconnectionDelay': 500,
    'reconnectionAttempts': 10
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
                },
                userID: false
            }
        }
    },
    mounted: function() {
        socket.on('nft-generation-status', (response) => {
            this.handleSocketResponse(response)
        })
        socket.on('connect', () => {
            console.log('connect', socket.id)
        })
        socket.on('disconnect', (reason) => {
            console.log('disconnect', reason)
        })
    },
    methods: {
        disconnectSocketio: function() {
            socket.disconnect()
        },
        connectSocketio: function() {
            socket.connect()
        },
        handleSocketResponse: function(response) {
            if (response.state == 'error') {
                this.setErrorMessage(response.value)
            } else if (response.state == undefined) {
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
        weightChange: function(layerIndex) {
            var layer = this.generator.layers[layerIndex].options

            var totalWeight = 0
            for (var i = 0; i < layer.length; i++) {
                var option = layer[i]
                totalWeight += parseInt(option.weight)
            }
            if (totalWeight == 0) {
                var percPerWeight = 0
            } else {
                var percPerWeight = 100 / totalWeight
            }
            var totalPerc = 0;
            for (var i = 0; i < layer.length; i++) {
                var option = layer[i]
                var perc = Math.round((percPerWeight*parseInt(option.weight)) * 10) / 10
                totalPerc += perc
                option.perc = perc
            }
        },
        generateCollection: async function(e) {
            this.setButtonLoader(e)

            await this.uploadTraitJSON()
            await this.uploadTraitImages(this.generator.files)

            // Todo: userID is not dynamic
            if (this.generator.userID) {
                socket.emit('nft-generation', {userID: this.generator.userID, prefix: this.generator.base, description: this.generator.description, total: parseInt(this.generator.total)});
            } else {
                this.setErrorMessage('Generation failed, please try again.')
            }
            this.resetButtonLoader()
        },
        uploadTraitJSON: function() {
            return axios.post('/generator/create', {layers: JSON.stringify(this.generator.layers)}).then((response) => {
                if (response.status == 200) {
                    this.generator.userID = response.data.user_id
                }
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
                value: structure[1].replace(/\.[^/.]+$/, ""),
            }
        },
        createLayerList: function(traits) {
            var output = []
            Object.entries(traits).forEach(trait => {
                var options = trait[1]
                for (var i = 0; i < options.length; i++) {
                    options[i].perc = Math.round(100/trait[1].length)
                }
                // Sort traits
                options.sort((a, b) => {
                    let va = a.value.toLowerCase(),
                        vb = b.value.toLowerCase();
                    if (va < vb) {
                        return -1;
                    }
                    if (va > vb) {
                        return 1;
                    }
                    return 0;
                });
                output.push({
                    'type': trait[0],
                    'options': options
                })
            })

            // Sort layers
            output.sort((a, b) => {
                let ta = a.type.toLowerCase(),
                    tb = b.type.toLowerCase();
                if (ta < tb) {
                    return -1;
                }
                if (ta > tb) {
                    return 1;
                }
                return 0;
            });
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