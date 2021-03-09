<template>

    <div class="m-2">
      <nav class=" relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <a 
        href="#" 
        class="relative border border-gray-300 inline-flex items-center px-1 py-1.5 rounded-l-md  bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        @click.prevent="switched(meta.current_page - 1)"
        >
          <span>&laquo; Prev</span> 
        </a>
        <a  v-for="x in meta.last_page"  :key = "x" :class="{ 'bg-blue-400': meta.current_page === x}"
        href="#" class="relative inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-blue-200"
        @click.prevent="switched(x)"
        >
          {{ x }}
        </a>
        <a href="#" 
        class="relative inline-flex items-center px-1 py-1.5 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        @click.prevent="switched(meta.current_page + 1)"
        >
          <span>Next &raquo;</span> 
        </a>
      </nav>
    </div>
</template>

<script>
    export default {
        props: [
            'meta'
        ],
        methods: {
            switched (page) {
              if (this.pageIsOutOfBounds(page)) {
                  return;
              }
              this.$emit('pagination',page)
               
                // this.$router.replace({
                //     query: Object.assign({}, this.$route.query, { page: page })
                // })
            },
            pageIsOutOfBounds (page) {
                return page <= 0 || page > this.meta.last_page
            }
        }
    }
</script>
