<template>
    <div class="page_wrap_vue pa-3">
        <h2>HOME PAGE</h2>
        <p>I am located at <b>resources/assets/js/admin/pages/dashboard/Home.vue</b></p>
        <p>single page routes can be found at <b>resources/assets/js/router</b></p>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('pages.Home.vue');

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Dashboard',name:''}
            ]);
        }
    }
</script>