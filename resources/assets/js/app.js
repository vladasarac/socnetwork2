
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//ovde registrujemo komponentu Init.vue iz 'socnetwork2/resources/assets/js/components'
Vue.component('init', require('./components/Init.vue'));
 //ovde registrujemo komponentu Feed.vue iz 'socnetwork2/resources/assets/js/components'
Vue.component('feed', require('./components/Feed.vue'));
//ovde registrujemo komponentu Post.vue iz 'socnetwork2/resources/assets/js/components'
Vue.component('post', require('./components/Post.vue'));
//ovde registrujemo komponentu Search.vue iz 'socnetwork2/resources/assets/js/components'
Vue.component('search', require('./components/Search.vue'));
//ovde registrujemo komponentu Friend.vue iz 'socnetwork2/resources/assets/js/components'
Vue.component('friend', require('./components/Friend.vue'));
//registrujemo komponentu UnreadNots.vue iz 'socnetwork2/resources/assets/js/components'
Vue.component('unread', require('./components/UnreadNots.vue'));
//ovde registrujemo komponentu Notification.vue iz 'socnetwork2/resources/assets/js/components'
Vue.component('notification', require('./components/Notification.vue'));
//uvozimo store.js to je neki vuex fajl...
import { store } from './store'

const app = new Vue({
    el: '#app',
    store
});
