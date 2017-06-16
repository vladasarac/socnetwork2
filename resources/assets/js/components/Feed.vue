<!--Komponenta je okacena u home.blade.php i sluzi da prikaze postove prijatelja trenutno ulogovannog usera i takodje ima child kompone-
-ntu Like.vue koju poziva za svaki post koji prikazuje da prikaze ispod posta ko ga je lajkovao tj njihovu sliku koja je link ka profilu-->

<template>
  <div class="container">
  	<div class="row">
  	  <div class="col-lg-10 col-lg-offset-1">
  	    <!--iteriramo kroz posts[] array koji stize kad compued propertu posts() pozove all_posts getter iz store.js i prikazujemo postove
  	        i njihove kreatore-->
  	  	<div class="panel panel-default" v-for="post in posts">
  	  	  <div class="panel-heading">
            <!--ja dodo link ka profilu usera ciji je post-->
            <a :href="'/profile/' + post.user.slug" target="blank">
              <!-- avatar usera koji je kreirao post -->
              <img :src="post.user.avatar" width="40px" height="40px" class="avatar-feed">
              <!-- ime usera koji je kreirao post -->
              {{ post.user.name }}
              <!-- vreme kreiranja posta -->  
            </a> 
  	  	    <span class="pull-right">{{ post.created_at }}</span>
  	  	  </div>
  	  	  <div class="panel-body">
  	  	  	<p class="text-center">
  	  	  	<!-- sadrzaj tj content posta -->
  	  	  	  {{ post.content }}	
  	  	  	</p>
            <!--ja dodo da ako post ima lajkova tj ako je post.likes vece od 0 stampa ovo-->
            <p v-if="post.likes.length > 0"><i>People who liked this post:</i></p>
            <!--ja dodo a ako nema stampa ovo-->
            <p v-else><i>Nobody liked this post...</i></p>
            <!--ovde kacimo komponentu Like.vue iz 'socnetwork2/resources/assets/js/components' koja je child ove komponente, komponenti
            saljemo id posta koji je trenutno u iteraciji tj koji se prikazuje-->
            <like :id="post.id"></like>
  	  	  </div>	
  	  	</div>
  	  </div>	
  	</div>
  </div>	
</template>

<script>
  //ovde importujemo komponentu Like.vue iz 'socnetwork2/resources/assets/js/components' koja je child ove komponente i u njoj su btn za like
  //i unlike i prikaz usera koji su lajkovali neki post
  import Like from './Like.vue'

  export default {

    mounted(){
      //pozivamo metod get_feed() koji je definisan u methods sekciji koji salje AJAX feed() metodu FeedsControllera koji vadi sve postove
      //prijatelja trenutno ulogovanog usera i vraca ih ovde da budu prikazani, takodje pored svakog posta vadi i podatke usera koji ga je 
      //kreirao
      this.get_feed()	
    },
    //ovo smo dodali kad smo importovali Like.vue komponentu koja je child ove komponente
    components: {
      Like
    },
    methods: {
      //metod salje AJAX feed() metodu FeedsControllera da izvuce sve postove prijatelja trenutno ulogovanog usera i zatim iterira kroz vra-
      //ceni array i svaki post zasebno salje u store.js da bude ubacen u posts[] array
      get_feed(){
        this.$http.get('/feed')
          .then((response) => {
            console.log(response)
            //iteriramo kroz vracene postove iz feed() metoda FeedsControllera i svaki saljemo u mutator add_post() iz store.js da ga uba-
            //-ci u array posts[]
            response.body.forEach((post) => {
            	this.$store.commit('add_post', post)
            })
          })
      }

    },
    computed: {
      //pozivamo getter all_posts() iz store.js da nam posalje array posts[] u kom su svi postovi koje je vratio feed() iz FeedsControllera-
      //i onda cemo u <template> - u iterirati kroz posts array i prikazati svaki post i njegovog kreatora
      posts(){
      	return this.$store.getters.all_posts
      }	
    }

  }	
</script>

<style>
  .avatar-feed{
  	border-radius: 50%;
  }
</style>











