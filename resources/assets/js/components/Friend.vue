<!-- komponenta se kaci na vju profile.blade.php i u zavisnosti sta vrati check() metod FriendshipsControllera prikazuje odredjeni btn
    ili tekst(u zavisnosti od toga koji je status izmedju ulogovanog korisnika i korisnika ciji profil gleda) i takodje u metodima 
    add_friend() i accept_friend() salje AJAX-e istoimenim metodima FriendshipsControllera -->
<template>
  <div>
      <!-- dok traje ucitavanje varijabla loading je true tako da prikazujemo p u kom pise da se ucitava -->
      <p class="text-center" v-if="loading">
        Loadng...  
      </p>
      <!--ako je zavrseno ucitavanje tj ako je var loading == false-->
      <p class="text-center" v-if="!loading">
        <!-- ako je check() metod FriendshipsControlera vratrio 0 tj nisu prijatelji niti je neko poslao request za friendship prikazuje
             se btn Add Friend, na klik event pozivamo add_friend() metod ove komponente -->
        <button class="btn btn-success" v-if="status == 0" @click="add_friend">Add Friend</button>
        <!-- ako je check() metod FriendshipsControlera vratrio pending tj nisu prijatelji ali je useru koji je ulogovan poslat request 
             za friendship od usera ciji profil gleda, na klik event pozivamo metod accept_friend() ove komponente-->
        <button class="btn btn-success" v-if="status == 'pending'" @click="accept_friend">Accept Friend</button>
        <!-- ako je check() metod FriendshipsControlera vratrio waiting tj nisu prijatelji ali je user koji je ulogovan poslao request 
             za friendship useru ciji profil gleda-->
        <span v-if="status == 'waiting'">Waiting for response</span>
        <!-- ako je check() metod FriendshipsControlera vratrio friends tj prijatelji su-->
        <span v-if="status == 'friends'">Friends</span>
      </p>

  </div>
</template>
  
<script>


  export default {

    mounted() {
      //console.log('Component mounted.')
      //koristeci profile_user_id prop saljemo get request ruti /check_relationship_stauts/{id} koja ce izvuci podatke usera ciji smo -
      //id poslali iz 'users' tabele i kad nam odgovori console.log-ujemo te podatke
      this.$http.get('/check_relationship_stauts/' + this.profile_user_id)
      .then((resp) => {
        console.log(resp)
        //u varijablu status koja je definisana u data() upisujemo ono sto nam vrati check() metod FriendshipsControllera, odgovor se -
        //-nalazi u body.status u responsu
        this.status = resp.body.status  
        //podesavamo varijablu loading na false, takodje je varijabla definisana u data()  
        this.loading = false
      })
    },
    //prop profile_user_id je bajndovan u <friend> tagu u profile.blade.php i vrednost mu je id usera ciji profil trenutno gledamo 
    props: ['profile_user_id'],
    //ovde definisemo varijable koje cemo koristiti u komponenti
    data(){
      return {
        status: '',//popunjva se onom vrednoscu koju nam vrati check() metod ProfilesControllera i u zavisnosti od toga izbacujemo sta treba u template-u
        loading: true//kad stigne odgovor od check() metoda menja se ufalse i vise se ne prikazuje loading... u template  
      }  
    },
    //ovde pravimo metode tj handlere na klikove na AddFriend ili AcceptFriend btn-e
    methods: {

      //metod koji ce slati AJAX add_friend() metodu FriendshipsControlera preko rute add_friend/id koji ce onda pozivat metod add_friend()-
      //traita Friendable.php da upise red u 'friendships' tabelu gde ce requester bii user koji je kliknuo btn a user_requested onaj na ci
      //jem proflu smo kliknuli btn AddFriend
      add_friend(){
        //menjamo varijablu loading u true da bi useru na ekranu dok ceka da server upise red u 'friendships' tabelu pisalo Loading...
        this.loading = true
        //saljemo AJAx na rutu '/add_friend/id' sa idem usera koji je upisan u prop profle_user_id
        this.$http.get('/add_friend/' + this.profile_user_id)
        .then((r) => {
          console.log(r)
          //kad stigne odgovr i ako je 1 (znaci da je upisan red u 'friendships' tabelu)
          if(r.body == 1){
            //menjamo varijablu status u waiting da ne bi vise bio btn AcceptFriend na ekranu
            this.status = 'waiting'
            //podesavamo noty plugin da prikaze success poruku
            noty({
              type: 'success',
              layout: 'bottomLeft',
              text: 'Friend request sent.'
            })
            //vracamo loading varijablu u false da vise useru ne bi na ekranu pisalo Loading...
            this.loading = false
          }
        })
      },
      //metod se poziva kad se klikne btn AcceptFriend i salje AJAX metodu accept_friend() FriendshipsControllera preko rute accept_friend/id
      //metod accept_friend() FriendshipsControllera ce pozvati istoimeni metod traita Friendable.php koji ce da odradi sve i vrati 1 ako -
      //sve prodje u redu tj updateuje status kolonu 'friendships' tabele u 1 tamo gde je requester onaj ciji poziv prihvatamo a user_reques
      //ted onaj koji je kliknuo AceptFriend btn
      accept_friend(){
        //menjamo varijablu loading u true da bi useru na ekranu dok ceka da server upise red u 'friendships' tabelu pisalo Loading...
        this.loading = true
        //saljemo AJAX na rutu '/accept_friend/id' sa idem usera koji je upisan u prop profle_user_id, ruta gadja add_friend() metod-
        //FriendshipsControllera koji upisuje 1 u status kolonu 'friendships' tabele i poziva FriendRequestAccepted notification klasu da-
        //posalje mail, i broadcastuje na pusher.com i upise red u 'notifications' tabelu
        this.$http.get('/accept_friend/' + this.profile_user_id)
        .then((r) => {
          console.log(r)
          //kad stigne odgovr i ako je 1 (znaci da je upisan red u 'friendships' tabelu)
          if(r.body == 1){
            //menjamo varijablu status u friends da ne bi vise bio btn AcceptFriend na ekranu
            this.status = 'friends'
            //podesavamo noty plugin da prikaze success poruku
            noty({
              type: 'success',
              layout: 'bottomLeft',
              text: 'You are now friends.'
            })
            //vracamo loading varijablu u false da vise useru ne bi na ekranu pisalo Loading...
            this.loading = false
          }
        })
      }

    }


  }


</script>
