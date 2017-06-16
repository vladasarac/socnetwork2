<template>
  <!--komponenta kojom pozivamo algolia.com koji pretrazuje dinamicno 'users' tabelu tj index users koji smo uploadovali tamo, kaci se -
  -na app.blade.php layout-->
  <div class="container">
  	<div class="row">
  	  <div class="col-lg-6 col-lg-offset-3">
  	    <span>algolia.com search</span>
  	    <!--polje u koje unosimo pojam za pretragu 'users' tabele tj indexa users na algolia.com, bajndovan je sa query iz data sekcije-
  	    -tj ono sto unesemo u input ce napuniti query varijablu koju cemo slati algolia.com-u da pretrazi index users-->
  	  	<input type="text" class="input-sm form-control" placeholder="search for other users" v-model="query">
  	  	<br>
  	  	<div class="row" v-if="results.length">
  	  	  <!--iteriramo kroz users[] u kom su useri koje je nasla alogia.com i prikazujemo ih-->
  	  	  <div class="text-left" v-for="user in results">
  	  	    <a :href="'/profile/' + user.slug" target="blank"><!--ja dodo da bude link-->
  	  	      <img :src="user.avatar" width="40px" height="40px" style="border-radius: 50%;">
  	  	      <span class="text-center">
                {{ user.name }}             
              </span><br>	
  	  	    </a>
            <!-- ja dodo proveravamo da li id usera koji je trenutno u iteraciji u friendsids arrayu i ako jeste pisemo 
            ispod njegove slike da je friend-->
            <span v-if="friendsids.indexOf(user.id) != -1">
              | Friend |
            </span>
            <!--ako nismo prijatelji a poslali smo mu poziv za prijateljstvo tj pendingfriendrequestssentids() metod Friendable.php traita 
            vrati u arrayu id ovog usera pisemo 'waiting for response'-->
            <span v-else-if="pendingfriendsrequestsids.indexOf(user.id) == -1 && friendsids.indexOf(user.id) == -1 && pendingfriendrequestssentids.indexOf(user.id) != -1">
              | Waiting for response |       
            </span>
            <!--ako nisu prijatelji tj nije u friends[] i nismo mi njemu poslali poziv tj nije u pendingfriendrequestssentids[] ali jeste-
            -u pendingfriendsrequestsids[] tj on je nama poslao poziv izbacujemo AcceptFriend btn koji takodje kao i AddFriend btn poz-
            -iva metod add_friend() pa onda add_friend() Friendable traita koji ce ako postoji poziv samo upisati 1 u status kolonu
            pogledaj add_friend() Friendable traita pa ce biti jasno...-->
            <span v-else-if="pendingfriendsrequestsids.indexOf(user.id) != -1 && friendsids.indexOf(user.id) == -1 && pendingfriendrequestssentids.indexOf(user.id) == -1">
              <button class="btn btn-info btn-xs" :id="user.id" :value="user.id" @click="add_friend">Accept Friend</button>      
            </span>
            <!--ako user nije ni u jedom od ovih array tj nismo prijatelji niti jeo on nama niti mi njemu poslali poziv izbacuje se btn-
            -AddFriend koji poziva add_friend() metod koji posle preko rute '/add_friend' poziva add_friend() iz Friendable.php traita-->
            <span v-else-if="pendingfriendsrequestsids.indexOf(user.id) == -1 && friendsids.indexOf(user.id) == -1 && pendingfriendrequestssentids.indexOf(user.id) == -1">
              <button class="btn btn-success btn-xs" :id="user.id" :value="user.id" @click="add_friend">Add Friend</button>       
            </span> <hr>
            
            <!-- dovde -->
  	  	  </div>
          <p class="text-center"><a @click="moresearch">Show More Results</a></p>
          <hr>
  	  	</div>
  	  </div>	
  	</div>
  </div>

</template>

