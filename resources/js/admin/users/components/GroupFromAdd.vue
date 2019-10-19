<template>
    <div class="component-wrap">

        <!-- form -->
        <v-card>
            <v-card-title>
                <v-icon>groups</v-icon> Create Group
            </v-card-title>
            <v-divider></v-divider>
            <v-form v-model="valid" ref="groupFormAdd" lazy-validation>
                <v-container grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12>
                        <div class="body-2 white--text">Group Details</div>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field label="Group Name" v-model="name" :rules="nameRules"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm4>
                        <v-select
                                label="Select Permission"
                                v-bind:items="options.permissions"
                                v-model="selectedPermission"
                                item-text="title"
                                item-value="key"
                        ></v-select>
                    </v-flex>
                    <v-flex xs12 sm4>
                        <v-select
                                label="Permission Value"
                                v-bind:items="options.permissionValues"
                                v-model="selectedPermissionValue"
                                item-text="label"
                                item-value="value"
                        ></v-select>
                    </v-flex>
                    <v-flex xs12 sm4>
                        <v-btn @click="addSpecialPermission()" class="primary lighten-1" dark>
                            Add Permission
                            <v-icon right>add</v-icon>
                        </v-btn>
                    </v-flex>
                    <v-flex xs12>
                        <div class="permissions_container">
                            <v-chip v-for="(p,k) in permissions" @input="remove(k)" :key="k" close class="white--text" :class="{'green':(p.value==1),'red':(p.value==-1),'blue':(p.value==0)}">
                                <v-avatar v-if="p.value==-1" class="red darken-4" title="Deny">
                                    <v-icon>block</v-icon>
                                </v-avatar>
                                <v-avatar v-if="p.value==1" class="green darken-4" title="Allow">
                                    <v-icon>check_circle</v-icon>
                                </v-avatar>
                                <v-avatar v-if="p.value==0" class="blue darken-4" title="Inherit">
                                    <v-icon>swap_horiz</v-icon>
                                </v-avatar>
                                {{p.title}}
                            </v-chip>
                            <div v-if="permissions.length===0">No special permissions assigned.</div>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <v-btn @click="save()" :disabled="!valid" color="primary" dark>Save</v-btn>
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
                valid: false,
                name: '',
                nameRules: [
                    (v) => !!v || 'Name is required',
                ],
                permissions: [],
                selectedPermission: {},
                selectedPermissionValue: 0,

                options: {
                    permissions: [],
                    permissionValues:[
                        {label:'Allow', value:1},
                        {label:'Deny', value:-1},
                    ],
                }
            }
        },
        mounted() {
            const self = this;

            self.loadPermissions(()=>{});

            self.$store.commit('setBreadcrumbs',[
                {label:'Users',to:{name:'users.list'}},
                {label:'Groups',to:{name:'users.groups.list'}},
                {label:'Create',to:''},
            ]);
        },
        methods: {
            remove(i) {
                this.permissions.splice(i,1);
            },
            save() {

                const self = this;

                let payload = {
                    name: self.name,
                    permissions: self.permissions
                };

                self.$store.commit('showLoader');

                axios.post('/admin/groups',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });
                    self.$store.commit('hideLoader');
                    self.$eventBus.$emit('GROUP_ADDED');

                    // reset
                    self.$refs.groupFormAdd.reset();
                    self.permissions = [];

                }).catch(function (error) {

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
            addSpecialPermission() {
                const self = this;

                _.each(self.options.permissions,(p)=>{

                    if(self.selectedPermission===p.key) {

                        if(!self.existsInPermissions(self.selectedPermission)) {
                            p.value = self.selectedPermissionValue;
                            self.permissions.push(p);
                        }
                    }
                });

                console.log(self.permissions);
            },
            loadPermissions(cb) {

                const self = this;

                let params = {
                    paginate: 'no',
                    page: self.page
                };

                axios.get('/admin/permissions',{params: params}).then(function(response) {
                    self.options.permissions = response.data.data;
                    cb();
                });
            },
            existsInPermissions(permissionKey) {
                const self = this;
                let found = false;
                _.each(self.permissions,(p)=>{
                    if(p.permission===permissionKey) found = true;
                });
                return found;
            },
        }
    }
</script>