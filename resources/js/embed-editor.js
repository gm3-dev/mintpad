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
    Vue.component('dark-mode', require('./components/DarkMode.vue').default);

    Vue.use(ColorPanel)
    Vue.use(ColorPicker)

    new Vue({
        el: '#app',
        mixins: [helpers,resources,modal],
        components: {},
        data: {
            editMode: true,
            collectionID: false,
            collection: {
                permalink: ''
            },
            claimPhases: [],
            timers: {0: {}, 1: {}, 2: {}},
            loadComplete: false,
            activeMintPhase: false,
            settingsChanged: false,
            settings: {
                phases: null,
                darkmode: false
            }
        },
        computed: {
            phasesSetting() {
                return this.settings.phases
            },
            darkmodeSetting() {
                return this.settings.darkmode
            },
            primaryColor() {
                return this.theme.primary;
            },
            backgroundColor() {
                return this.theme.background;
            },
            phaseColor() {
                return this.theme.phases;
            }
        },
        watch: {
            phasesSetting(newValue, oldValue) {
                if (oldValue != null) {
                    this.settingsChanged = true
                }
            },
            darkmodeSetting(darkmode) {
                this.setDarkmode(darkmode)
            },
            primaryColor() {
                this.setStyling()
            },
            backgroundColor() {
                this.setStyling()
            },
            phaseColor() {
                this.setStyling()
            }
        },
        async mounted() {
            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }
            
            axios.get('/'+this.collectionID+'/fetch').then(async (response) => {
                this.collection.token = response.data.token
                this.collection.embed_url = response.data.embed_url
                this.setDummyData()
                
                // Set theme
                if (response.data.theme.embed) {
                    this.theme = response.data.theme.embed
                } else {
                    this.theme = this.theme.embed
                }
                // Set settings
                if (response.data.settings.embed) {
                    this.settings = response.data.settings.embed
                }
                this.setStyling()
                this.appReady()
                this.loadComplete = true

            }).catch((error) => {
                //
            });
        },
        methods: {
            setDummyData: function() {
                const dummyData = this.getDummyCollection()
                this.collection = {...this.collection, ...dummyData.collection}
                this.claimPhases = dummyData.claimPhases
                this.timers = dummyData.timers
                this.activeMintPhase = 1;
            },
            previousPhase: function() {
                const phaseCount = this.claimPhases.length-1
                if (this.activeMintPhase == 0) {
                    this.activeMintPhase = phaseCount
                } else {
                    this.activeMintPhase--
                }
            },
            nextPhase: function() {
                const phaseCount = this.claimPhases.length-1
                if (this.activeMintPhase == phaseCount) {
                    this.activeMintPhase = 0
                } else {
                    this.activeMintPhase++
                }
            },
            copyEmbedCode: function(e) {
                var button = $(e.target)
                var buttonWidth = button.outerWidth()
                var buttonText = button.html()
                button.css('width', buttonWidth+'px').text('Copied')
                setTimeout(function() {
                    button.html(buttonText)
                }, 1000)
                navigator.clipboard.writeText(this.getIframeCode())
            },
            getIframeCode: function() {
                const height = this.settings.phases ? '369px' : '233px'
                return '<iframe frameborder="0" width="600px" height="'+height+'" src="'+this.collection.embed_url+'"></iframe>';
            },
            /**
             * Update settings
             */
            updateMintSettings: async function(e) {
                this.setButtonLoader(e)

                var data = {
                    buttons: this.collection.buttons,
                    theme: {embed: this.theme},
                    settings: {embed: this.settings}
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

                this.settingsChanged = false
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
    });
}