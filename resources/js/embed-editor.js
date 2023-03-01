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
        components: {},
        data() {
            return {
                editMode: true,
                colorpicker: {
                    primary: {
                        show: false,
                        color: {r: 0, g: 0, b: 0, a: 1},
                    },
                    background: {
                        show: false,
                        color: {r: 0, g: 0, b: 0, a: 1},
                    },
                    phases: {
                        show: false,
                        color: {r: 0, g: 0, b: 0, a: 1},
                    }
                },
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
                return this.colorpicker.primary.color;
            },
            backgroundColor() {
                return this.colorpicker.background.color;
            },
            phaseColor() {
                return this.colorpicker.phases.color;
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
            primaryColor(color) {
                this.theme.primary = color.rgba ? color.rgba : color
                this.setStyling()
            },
            backgroundColor(color) {
                this.theme.background = color.rgba ? color.rgba : color
                this.setStyling()
            },
            phaseColor(color) {
                this.theme.phases = color.rgba ? color.rgba : color
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
                    this.colorpicker.background.show = false
                    this.colorpicker.phases.show = false
                }
            })
            
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
                this.colorpicker.primary.color = toRaw(this.theme.primary)
                this.colorpicker.background.color = toRaw(this.theme.background)
                this.colorpicker.phases.color = toRaw(this.theme.phases)

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
            toggleColorpicker: function(target) {
                this.colorpicker.primary.show = false
                this.colorpicker.background.show = false
                this.colorpicker.phases.show = false
                this.colorpicker[target].show = !this.colorpicker[target].show
            },
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