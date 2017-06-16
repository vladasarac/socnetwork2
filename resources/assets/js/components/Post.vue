<!-- komponenta za kreiranje novog posta okacena je u <post> tagu u home.blade.php-->
<template>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">

          <div class="panel-body">
            <textarea rows="5" class="form-control" v-model="content"></textarea>
            <br>
            <!--klik event na button j vezan sa create_post() metodom koji salje AJAX store() metodu PostsControllera koji upisuje post
              u 'posts' tabelu-->
            <button class="btn btn-success pull-right" :disabled="not_working" @click="create_post()">
              Create a post
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    mounted() {
    	
    },
    data() {
      return {
        //variajbla u koju ubacujemo sta je user ukucao u <textarea> posto je bajndovana sa njom
        content: '',
        // varijabla koja kontrolise da li je submit btn disejblovan, po difoltu jeste a tek kad user ukuca nesto u textarea postaje false
        //i onda u <button> elementu disabled postaje false i moze se submitovati
        not_working: true
      }
    },
    methods: {
      //metod koji salje AJAX store() metodu PostsControllera koji ce upisati post u 'posts' tabelu preko rute /post/create
      create_post(){
        //saljemo AJAX na rutu /create/post sa userovim unosom u <textarea> tj saljemo content varijablu
        this.$http.post('/create/post', { content: this.content })
            // kad stigne odgovor od store() metoda PostsControllera
            .then((resp) => {
              // praznimo content varijablu tj <textarea> posto su bajndovani
              this.content = ''
              //izbacujemo useru noty poruku da je uspesno uneo post
              noty({
                type: 'success',
                layout: 'bottomLeft',
                text: 'Your post has been published!'
              })
              console.log(resp)  
            })
      }
    },
    watch: {
      //kad pocne da se kuca u <textarea> tj content varijabla koja je bajndovana sa njom postane duza od 0 not_working koji je po difoltu
      //true i samim tim disejbluje submit button postaje false i sad je moguce submitovati formu posto je disabled="false"
      content() {
        if(this.content.length > 0)
          this.not_working = false
        else
          this.not_working = true   
      }
    } 	
  }	
</script>
