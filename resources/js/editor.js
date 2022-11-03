window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'
import VueTippy from "vue-tippy"
import {ColorPicker, ColorPanel} from 'one-colorpicker'

// Includes
import { initSentry, resportError } from './includes/sentry'
import helpers from './includes/helpers.js'
import modal from './includes/modal.js'
import resources from './includes/resources'

// Config
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
Vue.use(VueTippy)
initSentry(Vue)

if (document.getElementById('app')) {  
    Vue.component('tinymce', require('./components/TinyMCE.vue').default);
    Vue.use(ColorPanel)
    Vue.use(ColorPicker)

    new Vue({
        el: '#app',
        mixins: [helpers,resources,modal],
        components: {},
        data: {
            style: {},
            colors: false,
            collectionID: false,
            collection: {
                permalink: '',
                about: '',
                roadmap: '',
                team: '',
                buttons: [],
                logo: false,
                background: false
            },
            tab: 1,
            edit: {
                logo: {classes: []},
                background: {classes: []},
                button: false,
                loading: false
            }
        },
        computed: {
            primaryColor() {
                return this.theme.primary;
            },
            backgroundColor() {
                return this.theme.background
            },
            boxColor() {
                return this.theme.box
            },
            titleColor() {
                return this.theme.title
            },
            textColor() {
                return this.theme.text
            },
        },
        watch: {
            primaryColor() {
                this.setStyling()
            },
            backgroundColor() {
                this.setStyling()
            },
            boxColor() {
                this.setStyling()
            },
            titleColor() {
                this.setStyling()
            },
            textColor() {
                this.setStyling()
            }
        },
        async mounted() {
            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }

            axios.get('/'+this.collectionID+'/fetch').then(async (response) => {
                this.collection.buttons = response.data.buttons
                this.collection.about = response.data.about
                this.collection.roadmap = response.data.roadmap
                this.collection.team = response.data.team
                this.collection.logo = response.data.logo
                this.collection.background = response.data.background
                this.collection.thumb = response.data.thumb
                
                // Set theme
                if (response.data.theme) {
                    this.theme = response.data.theme
                }
                this.setBackground()
                this.setStyling()
                this.appReady()

            }).catch((error) => {
                //
            });
        },
        methods: {
            addBackground: function() {
                this.modal.id = 'edit-background'
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
                    this.setBackground()
                    
                }).catch((error) => {
                    if (error.response.data.errors != undefined) {
                        this.setErrorMessage(error.response.data.errors.background[0])
                    } else {
                        this.setErrorMessage('Something went wrong, please try again.')
                    }
                });
            },
            deleteBackground: function(event) {
                if (confirm("Are you sure you want to delete this background?") == true) {
                    this.deleteResource('background').then((response) => {
                        this.collection.background = false
                        this.setBackground()
                    })
                }
            },


            addLogo: function() {
                this.modal.id = 'edit-logo'
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
                        this.setErrorMessage(error.response.data.errors.logo[0])
                    } else {
                        this.setErrorMessage('Something went wrong, please try again.')
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

            setColorCode: function(color) {
                //
            },
            RGBAtoRGB: function (r, g, b, a, r2,g2,b2){
                var r3 = Math.round(((1 - a) * r2) + (a * r))
                var g3 = Math.round(((1 - a) * g2) + (a * g))
                var b3 = Math.round(((1 - a) * b2) + (a * b))
                return "rgb("+r3+","+g3+","+b3+")";
            },
            changeBackgroundColor: function() {
                this.theme.primary = '59, 149, 13'
            },
            changeTab: function(index) {
                this.tab = index
            },
            deleteButton: function() {
                this.collection.buttons.splice(this.edit.button.index, 1)
                this.modal.id = false
            },
            editButton: function(index) {
                this.edit.button = this.collection.buttons[index]
                this.edit.button.index = index
                this.modal.id = 'edit-button'
            },
            newButton: function() {
                this.collection.buttons.push({label: 'Button', href: ''})
                this.editButton(this.collection.buttons.length-1)
            },
            addNewButton: function() {
                if (this.newButton.label == '' || this.newButton.label == '') {
                    this.setErrorMessage('Label and link are both required')
                } else {
                    this.collection.buttons.push({label: this.newButton.label, href: this.newButton.href})

                    this.newButton.label = ''
                    this.newButton.href = ''
                }
            },
            updateMintSettings: async function(e) {
                this.setButtonLoader(e)

                var data = {
                    buttons: this.collection.buttons,
                    about: this.collection.about,
                    team: this.collection.team,
                    roadmap: this.collection.roadmap,
                    theme: this.theme
                }

                await axios.put('/collections/'+this.collectionID, data)
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.setErrorMessage(error.response.data.message)
                    }
                })
                .then((response) => {
                    if (response) {
                        this.setSuccessMessage('Mint settings updated')
                    }
                })

                this.resetButtonLoader()
            }
        }
    });
}