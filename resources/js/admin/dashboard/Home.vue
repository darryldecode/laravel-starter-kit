<template>
    <div class="page_wrap_vue pa-3">
        <h2>HOME PAGE</h2>
        <p>I am located at <b>resources/js/admin/dashboard/Home.vue</b></p>
        <p>single page routes for admin can be found at <b>resources/js/admin/router.js</b></p>
    </div>
</template>

<script>
    export default {
        mounted() {

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Dashboard',name:''}
            ]);
        }
    }
</script>