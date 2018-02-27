<template>
    <div class="page_wrap_vue users_view">
        <v-tabs color="primary" dark v-model="active">

            <v-tab href="#users" ripple>
                Users
            </v-tab>
            <v-tab href="#groups" ripple>
                Groups
            </v-tab>
            <v-tab href="#permissions" ripple>
                Permissions
            </v-tab>

            <v-tab-item id="users">
                <v-card flat>
                    <v-card-text>
                        <user-lists></user-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item id="groups">
                <v-card flat>
                    <v-card-text>
                        <group-lists></group-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item id="permissions">
                <v-card flat>
                    <v-card-text>
                        <permission-lists></permission-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
    import UserLists from '../components/user/UserLists.vue';
    import GroupLists from '../components/user/GroupLists.vue';
    import PermissionLists from '../components/user/PermissionLists.vue';
    export default {
        components: {
            UserLists,
            GroupLists,
            PermissionLists
        },
        data() {
            return {
                active: ''
            }
        },
        watch: {
            active(v) {
                console.log('active tab: ' + v);
            }
        },
        mounted() {
            console.log('pages.Users.vue');

            const self = this;
        }
    }
</script>