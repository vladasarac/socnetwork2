import Vuex from 'vuex'
import Vue from 'vue'

Vue.use(Vuex)

export const store = new Vuex.Store({
  
  state: {
  	nots: [], // ovde ubacujemo notifikacije nekog usera
    posts: [], // ovde ubacujemo postove prijatelja nekog usera koje prikazuje Feed.vue komponenta
    auth_user: {} //ovde ubacujemo poatke ulogovanog usera koje salje Init.vue
  },
  getters: {
    //ovaj getter vraca nots[] iz state sekcije onom ko ga pozove
    all_nots(state){
      return state.nots
    },
    //ovaj getter vraca broj notifikacija u nots[] tj nots.length onom ko ga pozove
    all_nots_count(state){
      return state.nots.length
    },
    //ovaj getter vraca posts[] array u kom su postovi prijatelja trenutno ulogovanog usera
    all_posts(state){ 
      return state.posts
    }
  },
  mutations: {
    //ovde u array nots[] ubacujemo notifikacije tj not koji dolazi iz get_unread() metoda UnreadNots.vue komponente, kao argumente prima-
    //-state u kom je array nots[] u koji treba pushovati ono sto stigne iz get_unread() metoda UnreadNots.vue komponente i not a to ce 
    //biti jedna neprocitana notifikacija posto ce get_unread() iz UnreadNots.vue iterirati kroz vracene neprocitane notifikacije i jednu
    //po jednu ih slati ovde
    add_not(state, not) {
      state.nots.push(not)
    },
    //ovde u array posts[] ubacujemo postove prijatelja trenutno ulogovanog usera koje vraca feed() metod FeedsControllera tj metod get_fe
    //ed Feed.vue komponente kad mu stigne odgovor na AJAX iterira kroz ono sto je stiglo i svaki post zasebno salje ovde da bude ubacen u 
    //posts[] array
    add_post(state, post){
      state.posts.push(post)
    },
    //ovde ubacujemo u auth_user objekat ono sto nam posalje metod get_auth_user_data() iz Init.vue tj podatke trenutno ulogovanog usera
    auth_user_data(state, user){
      state.auth_user = user
    },
    //mutator kog poziva like() metod Like.vue komponente da postu koji je lajkovan(iz posts[] arraya) doda lajk, u payload je ono  sto je -
    //-like() iz Like.vue poslao a to je id(payload.id) posta i like objekat sa user objektom tj podatcima usera koji je lajkovao(payload.like)
    update_post_likes(state, payload){
      //nalazimo post u posts[] arrayu po id koji je stigao 
      var post = state.posts.find( (p) => {
        return p.id === payload.id
      })  
      //sada ubacujemo lajk sa podatcima usera u posts[] array tj u post.likes(posto postovi imaju vise lajkova pa je ovo podarray)
      post.likes.push(payload.like)
    },
    //mutator kog poziva unlike() metod Like.vue da obrise iz posts[] tj iz posts.likes lajk koji je obrisan, u payload je id posta (payloa-
    //d.post_id) i id lajka koji je obrisan kog je vratio unlike() iz LikesControllera(payload.like_id)
    unlike_post(state, payload){
      //nalazimo post koji je neko unlikeovao
      var post = state.posts.find((p) => {
        return p.id === payload.post_id
      })
      //posto smo nasli post sada nalazimo njegov lajk kom id odgovara lajku koji je stigao iz Like.vue tj iz unlike() metoda LikesControllera
      var like = post.likes.find((l) => {
        return l.id === payload.like_id
      })
      //nalazimo index lajka koji hocemo da obrisemo u post.likes
      var index = post.likes.indexOf(like)
      //koristeci splice secamo ga iz post.likes arraya
      post.likes.splice(index, 1)
    }
    
  }

})







