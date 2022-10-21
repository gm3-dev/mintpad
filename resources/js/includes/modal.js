export default {
    data() {
        return {
            modal: {
                id: false,
                show: false,
                title: false,
                content: ''
            }
        }
    },
    mounted() {
        window.addEventListener("keyup", e => {
            if (e.key == 'Escape' && this.modal.id) {
                this.modal.id = false
            }
        });
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
         * Close modal
         */
        modalClose: function() {
            this.modal.id = false
        },
        /**
         * Open modal
         * @param {string} id 
         */
        modalOpen: function(id) {
            this.modal.id = id
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