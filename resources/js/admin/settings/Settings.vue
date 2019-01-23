<template>
    <div class="page_wrap_vue pa-3">
        <h2>SETTINGS PAGE</h2>
        <p>I am located at <b>resources/js/admin/settings/Settings.vue</b></p>
        <p>single page routes for admin can be found at <b>resources/js/admin/router.js</b></p>
    </div>
</template>

<script>
    export default {
        mounted() {

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Settings',name:''}
            ]);
        }
    }
</script>