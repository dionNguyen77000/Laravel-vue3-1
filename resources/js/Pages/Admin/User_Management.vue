<template>
  <!-- <div class="leading-normal tracking-normal" id="main-body"> -->
    <div class="flex flex-col md:flex-row">

      <div :class="sideBarOpen ? 'flex-none' : 'hidden'" >
      <Sidebar />
      </div>
      <div :class="sideBarOpen ? 'flex-auto' : 'w-full'">
        <UserMContent />
      </div>
    </div>
</template>



<script>

import { mapState } from 'vuex'
import UserMContent from './UserMContent.vue'
export default {
  
 computed: {
    ...mapState(['sideBarOpen'])
  },

  components: {
    UserMContent
  }
    
}

</script>