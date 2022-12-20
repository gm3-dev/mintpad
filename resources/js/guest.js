window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'

// User address in navigation
if (document.getElementById('guest-container')) {  
    new Vue({
        el: '#guest-container',
        data: {
            showPassword: false,
            showConfirmPassword: false,
            isCompany: '0',
            signUpStep: 1,
            form: {
                name: '',
                reference: '',
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
            },
            validForm: true,
            validation: { 
                name: false,
                reference: false,
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
            },
            validationMessages: {
                required: 'Field is required'
            }
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
                    alert('submit')
                } else if (this.signUpStep == 1 && this.isCompany == '1') {
                    this.signUpStep = 2
                } else if (this.signUpStep == 1 && this.isCompany == '0') {
                    this.signUpStep = 3
                } else {
                    this.signUpStep += 1
                }
            },
            validateForm(step) {
                console.log('validate', step)
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
            }
        }
    })
}