window.$ = require('jquery')
import { createApp, toRaw } from 'vue'
import VueTippy from "vue-tippy"
import { Chrome, create } from '@ckpack/vue-color';

// Includes
import { initSentry } from './includes/sentry'
import helpers from './includes/helpers.js'
import modal from './includes/modal.js'
import resources from './includes/resources'

// Config
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

if (document.getElementById('app')) {  
    const app = createApp({
        mixins: [helpers,resources,modal],
        data() {
            return {
                editMode: true,
                testje: '#194D33',
                colorpicker: {
                    primary: {
                        show: false,
                        color: {r: 0, g: 0, b: 0, a: 1},
                        
                    }
                },
                collectionID: false,
                collection: {
                    permalink: '',
                    buttons: [],
                    logo: false,
                    background: false
                },
                claimPhases: [],
                timers: {0: {}, 1: {}, 2: {}},
                loadComplete: false,
                edit: {
                    logo: {classes: []},
                    background: {classes: []},
                    button: false,
                    loading: false
                }
            }
        },
        computed: {
            primaryColor() {
                return this.colorpicker.primary.color;
            }
        },
        watch: {
            primaryColor(color) {
                this.theme.primary = color.rgba ? color.rgba : color
                this.setStyling()
            }
        },
        async mounted() {
            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }

            document.addEventListener("mouseup", e => {
                let target = $(e.target)[0].className
                let closeColorPicker = !target.startsWith('vc-')
                if (closeColorPicker) {
                    this.colorpicker.primary.show = false
                }
            })
            
            axios.get('/'+this.collectionID+'/fetch').then(async (response) => {
                this.collection.buttons = response.data.buttons
                this.collection.logo = response.data.logo
                this.collection.background = response.data.background
                this.collection.thumb = response.data.thumb
                this.collection.token = response.data.token

                this.setDummyData()
                
                // Set theme
                if (response.data.theme.mint) {
                    this.theme = response.data.theme.mint
                } else {
                    this.theme = this.theme.mint
                }

                this.colorpicker.primary.color = toRaw(this.theme.primary)
                this.setStyling()
                this.appReady()
                this.loadComplete = true

            }).catch((error) => {
                //
            });
        },
        methods: {
            toggleColorpicker: function(target) {
                this.colorpicker[target].show = !this.colorpicker[target].show
            },
            setDummyData: function() {
                const dummyData = this.getDummyCollection()
                this.collection = {...this.collection, ...dummyData.collection}
                this.claimPhases = dummyData.claimPhases
                this.timers = dummyData.timers
            },
            /**
             * Background
             */
            addBackground: function() {
                this.modal.id = 'edit-background'
                this.modalTitle('Edit background')
                this.setResource('background')
            },
            uploadBackground: function(event) {
                var files = event.target.files
                var formData = new FormData()
                formData.append('resource', files[0])
                formData.append('name', 'background')

                this.uploadResource('background', formData).then(async (response) => {
                    this.collection.background = response.data.url
                    this.resources.background.loading = false
                    
                }).catch((error) => {
                    if (error.response.data.errors != undefined) {
                        this.setMessage(error.response.data.errors.background[0], 'error')
                    } else {
                        this.setMessage('Something went wrong, please try again.', 'error')
                    }
                });
            },
            deleteBackground: function(event) {
                if (confirm("Are you sure you want to delete this background?") == true) {
                    this.deleteResource('background').then((response) => {
                        this.collection.background = false
                    })
                }
            },

            /**
             * Logo
             */
            addLogo: function() {
                this.modal.id = 'edit-logo'
                this.modal.title = 'Edit logo'
                this.setResource('logo')
            },
            uploadLogo: function(event) {
                var files = event.target.files
                var formData = new FormData()
                formData.append('resource', files[0])
                formData.append('name', 'logo')

                this.uploadResource('logo', formData).then(async (response) => {
                    this.collection.logo = response.data.url
                    this.resources.logo.loading = false
                    
                }).catch((error) => {
                    if (error.response.data.errors != undefined) {
                        this.setMessage(error.response.data.errors.logo[0], 'error')
                    } else {
                        this.setMessage('Something went wrong, please try again.', 'error')
                    }
                });
            },
            deleteLogo: function(event) {
                if (confirm("Are you sure you want to delete this logo?") == true) {
                    this.deleteResource('logo').then((response) => {
                        this.collection.logo = false
                    })
                }
            },
            /**
             * Buttons
             */
            deleteButton: function() {
                this.collection.buttons.splice(this.edit.button.index, 1)
                this.modal.id = false
            },
            editButton: function(index) {
                this.edit.button = JSON.parse(JSON.stringify(this.collection.buttons[index]))
                this.edit.button.index = index
                this.modal.id = 'edit-button'
            },
            newButton: function() {
                // this.collection.buttons.push({label: 'Button', href: ''})
                // this.editButton(this.collection.buttons.length-1)
                this.edit.button = {label: '', href: '', index: -1}
                this.modal.id = 'edit-button'
            },
            addNewButton: function() {
                if (this.edit.button.label == '' || this.edit.button.href == '') {
                    this.setMessage('Label and link are both required', 'error')
                } else {
                    if (this.edit.button.index == -1) {
                        this.collection.buttons.push({label: this.edit.button.label, href: this.edit.button.href})
                    } else {
                        this.collection.buttons[this.edit.button.index] = {label: this.edit.button.label, href: this.edit.button.href}
                    }
                    this.modal.id = false
                }
            },
            /**
             * Update settings
             */
            updateMintSettings: async function(e) {
                this.setButtonLoader(e)

                var data = {
                    buttons: this.collection.buttons,
                    theme: {mint: this.theme}
                }

                await axios.put('/collections/'+this.collectionID, data)
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.setMessage(error.response.data.message, 'error')
                    }
                })
                .then((response) => {
                    if (response) {
                        this.setMessage('Mint settings updated', 'success')
                    }
                })

                this.resetButtonLoader()
            },
            mintNFT: function() {
                this.modal.id = 'mint-successful'
            },
            openYouTubeModal: function(url) {
                this.modalToggle(true)
                this.modalTitle('Tutorial video')
                this.modalContent('<div class="w-full text-center"><iframe class="inline-block" width="650" height="366" src="'+url+'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>')
            }
        }
    })

    initSentry(app)
    app.use(
        VueTippy,
        {
            directive: 'tippy', // => v-tippy
            component: 'tippy', // => <tippy/>
        }
    )
    app.use(create({
        components: [Chrome],
    }));
    app.component('dark-mode', require('./components/DarkMode.vue').default)

    app.mount('#app')
}