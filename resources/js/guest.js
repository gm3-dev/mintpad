window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

// User address in navigation
if (document.getElementById('guest-container')) {  
    new Vue({
        el: '#guest-container',
        data: {
            showPassword: false,
            showConfirmPassword: false,
            signUpStep: 1,
            form: {
                name: '',
                birth_day: false,
                birth_month: false,
                birth_year: false,
                reference: '',
                is_company: '0',
                company_name: '',
                vat_id: '',
                country: 'US',
                address: '',
                city: '',
                postalcode: '',
                state: '',
                email: '',
                password: '',
                password_confirmation: '',
                accept_tos: false,
                affiliate: ''
            },
            validForm: true,
            validation: {},
            validationMessages: {
                required: 'Field is required'
            }
        },
        mounted() {
            if ($('#affiliate-code').length) {
                this.form.affiliate = $('#affiliate-code').val()
            }
            const now = new Date()
            this.form.birth_day = now.getDate()
            this.form.birth_month = now.getMonth()+1
            this.form.birth_year = now.getFullYear()
            this.setValidationData()
        },
        methods: {
            toggleShowPassword(state) {
                this.showPassword = state
            },
            toggleShowConfirmPassword(state) {
                this.showConfirmPassword = state
            },
            nextSignUpStep() {
                this.validateForm(this.signUpStep)
                
                if (!this.validForm) return

                if (this.signUpStep == 4) {
                    this.postForm()
                } else if (this.signUpStep == 1 && this.form.is_company == '1') {
                    this.signUpStep = 2
                } else if (this.signUpStep == 1 && this.form.is_company == '0') {
                    this.signUpStep = 3
                } else {
                    this.signUpStep += 1
                }
            },
            postForm() {
                axios.post('/register', this.form).then((response) => {
                    window.location.href = response.data.redirect
                }).catch(error => {
                    this.setValidationData()

                    for (var field in error.response.data.errors) {
                        this.validation[field] = error.response.data.errors[field][0]
                    }
                })
            },
            validateForm(step) {
                this.validForm = true

                switch (step) {
                    case 1:
                        this.isRequired('name')
                        this.isRequired('reference')
                        break;
                    case 2:
                        this.isRequired('company_name')
                        this.isRequired('vat_id')
                        break;
                    case 3:
                        this.isRequired('country')
                        this.isRequired('address')
                        this.isRequired('city')
                        this.isRequired('postalcode')
                        this.isRequired('state')
                        break;
                }
            },
            isRequired(name) {
                if (this.form[name].trim() == '') {
                    this.validation[name] = this.validationMessages.required
                    this.validForm = false
                } else {
                    this.validation[name] = false
                }
            },
            setValidationData() {
                this.validation = { 
                    name: false,
                    reference: false,
                    is_company: false,
                    company_name: false,
                    vat_id: false,
                    country: false,
                    address: false,
                    city: false,
                    postalcode: false,
                    state: false,
                    email: false,
                    password: false,
                    password_confirmation: false,
                    accept_tos: false,
                }
            }
        }
    })
}