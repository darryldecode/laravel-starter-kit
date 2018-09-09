<template>
    <div class="page_wrap_vue pa-3">
        <transition name="fade">
            <router-view></router-view>
        </transition>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                active: ''
            }
        },
        mounted() {
            const self = this;
        }
    }
</script>