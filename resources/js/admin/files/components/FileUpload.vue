<template>
    <div class="component-wrap">
        <!-- form -->
        <v-card>
            <v-form ref="fileFormUpload" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12 sm8>
                            <v-select
                                    label="Upload To File Group"
                                    v-bind:items="fileGroups"
                                    v-model="uploadTo"
                                    item-text="name"
                                    item-value="id"
                            ></v-select>
                        </v-flex>
                        <v-flex xs12 sm4>
                            <v-btn @click="clear()" block class="primary lighten-1" dark>
                                Clear
                            </v-btn>
                        </v-flex>
                        <v-flex xs12>
                            <div class="dropzone" id="fileupload"></div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
        <!-- /form -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                dropzone: null,
                fileGroups: [],
                uploadTo: '',
                addedFiles: []
            }
        },
        mounted() {
            console.log('pages.files.components.FileUpload.vue');

            const self = this;

            self.loadFileGroups(()=>{});
            self.initDropzone();

            self.$eventBus.$on(['FILE_GROUP_ADDED'],()=>{
                self.loadFileGroups(()=>{});
            });
        },
        methods: {
            clear() {
                const self = this;

                _.each(self.addedFiles,f=>{
                    self.dropzone.removeFile(f);
                });

                self.addedFiles = [];
            },
            upload() {

                const self = this;

                self.dropzone.processQueue();
            },
            initDropzone() {

                const self = this;

                Dropzone.autoDiscover = false;

                self.dropzone = new Dropzone("#fileupload", {
                    url:'/admin/files',
                    paramName: "file", // The name that will be used to transfer the file
                    maxFilesize: 50, // 50MB
                    uploadMultiple: true,
                    //acceptedFiles: 'image/*',
                    headers: {'X-CSRF-TOKEN' : _token},
                    autoProcessQueue: true,
                    init: function() {
                        // initial hook
                    },
                    success: function(file, response){
                        // success hook
                    }
                });

                self.dropzone.on("addedfile", function(file) {
                    if(!self.uploadTo) {
                        self.$store.commit('showSnackbar',{
                            message: "Please choose file group to upload the file(s)",
                            color: 'error',
                            duration: 3000
                        });
                        self.dropzone.removeFile(file);
                    } else {
                        self.addedFiles.push(file);
                    }
                });

                self.dropzone.on('sending',(file,xhr,formData)=> {
                    formData.append('file_group_id',self.uploadTo);
                });

                self.dropzone.on("complete", function(file) {
                    self.$store.commit('showSnackbar',{
                        message: "File(s) uploaded successfully.",
                        color: 'success',
                        duration: 3000
                    });

                    self.$eventBus.$emit('FILE_UPLOADED');
                });
            },
            loadFileGroups(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/file-groups',{params: params}).then(function(response) {
                    self.fileGroups = response.data.data;
                    cb();
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