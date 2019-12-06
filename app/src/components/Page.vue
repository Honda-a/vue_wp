<template>
  <div class="Page">
    <div class="loading" v-if="loading">
      Loading...
    </div>

    <div v-if="error" class="error">
      {{ error }}
    </div>

    <div v-if="data" class="content">
      <h1>{{ data.title.rendered }}</h1>
      <div v-html="data.content.rendered"></div>
    </div>
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
      .get("http://wp.scan/wp/wp-json/wp/v2/posts?slug="+ this.$route.params.id)
      .then(response => {
          this.loading = false
          this.data = response.data[0]
          
        })
      .catch(error => {
          console.log(error)
          this.error = error
        });
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
