<!-- komponenta prikazuje notifikacije koje stizu preko pusher.com kad neko nekog doda za prijatelja ili neko prihvati neciji
    poziv za prijateljstvo, notifikacije broadcastuju NewFriendRequest.php i FriendRequestAccepted.php a komponenta se kaci na 
    app.blade.php layout -->
<template>

  <div>
  	
  </div>	

</template>

<script>
  export default {

    // kad je mountovana komponenta pozivamo listen() metod koji pravimo u methods sekciji
  	mounted(){
  	  this.listen()	
  	},
    //ovde primamo id trenutno ulogovanog usera koji stize iz layouta app.blade.php koji kaci ovu komponentu 
  	props: ['id'],
    //  
  	methods: {
  	  //u ovom metodu pozivamo laravel Echo da valjda broadcastuje pusheru sta treba ako sam dobro shvatio i kad stigne odgovor onda radi
      //sta treba
  	  listen(){
  	  	Echo.private('App.User.' + this.id)
  	  		.notification( (notification) => {
  	  		  //alert('new notification')
            //pozivamo noty paket da prikaze na ekranu notification koji je broadcastovan i stigao preko pusher.com
            noty({
              type: 'success',
              layout: 'bottomLeft',
              text: notification.name + notification.message
            })
            //ovde da bi bilo dinamicno tj da bi odmah useru povecali broj neprocitanih notifikacija pozivamo metod tj mutator add_not iz 
            //store.js i sljemo mu notification kao argment da ga ubaci u nots[] array
            this.$store.commit('add_not', notification)
            //pusti audio sempl, okacen je u app.blade.php layoutu
            document.getElementById("noty_audio").play()
  	  		  console.log(notification)	
  	  		})
  	  }	

  	}

  }	
</script>















