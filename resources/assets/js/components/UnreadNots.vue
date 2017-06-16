<!-- komponenta prikazuje koliko user ima neprocitanih notifikacija i kaci se u app.blade.php layoutu -->
<template>
  <li>
  	<a href="/notifications">
  	  Unread Notifications
  	  <!-- pozivamo all_nots_count computed property koji od all_nots_count gettera iz store.js dobija broj neprocitanih notifikacija
  	    ulogovanog usera tj duzinu arraya nots[] u kom su neprocitane notifikacije ovog usera -->
  	  <span class="badge">{{ all_nots_count }}</span>	
  	</a>
  </li>	
</template>

<script>
  export default {
  	mounted(){
  	  //na mounted event pozivamo metod get_unread() koji je napravljen u methods sekciji koji salje AJAX i dobija broj neprocitanih 
  	  // notifikacija usera
  	  this.get_unread()
  	},
  	methods: {
  	  // metod za sada salje AJAX ruti get_unread koja ce vratiti neprocitane notifikacije trenutno ulogovanog usera iz notifications tabele
  	  // tj one kojimea je read_at kolona == NULL i zatim ih salje u add_not() mutator u store.js da ih ubaci u nots[] array
  	  get_unread(){
  	    this.$http.get('/get_unread')
  	        .then((nots) => {
  	          //console.log(nots)	
  	          //neprocitane notifikacije se nalaze u nots.body objektu i ovde iteriramo kroz njih i saljemo ih u add_not() metod tj mutat-
  	          //-or u store.js koji ce ih ubaciti u nots[] array 
  	          nots.body.forEach( (not) => {
  	          	this.$store.commit('add_not', not)
  	          })
  	        })	
  	  }	
  	},
  	computed: {
  	  //ovaj computed property poziva all_nots_count() getter iz store.js da mu vrati broj neprocitanih notifikacija ulogovanog user
  	  all_nots_count(){
  	  	return this.$store.getters.all_nots_count
  	  }	
  	} 
  }	
</script>



