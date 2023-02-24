<script>
    import Editor from '@tinymce/tinymce-vue'

    export default {
        components: {
            'editor': Editor
        },
        props: {
            value: {
                type: String
            }
        },
        methods: {
            updateValue: function (value) {
                this.$emit('input', value)
            }
        }
    }
</script>
<template>
    <div>
        <editor
            :value="value"
            v-on:input="updateValue" 
            :init="{
                menubar: false,
                statusbar: false,
                toolbar: 'undo redo link h1 h2 h3 bold italic alignleft aligncenter alignright numlist bullist',
                plugins: 'link lists autoresize',
                autoresize_bottom_margin: 0,
                min_height: 300,
                valid_elements: 'p,h1,h2,h3,ul,ol,li,strong,em',
                valid_children: 'p[a,em,strong],li[a,em,strong]',
                extended_valid_elements: 'a[href|target|title|rel]'
            }"
        />
    </div>
</template>