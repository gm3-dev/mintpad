export default {
    data() {
        return {
            modal: {
                show: false,
                title: false,
                content: ''
            }
        }
    },
    methods: {
        /**
         * Toggle modal
         * @param {boolean} state 
         */
        modalToggle: function(state) {
            this.modal.show = state
        },
        /**
         * Set modal title
         * @param {string} title 
         */
        modalTitle: function(title) {
            this.modal.title = title
        },
        /**
         * Set modal content
         * @param {string} content 
         */
        modalContent: function(content) {
            this.modal.content = content
        }
    }
}