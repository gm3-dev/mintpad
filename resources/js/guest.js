window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'

// User address in navigation
if (document.getElementById('guest-container')) {  
    new Vue({
        el: '#guest-container',
        data: {
            showPassword: false,
            showConfirmPassword: false
        },
        methods: {
            toggleShowPassword: function(state) {
                this.showPassword = state
            },
            toggleShowConfirmPassword: function(state) {
                this.showConfirmPassword = state
            }
        }
    })
}

$(() => {
    if (document.getElementById('is-company')) {  
        var isCompany = $('#is-company').is(':checked');
        toggleCompanyInfo(isCompany);

        $('#guest-container').on('click', '#is-company', (e) => {  
            var isCompany = $('#is-company').is(':checked');
            toggleCompanyInfo(isCompany);
        })
    }
})

function toggleCompanyInfo(status) {
    status ? $('#company-info').removeClass('hidden') : $('#company-info').addClass('hidden');
}