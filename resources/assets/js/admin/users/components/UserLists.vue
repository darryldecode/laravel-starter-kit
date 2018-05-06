<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card dark class="pt-3">
            <v-layout row wrap>
                <v-flex xs12 sm4 class="px-2">
                    <v-btn @click="$router.push({name:'users.create'})" class="blue lighten-1" dark>
                        New User
                        <v-icon right dark>add</v-icon>
                    </v-btn>
                </v-flex>
                <v-flex xs12 sm8 class="px-2 text-xs-center text-sm-right">
                    <v-btn @click="$router.push({name:'users.groups.list'})" class="blue lighten-1" dark>
                        Manage Groups <v-icon right dark>group</v-icon>
                    </v-btn>
                    <v-btn @click="$router.push({name:'users.permissions.list'})" class="blue lighten-1" dark>
                        Manage Permissions <v-icon right dark>vpn_key</v-icon>
                    </v-btn>
                </v-flex>
                <v-flex xs12 class="my-2"><v-divider></v-divider></v-flex>
                <v-flex xs12 sm4 class="px-2">
                    <v-text-field prepend-icon="search" dark label="Filter By Name" v-model="filters.name"></v-text-field>
                </v-flex>
                <v-flex xs12 sm4 class="px-2">
                    <v-text-field prepend-icon="search" dark label="Filter By Email" v-model="filters.email"></v-text-field>
                </v-flex>
                <v-flex xs12 sm4 class="px-2">
                    <v-select box
                              multiple
                              chips
                              deletable-chips
                              clearable
                              prepend-icon="filter_list"
                              autocomplete
                              label="Filter By Groups"
                              placeholder="Select groups.."
                              :items="filters.groupOptions"
                              item-text="name"
                              item-value="id"
                              v-model="filters.groupId"
                    ></v-select>
                </v-flex>
            </v-layout>
        </v-card>
        <!-- /search -->

        <!-- data table -->
        <v-data-table
                v-bind:headers="headers"
                v-bind:pagination.sync="pagination"
                :items="items"
                :total-items="totalItems"
                class="elevation-1">
            <template slot="headerCell" slot-scope="props">
                <span v-if="props.header.value=='name'">
                    <v-icon>person</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='email'">
                    <v-icon>email</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='permissions'">
                    <v-icon>vpn_key</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='groups'">
                    <v-icon>group</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='last_login'">
                    <v-icon>av_timer</v-icon> {{ props.header.text }}
                </span>
                <span v-else>{{ props.header.text }}</span>
            </template>
            <template slot="items" slot-scope="props">
                <td>
                    <v-menu>
                        <v-btn icon slot="activator" dark>
                            <v-icon>more_vert</v-icon>
                        </v-btn>
                        <v-list>
                            <v-list-tile @click="$router.push({name:'users.edit',params:{id: props.item.id}})">
                                <v-list-tile-title>Edit</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile @click="trash(props.item)">
                                <v-list-tile-title>Delete</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </td>
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.email }}</td>
                <td>
                    <v-btn small @click="showDialog('user_permissions',props.item.permissions)" outline round color="grey" dark>Show</v-btn>
                </td>
                <td>
                    <v-chip v-for="group in props.item.groups" :key="group.id" outline color="grey" text-color="grey">
                        {{group.name}}
                    </v-chip>
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

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showPermissions.show" lazy absolute max-width="300px">
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

    </div>
</template>

<script>
    export default {
        data () {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Name', value: 'name', align: 'left', sortable: false },
                    { text: 'Email', value: 'email', align: 'left', sortable: false },
                    { text: 'Permissions', value: 'permissions', align: 'left', sortable: false },
                    { text: 'Groups', value: 'groups', align: 'left', sortable: false },
                    { text: 'Last Login', value: 'last_login', align: 'left', sortable: false },
                    { text: 'Active', value: 'active', align: 'left', sortable: false },
                ],
                items: [],
                totalItems: 0,
                pagination: {
                    rowsPerPage: 10
                },

                filters: {
                    name: '',
                    email: '',
                    groupId: [],
                    groupOptions: []
                },

                dialogs: {
                    showPermissions: {
                        items: [],
                        show: false
                    }
                }
            }
        },
        mounted() {
            const self = this;

            self.loadGroups(()=>{});

            self.$eventBus.$on(['USER_ADDED','USER_UPDATED','USER_DELETED','GROUP_ADDED'],()=>{
                self.loadUsers(()=>{});
            });

            self.$store.commit('setBreadcrumbs',[
                {label:'Users',name:''},
            ]);
        },
        watch: {
            'pagination.page':function(){
                this.loadUsers(()=>{});
            },
            'pagination.rowsPerPage':function(){
                this.loadUsers(()=>{});
            },
            'filters.name':_.debounce(function(){
                const self = this;
                self.loadUsers(()=>{});
            },700),
            'filters.email':_.debounce(function(){
                const self = this;
                self.loadUsers(()=>{});
            },700),
            'filters.groupId':_.debounce(function(){
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

                        axios.delete('/admin/users/' + user.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('USER_DELETED');

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
                }
            },
            loadUsers(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    email: self.filters.email,
                    group_id: self.filters.groupId.join(","),
                    page: self.pagination.page,
                    per_page: self.pagination.rowsPerPage
                };

                axios.get('/admin/users',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            },
            loadGroups(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/groups',{params: params}).then(function(response) {
                    self.filters.groupOptions = response.data.data;
                    cb();
                });
            }
        }
    }
</script>