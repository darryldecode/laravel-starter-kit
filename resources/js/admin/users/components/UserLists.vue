<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card class="pt-3">
            <div class="d-flex flex-row">
                <div class="flex-grow-1 pa-2">
                    <v-btn @click="$router.push({name:'users.create'})" class="primary lighten-1" dark>
                        New User
                        <v-icon right dark>add</v-icon>
                    </v-btn>
                </div>
                <div class="flex-grow-1 pa-2">
                    <v-btn @click="$router.push({name:'users.groups.list'})" class="primary lighten-1 float-right" dark>
                        Manage Groups <v-icon right dark>group</v-icon>
                    </v-btn>
                    <v-btn @click="$router.push({name:'users.permissions.list'})" class="primary lighten-1 float-right mr-2" dark>
                        Manage Permissions <v-icon right dark>vpn_key</v-icon>
                    </v-btn>
                </div>
            </div>
            <div class="d-flex flex-lg-row flex-sm-column">
                <div class="flex-grow-1 pa-2">
                    <v-text-field filled prepend-icon="search" label="Filter By Name" v-model="filters.name"></v-text-field>
                </div>
                <div class="flex-grow-1 pa-2">
                    <v-text-field filled prepend-icon="search" label="Filter By Email" v-model="filters.email"></v-text-field>
                </div>
                <div class="flex-grow-1 pa-2">
                    <v-autocomplete filled
                                    multiple
                                    chips
                                    deletable-chips
                                    clearable
                                    prepend-icon="filter_list"
                                    label="Filter By Groups"
                                    :items="filters.groupOptions"
                                    item-text="name"
                                    item-value="id"
                                    v-model="filters.groupId"
                    ></v-autocomplete>
                </div>
            </div>
        </v-card>
        <!-- /search -->
        <v-divider class="pb-2"/>

        <!-- data table -->
        <v-data-table
                hide-default-header
                v-bind:headers="headers"
                :options.sync="pagination"
                :items="items"
                :server-items-length="totalItems"
                class="elevation-1">
            <template v-slot:header="{props:{headers}}">
                <thead>
                <tr>
                    <th v-for="header in headers">
                        <div v-if="header.value=='name'" :class="`text-${header.align}`"><v-icon>mdi-person</v-icon> {{header.text}}</div>
                        <div v-else-if="header.value=='email'" :class="`text-${header.align}`"><v-icon>mdi-email</v-icon> {{header.text}}</div>
                        <div v-else-if="header.value=='permissions'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                        <div v-else-if="header.value=='groups'" :class="`text-${header.align}`"><v-icon>mdi-group</v-icon> {{header.text}}</div>
                        <div v-else-if="header.value=='last_login'" :class="`text-${header.align}`"><v-icon>mdi-av_timer</v-icon> {{header.text}}</div>
                        <div v-else :class="`text-${header.align}`">{{header.text}}</div>
                    </th>
                </tr>
                </thead>
            </template>
            <template v-slot:body="{items}">
                <tbody>
                    <tr v-for="item in items" :key="item.id">
                        <td>
                            <div class="ml-n1 my-1 d-flex justify-space-between align-content-space-around flex-wrap">
                                <v-btn @click="$router.push({name:'users.edit',params:{id: item.id}})" class="ma-1" small outlined icon color="info">
                                    <v-icon small>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn @click="trash(item)" class="ma-1" small outlined icon color="red">
                                    <v-icon small>mdi-delete</v-icon>
                                </v-btn>
                            </div>
                        </td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.email }}</td>
                        <td>
                            <v-btn small @click="showDialog('user_permissions',item.permissions)" outlined rounded color="grey" dark>Show</v-btn>
                        </td>
                        <td>
                            <v-chip v-for="group in item.groups" :key="group.id" outlined color="grey" text-color="white">
                                {{group.name}}
                            </v-chip>
                        </td>
                        <td>{{ $appFormatters.formatDate(item.last_login) }}</td>
                        <td class="text-center">
                            <v-avatar outlined>
                                <v-icon v-if="item.active!=null" class="green--text">check_circle</v-icon>
                                <v-icon class="grey--text" v-else>error_outline</v-icon>
                            </v-avatar>
                        </td>
                    </tr>
                </tbody>
            </template>
        </v-data-table>

        <v-divider class="py-5"/>

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showPermissions.show" absolute max-width="300px">
            <v-card>
                <v-card-title>
                    <div class="headline"><v-icon>vpn_key</v-icon> User Permissions</div>
                </v-card-title>
                <v-card-text>
                    <v-chip v-for="(permission,key) in dialogs.showPermissions.items" :key="key" class="white--text ma-1" :class="{'green':(permission.value==1),'red':(permission.value==-1),'blue':(permission.value==0)}">
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
                    { text: 'Active', value: 'active', align: 'center', sortable: false },
                ],
                items: [],
                totalItems: 0,
                pagination: {
                    itemsPerPage: 10
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
                {label:'Users',to:{name:'users.list'}},
            ]);
        },
        watch: {
            pagination: {
                handler () {
                    this.loadUsers(()=>{});
                },
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
                    icon: 'warning',
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
                    per_page: self.pagination.itemsPerPage
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
