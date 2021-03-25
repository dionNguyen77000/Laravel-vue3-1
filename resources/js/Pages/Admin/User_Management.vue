<template>
  <!-- <div class="leading-normal tracking-normal" id="main-body"> -->
   
        <UserMContent />
    
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