<template>

<div class="position-relative">
	<div class="input-group mb-3">
	  <div class="input-group-prepend">
	    <button class="btn btn-outline-secondary country-phone dropdown-toggle d-flex align-items-center text-08" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <img v-if="country != 'Autre' && country != 'Pays'" :src="'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/flags/4x3/'+country.toLowerCase()+'.svg'" height="14" class="mr-2"/>
	    {{country}}
		</button>
	    <div class="dropdown-menu">
	      <a class="dropdown-item d-flex align-items-center" href="#" @click.prevent="change_country('MA',1)">
	      	<img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/flags/4x3/ma.svg" height="14" class="mr-2"/> Maroc
	  	  </a>
	      <!-- <a class="dropdown-item d-flex align-items-center" href="#" @click.prevent="change_country('DZ',1)">
	      	<img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/flags/4x3/dz.svg" height="14" class="mr-2"/> Algérie</a>
	      <a class="dropdown-item d-flex align-items-center" href="#" @click.prevent="change_country('TN',1)">
	      	<img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/flags/4x3/tn.svg" height="14" class="mr-2"/> Tunisie</a>
	      <a class="dropdown-item d-flex align-items-center" href="#" @click.prevent="change_country('EG',1)">
	      	<img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/flags/4x3/eg.svg" height="14" class="mr-2"/> Egypte</a> -->
	      	<a class="dropdown-item d-flex align-items-center" href="#" @click.prevent="change_country('FR',1)">
	      	<img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/flags/4x3/fr.svg" height="14" class="mr-2"/> France</a>
	      	<div class="dropdown-divider"></div>

	      	<a class="dropdown-item d-flex align-items-center" href="#" @click.prevent="change_country('Autre',1)">Autre pays</a>
	    </div>
	  </div>
	  <the-mask :masked="true" :class="[ errors.has('gsm') ? 'is-invalid' : '', large ? 'form-control-lg' : '']" v-validate="'required|numeric|min:8'" name="gsm" class="form-control"  :value="inputVal" :mask="mask" @input="emit_phone" @focus.native="apply_mask()" v-model="inputVal" :placeholder="placeholder" data-vv-as="Numéro GSM" ></the-mask>
	</div>
</div>
</template>
<script>
import VueTheMask from 'vue-the-mask';
var Vue = require('vue');
Vue.use(VueTheMask);

export default {

	props: ['mynumber','large'],

	data() {

		return {
			    mask: '',
			    inputVal: '',
			    country: '',
			    placeholder: ''
			}
	},

	methods: {
		change_country: function(val,nullit){

			this.country = val;
			this.$emit('phonefc', null);

			if(nullit){
				this.inputVal = '';
			}

			if(val == 'MA'){
				this.placeholder = '+(212)661-00-00-00';
				this.mask = "+(212)###-##-##-##";

			} else if(val == 'TN'){
				this.placeholder = '+(216)91-00-00-00';
				this.mask = "+(216)##-###-###";

			} else if(val == 'DZ'){
				this.placeholder = '+(213)6-00-00-00-00';
				this.mask = "+(213)#-##-##-##-##";

			} else if(val == 'EG'){
				this.placeholder = '+(20)11-0000-0000';
				this.mask = "+(20)##-####-####";

			} else if(val == 'FR'){
				this.placeholder = '+(33)1-00-00-00-00';
				this.mask = "+(33)#-##-##-##-##";

			}  else if(val == 'Autre'){
				this.placeholder = '+0000000000';
				this.mask = "+#################";
			}
		},

		apply_mask: function() {
			if(this.country == 'MA' && this.inputVal == null){
				this.inputVal = "+(212)";
			} else if(this.country == 'TN' && this.inputVal == null){
				this.inputVal = "+(216)";
			} else if(this.country == 'DZ' && this.inputVal == null){
				this.inputVal = "+(213)";
			} else if(this.country == 'EG' && this.inputVal == null){
				this.inputVal = "+(20)";
			} else if(this.country == 'FR' && this.inputVal == null){
				this.inputVal = "+(33)";
			} else if(this.country == 'Autre' && this.inputVal == null){
				this.inputVal = ' ';
			}
		},
		emit_phone: function(){
			if(this.inputVal.length != 0){
				this.$emit('phonefc', this.inputVal);
			} else {
				this.$emit('phonefc', null);
			}
		}
	},

	created() {

		setTimeout(this.emit_phone,100);

		if(this.mynumber){

			this.inputVal = this.mynumber;

			if(this.mynumber.substr(0,6) == '+(212)') {
				this.country = "MA";
			} else if(this.mynumber.substr(0,6) == '+(213)') {
				this.country = "DZ";
			} else if(this.mynumber.substr(0,6) == '+(216)') {
				this.country = "TN";
			} else if(this.mynumber.substr(0,5) == '+(20)') {
				this.country = "EG";
			} else if(this.mynumber.substr(0,5) == '+(33)') {
				this.country = "FR";
			} else {
				this.country = "Autre";
			}
			this.change_country(this.country,0);
			this.apply_mask();

		} else {
			this.change_country('MA',0);
			this.apply_mask();
		}

	}
}
</script>