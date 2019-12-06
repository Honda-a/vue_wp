
<template>
    <div> 
    <h2>Nav Bar</h2> 
    <nav> 
        <ul>
            <li v-for="item in data">
                <router-link :to="'/page/' + item.slug">{{ item.title }}</router-link>
            </li>
        </ul>
        
    </nav> 
    </div>
</template>

<script>
export default {
  data () {
    return {
      loading: false,
      data: null,
      error: null
    }
  },
  created () {
    // fetch the data when the view is created and the data is
    // already being observed
    this.fetchData()
  },
  watch: {
    // call again the method if the route changes
    '$route': 'fetchData'
  },
  methods: {
    fetchData () {
      this.error = this.data = null
      this.loading = true
      // replace `getPost` with your data fetching util / API wrapper
      this.$http
      .get("http://wp.scan/wp/wp-json/wp/v2/posts")
      .then(response => {
          this.loading = false
          var raw_data = []
          response.data.map(function (value, key){
              raw_data.push({
                  "title": value.title.rendered,
                  "slug": value.slug
              })
          })
          
          this.data = raw_data
          
        })
      .catch(error => {
          console.log(error)
          this.error = error
        });
    }
  }
}
</script>
