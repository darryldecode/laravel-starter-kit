<template>
    <div class="component-wrap">
        <v-btn color="primary" @click="upload">Upload</v-btn>
        <div class="dropzone" id="fileupload"></div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                dropzone: null
            }
        },
        mounted() {
            console.log('pages.Home.vue');

            const self = this;

            self.initDropzone();
        },
        methods: {
            upload() {

                const self = this;

                self.dropzone.processQueue();
            },
            initDropzone() {

                const self = this;

                Dropzone.autoDiscover = false;

                self.dropzone = new Dropzone("#fileupload", {
                    url:'/ajax/files',
                    paramName: "file", // The name that will be used to transfer the file
                    maxFilesize: 50, // 50MB
                    uploadMultiple: true,
                    acceptedFiles: 'image/*',
                    headers: {'X-CSRF-TOKEN' : _token},
                    autoProcessQueue: false,
                    init: function() {
                        // initial hook
                    },
                    success: function(file, response){
                        // success hook
                    }
                });
            }
        }
    }
</script>
<style scoped>
    #fileupload {
        min-height: 400px;
        background: grey;
        border: 1px dashed #eaeaea;
    }
    .dropzone .dz-preview.dz-image-preview {
        background: none;
    }
</style>