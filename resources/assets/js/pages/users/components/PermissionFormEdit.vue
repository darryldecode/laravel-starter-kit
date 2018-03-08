<template>
    <div class="component-wrap">

        <!-- form -->
        <v-card dark>
            <v-form v-model="valid" ref="permissionFormEdit" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <div class="body-2 white--text">Permission Details</div>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field box dark label="Permission Title" v-model="title" :rules="titleRules"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field box dark label="Permission Key" v-model="permissionKey" :rules="permissionKeyRules"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field box dark label="Description" v-model="description" :rules="descriptionRules" multi-line></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-btn @click="save()" :loading="isLoading" :disabled="!valid || isLoading" color="primary" dark>Save</v-btn>
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
        props: {
            propPermissionId: {
                required: true
            }
        },
        data() {
            return {
                valid: false,
                isLoading: false,
                title: '',
                titleRules: [
                    (v) => !!v || 'Title is required',
                ],
                description: '',
                descriptionRules: [
                    (v) => !!v || 'Description is required',
                ],
                permissionKey: '',
                permissionKeyRules: [
                    (v) => !!v || 'Permission Key is required',
                    (v) => !v.match(/[^\w\.]+/g) || 'Description cannot contain special characters',
                ],
            }
        },
        mounted() {
            console.log('components.PermissionFormEdit.vue');
        },
        watch: {
            permissionKey(v) {
                this.permissionKey = v.replace(' ','.').toLowerCase();
            },
            title(v) {
                this.permissionKey = v.replace(' ','.').toLowerCase();
            },
            propPermissionId(v) {
                if(v) this.loadPermission(()=>{});
            }
        },
        methods: {
            save() {

                const self = this;

                let payload = {
                    title: self.title,
                    description: self.description,
                    key: self.permissionKey
                };

                self.isLoading = true;

                axios.put('/admin/permissions/' + self.propPermissionId,payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });
                    self.$eventBus.$emit('PERMISSION_UPDATED');

                    // reset
                    self.permissions = [];
                    self.isLoading = false;

                }).catch(function (error) {

                    self.isLoading = false;
                    self.$store.commit('hideLoader');

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
            },
            loadPermission(cb) {

                const self = this;

                axios.get('/admin/permissions/' + self.propPermissionId).then(function(response) {

                    let Permission = response.data.data;

                    self.title = Permission.title;
                    self.description = Permission.description;
                    self.permissionKey = Permission.key;

                    cb();
                });
            }
        }
    }
</script>