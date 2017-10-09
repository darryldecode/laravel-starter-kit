<template>
    <div>
        DASHBOARD VUE
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('pages.Home.vue');

            const self = this;

            setTimeout(()=>{
                self.$store.commit('hideLoader');
            },1500);
        }
    }
</script>