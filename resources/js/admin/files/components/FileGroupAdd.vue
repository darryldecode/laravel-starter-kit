<template>
    <div class="component-wrap">
        <v-card>
            <v-form v-model="valid" ref="fileGroupFormAdd" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <div class="body-2 white--text">File Group Details</div>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field label="Group Name" v-model="name" :rules="nameRules"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea label="Group Description" v-model="description" :rules="descriptionRules"></v-textarea>
                        </v-flex>
                        <v-flex xs12>
                            <v-btn @click="save()" :disabled="!valid" color="primary">Save</v-btn>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                valid: false,
                isLoading: false,
                name: '',
                nameRules: [
                    (v) => !!v || 'Name is required',
                ],
                description: '',
                descriptionRules: [
                    (v) => !!v || 'Description is required',
                ],
            }
        },
        mounted() {
            console.log('pages.files.components.FileGroupAdd.vue');

            const self = this;
        },
        methods: {
            save() {
                const self = this;

                let payload = {
                    name: self.name,
                    description: self.description
                };

                self.isLoading = true;

                axios.post('/admin/file-groups',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });

                    self.isLoading = false;
                    self.$eventBus.$emit('FILE_GROUP_ADDED');

                    // reset
                    self.$refs.fileGroupFormAdd.reset();

                }).catch(function (error) {
                    self.isLoading = false;
                    if (error.response) {
                        self.$store.commit('showSnackbar',{
                            message: error.response.data.message,
                            color: 'error',
                            duration: 3000
                        });
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                });
            }
        }
    }
</script>