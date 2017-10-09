<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card dark>
            <v-container grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 sm12>
                        <v-btn @click="showDialog('user_add')" class="blue lighten-1" dark>
                            New User
                            <v-icon right dark>add</v-icon>
                        </v-btn>
                    </v-flex>
                    <v-flex xs12 sm6>
                        <v-text-field prepend-icon="search" box dark label="Filter By Name" v-model="filters.name"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                        <v-text-field prepend-icon="search" box dark label="Filter By Email" v-model="filters.email"></v-text-field>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
        <!-- /search -->

        <!-- data table -->
        <v-data-table
                v-bind:headers="headers"
                :items="items"
                hide-actions
                class="elevation-1">
            <template slot="headerCell" scope="props">
                <span v-if="props.header.value=='name'">
                    <v-icon>person</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='email'">
                    <v-icon>email</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='permissions'">
                    <v-icon>vpn_key</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='last_login'">
                    <v-icon>av_timer</v-icon> {{ props.header.text }}
                </span>
                <span v-else>{{ props.header.text }}</span>
            </template>
            <template slot="items" scope="props">
                <td>
                    <v-btn @click="showDialog('user_edit',props.item)" icon small>
                        <v-icon dark class="blue--text">edit</v-icon>
                    </v-btn>
                    <v-btn @click="trash(props.item)" icon small>
                        <v-icon dark class="red--text">delete</v-icon>
                    </v-btn>
                </td>
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.email }}</td>
                <td>
                    <v-btn small @click="showDialog('user_permissions',props.item.permissions)" outline round color="grey" dark>Show</v-btn>
                </td>
                <td>{{ $appFormatters.formatDate(props.item.last_login) }}</td>
                <td>
                    <v-avatar outline>
                        <v-icon v-if="props.item.active!=null" class="green--text">check_circle</v-icon>
                        <v-icon class="grey--text" v-else>error_outline</v-icon>
                    </v-avatar>
                </td>
            </template>
        </v-data-table>
        <div class="text-xs-center">
            <v-pagination :length="totalPages" :total-visible="8" v-model="page" circle></v-pagination>
        </div>

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showPermissions.show" lazy absolute>
            <v-card>
                <v-card-title>
                    <div class="headline"><v-icon>vpn_key</v-icon> User Permissions</div>
                </v-card-title>
                <v-card-text>
                    <v-chip v-for="(permission,key) in dialogs.showPermissions.items" :key="key" class="white--text" :class="{'green':(permission.value==1),'red':(permission.value==-1),'blue':(permission.value==0)}">
                        <v-avatar v-if="permission.value==-1" class="red darken-4" title="Deny">
                            <v-icon>block</v-icon>
                        </v-avatar>
                        <v-avatar v-if="permission.value==1" class="green darken-4" title="Allow">
                            <v-icon>check_circle</v-icon>
                        </v-avatar>
                        <v-avatar v-if="permission.value==0" class="blue darken-4" title="Inherit">
                            <v-icon>swap_horiz</v-icon>
                        </v-avatar>
                        {{permission.title}}
                    </v-chip>
                    <p v-if="dialogs.showPermissions.items.length==0">No permissions</p>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- add user -->
        <v-dialog v-model="dialogs.add.show" fullscreen transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar dark class="primary">
                    <v-btn icon @click.native="dialogs.add.show = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Add User</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="dialogs.add.show = false">Done</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <user-form-add></user-form-add>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- edit user -->
        <v-dialog v-model="dialogs.edit.show" fullscreen :laze="false" transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar dark class="primary">
                    <v-btn icon @click.native="dialogs.edit.show = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Edit User</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="dialogs.edit.show = false">Done</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <user-form-edit :propUserId="dialogs.edit.user.id"></user-form-edit>
                </v-card-text>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
    import UserFormAdd from './UserFormAdd.vue';
    import UserFormEdit from './UserFormEdit.vue';
    export default {
        components: {
            UserFormAdd,
            UserFormEdit
        },
        data () {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Name', value: 'name', align: 'left', sortable: false },
                    { text: 'Email', value: 'email', align: 'left', sortable: false },
                    { text: 'Permissions', value: 'permissions', align: 'left', sortable: false },
                    { text: 'Last Login', value: 'last_login', align: 'left', sortable: false },
                    { text: 'Active', value: 'active', align: 'left', sortable: false },
                ],
                items: [],
                totalPages: 0,
                page: 1,

                filters: {
                    name: '',
                    email: '',
                },

                dialogs: {
                    showPermissions: {
                        items: [],
                        show: false
                    },
                    edit: {
                        user: {},
                        show: false
                    },
                    add: {
                        show: false
                    }
                }
            }
        },
        mounted() {
            const self = this;

            self.loadUsers(()=>{});

            self.$eventBus.$on(['USER_ADDED','USER_UPDATED','USER_DELETED','GROUP_ADDED'],()=>{
                self.loadUsers(()=>{});
            });
        },
        watch: {
            page(val) {
                const self = this;

                self.page = val;

                self.loadUsers(()=>{});
            },
            'filters.name':_.debounce(function(){
                const self = this;
                self.loadUsers(()=>{});
            },700),
            'filters.email':_.debounce(function(){
                const self = this;
                self.loadUsers(()=>{});
            },700)
        },
        methods: {
            trash(user) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this user?",
                    okCb: ()=>{

                        axios.delete('/ajax/users/' + user.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('USER_DELETED');

                        }).catch(function (error) {
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
                    cancelCb: ()=>{
                        console.log("CANCEL");
                    }
                });
            },
            showDialog(dialog, data) {

                const self = this;

                switch (dialog){
                    case 'user_permissions':
                        self.dialogs.showPermissions.items = data;
                        setTimeout(()=>{
                            self.dialogs.showPermissions.show = true;
                        },500);
                    break;
                    case 'user_edit':
                        self.dialogs.edit.user = data;
                        setTimeout(()=>{
                            self.dialogs.edit.show = true;
                        },500);
                    break;
                    case 'user_add':
                        setTimeout(()=>{
                            self.dialogs.add.show = true;
                        },500);
                        break;
                }
            },
            loadUsers(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    email: self.filters.email,
                    page: self.page
                };

                axios.get('/ajax/users',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalPages = response.data.data.last_page;
                    cb();
                });
            }
        }
    }
</script>