<script>
  //ovo je sa algolia.com - https://www.algolia.com/doc/api-client/javascript/getting-started/#quick-start
  var algoliasearch = require('algoliasearch');
  //inicijalizacija klijenta, ovo je sa algolia.com - https://www.algolia.com/doc/api-client/javascript/getting-started/#quick-start
  //parametri su appid i appsecret iz .env fajla a njih sam skinuo sa dashboarda na algolia.com
  var client = algoliasearch('USM82GJUTB', '5d45df7600b4504a6e6703cb11ea5820');
  //ovde govorimo algolia.com koji index da pretrazuje posto tamo mozemo prebaciti vise tabela,ovde mu govrimo da pretrazuje users
  var index = client.initIndex('users');
  export default {
  	mounted(){
  	  //probno pretrazivanje indexa users na algolia.com, pretrazujemo po pojmu 'vlada'  ako nadje console.log-ovace sta nadje ili error
  	  // index.search('vlada', (err, content) => {
  	  // 	console.log(err, content)
  	  // })
  	  //this.search()
  	},
  	data(){
  	  return {
  	  	query: '',//ovde ubacujemo sta je user uneo u input za pretragu 'users' tabele
  	  	results: [], //array u koji ubacujemo ono sto pronadje algolia.com
        friendsids: [],//ovde cemo ubaciti id-eve prijatelja ulogovanog usera da bi znal koji btn da se prikaze posle searcha
        //ovde cemo ubaciti id-eve onih koji su add-ovali ulogovanog usera da bi znal koji btn da se prikaze posle searcha
        pendingfriendsrequestsids: [],
        //ovde cemo ubaciti id-eve onih koje je add-ovao ulogovani user da bi znal koji btn da se prikaze posle searcha
        pendingfriendrequestssentids: [],
        authuser: '',
  	  }	
  	},
  	methods: {
  	  //metod kojim pozivamo pretragu 'users' tabele tj indexa users na algolia.com
  	  search(){
  	  	//pozivamo algolia.com i kao parametar po kom pretrazuje index users saljemo userov unos u input tj query varijablu
        //ja dodo da vraca samo 4 rezultata -  {hitsPerPage: 4}
  	  	index.search(this.query, {hitsPerPage: 20}, (err, content) => {
  	  	  //console.log(err, content)
  	  	  //u array results iz data sekcije ubacujemo ono sto nadje algolia.com a to je u hits sekciji objekta koji vraca
  	  	  this.results = content.hits
  	    });
        //ja dodo da pozove rutu '/friendsids' koja vraca array sa id-evima trenutno ulogovanog usera da bih moga u <template> da za sva-
        //kog usera proverim da li je njegov id u tom arrayu i ako nije da pored dodam btn AddFriend koji ce pozivati metod add_friend()
        this.$http.get('/friendsids')
          .then((response) => {
            //console.log(response)
            this.friendsids = response.body
            //console.log(this.friendsids)
          }); 
        //ja dodo da pozove rutu '/pendingfriendsrequestsids' koja vraca array sa id-evima usera koji su ulogovanom useru poslali poziv za-
        //prijateljstvo, podatke ubacujemo u array pendingfriendsrequestsids da bi znali koji btn da se prikaze ispod usera posle algolia searcha
        this.$http.get('/pendingfriendsrequestsids')
          .then((response1) => {
            //console.log(response1)
            this.pendingfriendsrequestsids = response1.body
            //console.log(this.pendingfriendsrequestsids)
          }); 
        //ja dodo da pozove rutu '/pendingfriendrequestssentids' koja vraca array sa id-evima usera kojma je ulogovani user poslao pozv za -
        //prijateljstvo, podatke ubacujemo u array pendingfriendrequestssentids da bi znali koji btn da se prikaze ispod usera posle algolia searcha
        this.$http.get('/pendingfriendrequestssentids')
          .then((response2) => {
            console.log(response2)
            this.pendingfriendrequestssentids = response2.body
            console.log(this.pendingfriendrequestssentids)
          });  
  	  },
      //ja dodo, metod se poziva kad se klikne btn AddFriend koji se pojavi pored usera kog nam vrati algolia.com a koji nam nije prijatelj
      //metod salje request /ruti add_friend koja ide na add_friend() metod FriendshipsController-a
      add_friend(event){
        //alert(event.target.value)
        //event.target.display = false
        this.$http.get('/add_friend/' + event.target.value)
          .then((r) => {
            console.log(r)
            if(r.body == 1){
            //podesavamo noty plugin da prikaze success poruku
            noty({
              type: 'success',
              layout: 'bottomLeft',
              text: 'Friend request sent.'
            })
            //uklanjamo kliknuti AddFriend button
            document.getElementById(event.target.value).remove();
          }
          })
      },
      moresearch(){

      }	  	
  	},
  	//ja dodo kod njega mora da se klikne enter da se pozove search() metod a kod mene poziva cim pocne nesto da se unosi u input za pretra-
  	//-gu tj cim je duzina query varijable koja je bajndovana sa inputom > 0
  	watch: {
  	  query(){
  	  	if(this.query.length > 0)
          this.search()
  	  }	
  	}
  }	
</script>
















