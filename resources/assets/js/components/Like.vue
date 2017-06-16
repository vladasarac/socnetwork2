<template>
  <!-- ova komponenta je child Feed.vue komponente koja je kaci i ovde pravimo funkcionalnost lajkovanja postova koje prikazuje komponenta-
       -Feed.vue i takodje prikaz usera koji su lajkovali neki post -->
  
  <div>

    <!-- ja dodo da prikazuje div sa userima koji su lajkovali samo ako post ima neke lajkove tj ako je post.likes.length > 0 -->
    <div v-if="post.likes.length > 0">
    <div class="panel plava">
      <!--iteriramo kroz lajkove posta koji se trenutno prikazuje u Feed.vue(koji smo izvukli iz store.js u computed property-u post()) i ako ih ima prikazujemo ih, to je moguce posto kad feed() iz FeedsControllera posalje postove u Feed.vue sa njima sa salje i lajkove po-
      sta a sa lajkovima posta i podatke usera koji je lajkovao, to je napravljeno u modelima -eager loading-->
      <!-- ja dodo klasu plava da bi prikazao plavi background iza avatara usera koji su lajkovali post, takodje u lekcijama je umesto span
           koristio p ali mi se vise svidja ovako posto je inline -->
      <span class="text-center plava" v-for="like in post.likes">
      <!-- ja dodo da ovo budu linkovi ka profilima usera koji su lajkovali post -->
        <a :href="'/profile/' + like.user.slug" target="blank">
          <!--prikazujemo avatar usera koji su lajkovali post ciji je id poslal Feed.vue komponenta kad je pozvala Like.vue-->
          <img :src="like.user.avatar" width="25px" height="25px" class="avatar-like" :title="like.user.name">      
        </a> 
      </span>    
    </div>    
    </div>
    
    <hr>
    <!--ako user nije lajkovao post koji trenutno prikazuje Feed.vue tj computted property auth_user_likes_post vraca false
    btn na klik poziva metod like()-->
    <button class="btn btn-primary btn-xs" v-if="!auth_user_likes_post" @click="like()">
      Like this post      
    </button>      
    <!--ako je user lajkovao post koji trenutno prikazuje Feed.vue tj computted property auth_user_likes_post vraca true
     btn na klik poziva metod unlike()-->
    <button class="btn btn-danger btn-xs" v-else @click="unlike()">
      Unlike this post      
    </button>
  </div>

</template>

<script>
  export default {
    mounted(){

    },
    //id salje parent koji je u Feed.vue i ovo je id posta koji trenutno prikazuje Feed.vue a ovde cemo prikazati njegove lajkove
    props: ['id'], 
    computed: {
      likers(){
        //array u koji cemo ubaciti id-eve usera koji su lajkovali post koji je trenutno u iteraciji u Feed.vue i poslat je ovde na prikaz
        var likers = []    
        //iteriramo kroz postove tj like-ove posta i ubacujemo id-eve usera koji su lajkovali post u array likers
        this.post.likes.forEach((like) => {
          likers.push(like.user.id)  
        })      
        return likers
      },
      auth_user_likes_post(){
        //ovde proveravamo da li u likers[] arrayu postoji id trenutno ulogovanog usera koji je u store.js upisan u auth_user.id, ako postoji-
        //vratice taj id tj to ce upisati u check_index varijablu a ako ga ne nadje u varijablu ce upisati -1, ovo nam treba da bi znali da-
        //-li da gore prikazemo Like btn(ako user nije lajkovao post tj nema njegovog id-a u likers arrayu) ili Unlike btn (ako ima njegov -
        //-id u likers arrayu)
        var check_index = this.likers.indexOf(
          this.$store.state.auth_user.id    
        )      
        if(check_index === -1) // ako je check_index == -1 tj nema id trenutno ulogovanog usera medju onima koji su lajkovali post
          return false
        else // ako ima id trenutno ulogovanog usera madju onima koji su lajkovali post tj lajkovao je post
          return true  
      },
      post(){
        //u ovom computed propertyu nekako(nije mi bas jasno kako) vadimo post iz store.js tj iz posts[] arraya u kom su svi postovi koji s-
        //-u stigli iz feed() metoda FeedsControllera, kom je id jednak id-u koji je u props tj onom koji je stigao iz Feed.vue kad u iter-
        //aciji stigao id posta posto se za svaki post koji prikazuje Feed.vue poziva ova kompoennta da prikaze lajkove    
        return this.$store.state.posts.find((post) => {
            return post.id === this.id
        })      
      }
    },
    methods: {
      //metod se poziva kad se klikne btn Like i salje AJAX like() metodu LikesControllera da upise red u 'likes' tabelu,kad primi odgovor
      //upisace lajk koji mu je vratio kontoler u posts[] tj posts.likes koji je u store.js preko mutatora update_post_likes()
      like(){
        //saljemo AJAX preko rute /like/id i u ajaxu kao parametar id posta koji je stigao iz Feed.vue u props kad je pozvana Like.vue
        this.$http.get('/like/' + this.id)
          .then( (resp) => {
            console.log(resp)
            //pozivamo mutautor update_post_likes iz store.js koji ce u posts.likes dodati like koji smo upravo upisali tako da ce odmah
            //biti prikazan i user koji je lajkvao post medju ostalima koji su ga lajkovali posto sa lajkom stizu i podatci usera
            this.$store.commit('update_post_likes', {
              id: this.id, //saljemo mutatoru id posta koji je lajkovan
              //saljemo mu takodje sta je vratio metod like() LikesControllera tj like iz 'likes' tabele sa userom iz 'users' tabele
              like: resp.body
            })
            //pozivamo noty paket da izbaci na ekran notifikaciju da je upisan lajk u 'likes' tabelu
            noty({
              type: 'success',
              layout: 'bottomLeft',
              text: 'You successfully liked the post!'
            })
          })  
      },
      //poziva se kad se klikne btn Unlike i salje AJAX unlike() metodu LikesControllera da obrise red u 'likes' tabeli
      unlike(){
        //saljemo AJAX preko rute /unlike/id i u ajaxu kao parametar id posta koji je stigao iz Feed.vue u props kad je pozvana Like.vue
        this.$http.get('/unlike/' + this.id)
          .then((response) => {
            //pozivamo mutator unlike_post() store.js-a da obrise iz posts[] tj posts.likes lajk ciji je id stigao u responsu
            this.$store.commit('unlike_post', {
              //mutatoru saljemo id posta u kom je lajk koji je obrisan
              post_id: this.id,
              //saljemo i id samog lajka koji je vratio unlike() metod  LikesControllera
              like_id: response.body
            })
            //pozivamo noty paket da izbaci na ekran notifikaciju da je izbrisan lajk u 'likes' tabeli
            noty({
              type: 'success',
              layout: 'bottomLeft',
              text: 'You successfully unliked the post!'
            })
          }) 
      }
    }  
  }      
</script>

<style>
  .avatar-like{
      border-radius: 50%;
  }
  .avatar-like:hover{
    /*width: 35px;
    height: 35px;*/
    border: 3px solid white;
}
  .plava{
      background-color: #dce7e5;
      padding: 5px;
  }
</style>


