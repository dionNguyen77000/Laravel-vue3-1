<template>
    <div class="fixed left-1 top-14 z-50">
      <button class="focus:outline-none navbar-burger" @click="toggleSidebar()">
        <br><svg class=" fill-current text-white h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
      </button>
    </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
   name: 'SidebarHamburger',
    computed: {
        ...mapState(['sideBarOpen'])
    },
    data() {
        return {
            dropDownOpen: false
        }
    },
    methods: {
        toggleSidebar() {
            this.$store.dispatch('toggleSidebar')
        }
    }
    
}
</script>