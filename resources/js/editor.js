window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'
import helpers from './helpers.js'
import modal from './modal.js'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
import VueTippy from "vue-tippy"
Vue.use(VueTippy)
import {ColorPicker, ColorPanel} from 'one-colorpicker'

if (document.getElementById('app')) {  
    Vue.component('tinymce', require('./components/TinyMCE.vue').default);
    Vue.use(ColorPanel)
    Vue.use(ColorPicker)

    new Vue({
        el: '#app',
        mixins: [helpers,modal],
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

            axios.get('/mint/'+this.collectionID+'/fetch').then(async (response) => {
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
            /**
             * Logo management
             */
            addResource: function(name) {
                this.modal.id = 'edit-'+name
            },
            deleteResource: function(name) {
                if (confirm("Are you sure you want to delete this "+name+"?") == true) {
                    var data = {data: {name: name}}
                    axios.delete('/editor/'+this.collectionID+'/delete-resource', data).then(async (response) => {
                        this.collection[name] = false
                        this.setBackground()
                    }).catch((error) => {
                        // console.log(error)
                    });
                }
            },
            dragEnterUploadResource: function(name) {
                this.edit[name].classes = ['border-mintpad-300']
            },
            dragLeaveUploadResource: function(name) {
                this.edit[name].classes = []
            },
            uploadResource: function(name, event) {
                this.edit.loading = true
                var files = event.target.files
                var formData = new FormData()
                formData.append('resource', files[0])
                formData.append('name', name)
                axios.post('/editor/'+this.collectionID+'/upload-resource', formData).then(async (response) => {
                    this.collection[name] = response.data.url
                    console.log('response.data.url', response.data.url)
                    this.edit.loading = false
                    this.setBackground()
                }).catch((error) => {
                    this.edit.loading = false
                    if (error.response.data.errors != undefined) {
                        this.setErrorMessage(error.response.data.errors[name][0])
                    } else {
                        this.setErrorMessage('Something went wrong, please try again.')
                    }
                });
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