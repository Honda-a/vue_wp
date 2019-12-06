import Vue from 'vue'
import Router from 'vue-router'
import Page from '@/components/Page'
import FrontPage from '@/components/FrontPage'
import axios from 'axios'

Vue.use(Router)
Vue.prototype.$http = axios;

export default new Router({
  routes: [
    {
      path: '/',
      name: 'front_page',
      component: FrontPage
    },
    {
      path: '/page/:id',
      name: 'Page',
      component: Page
    }
  ]
})
