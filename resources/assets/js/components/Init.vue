<template>
  <div>
  	<!--ova komponenta samo vadi podatke usera koje joj vraca ruta /get_auth_user_data i onda ih upisuje u auth_user objekat u store.js,
  	-komponenta je okacene u app.blade.php layout-->
  </div>	
</template>

<script>
  export default {

  	mounted() {
  	  //pozivamo metod get_auth_user_data()
  	  this.get_auth_user_data()  	
  	},
  	methods: {
  	  //metod sluzi da uzme podatke trenutno ulogovanog usera
  	  get_auth_user_data(){
  	  	//saljemo AJAX ruti /get_auth_user_data koja u closeure-u vraca Auth::usera() tj njegove podatke
  	  	this.$http.get('/get_auth_user_data')
  	  	  .then((resp) => {
  	  	  	console.log(resp)
  	  	  	//ubacujemo vraceno u store.js u user_data objekat pozivajuci mutator auth_user_data
  	  	  	this.$store.commit('auth_user_data', resp.body)
  	  	  })
  	  }	
  	}

  }	
</script>

