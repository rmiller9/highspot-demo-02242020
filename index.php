<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Highspot Test</title>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	</head>
	<body>
		<div id="app" class="container">
			<h1>Elder Scrolls VI</h1>
			<div class="row">		
				<div class="searchBar"> 
					<form @submit.prevent="findCard">
						<div class="col-md-4 col-sm-6 col-12 fieldWrap">
							<input type="text"  class="form-control" placeholder="Search for a card by name" v-model="form.search">
						</div>
						<div class="col-md-3 col-sm-3 col-6 fieldWrap">
							<button @click="newSearch"  class="form-control" type="submit">Search</button>	
						</div>
						<div class="col-md-3 col-sm-3 col-6 fieldWrap">
							<button v-if="searching" @click="resetPage" class="form-control" type="button">Reset</button>
						</div>
					</form>				
				</div><!--searchBar -->
				<div class="searchResults">{{searchResults}}</div>	
			</div><!-- row -->

			<div class="row">
				<div v-for='card in cards'
				class="card col-lg-3 col-md-4 col-sm-6 col-xs-12">
					<div class="innerWrap">
						<h3 class="cardName">{{ card.name}}</h3>
						<div class="imageWrap">
							<img class="cardImage" v-bind:src='card.imageUrl'>
						</div>
						<div class="cardDescription">
							<p>{{card.text}}</p>
							<p><strong>Set:</strong> {{card.set.name}}</p>
							<p><strong>Type: </strong>{{card.type}}  </p>				
						</div>
					</div>
					  <section v-if="errored">
						<p>We're sorry, we're not able to retrieve this information at the moment, please try back later</p>
					  </section>
			  </div>
				<div v-if="loading" class="loading col-12" id="loadingImage"><img src="images/colored-ellipses-fidget.gif"></div>
			</div>			
		</div>
	</body>
</html>
<script>
	new Vue({
	  el: '#app',
	  data () {
		return {
			form:{search:""},
			searchTerm: "",
			searchResults: "",
			loading: true,
			errored: false,
			bottom: false,
			cards:[],
			moreCards:[],
			searching: false,
			page:1,
			pageSize: 20
		}
	  },
	  watch: {
		bottom(bottom) {
		  if (bottom) {
			if(!this.searching){
				this.addCards()				
			}else{
				this.findCard()
			}

		  }
		}
	  },
	  created() {
		window.addEventListener('scroll', () => {
		  this.bottom = this.bottomVisible()
		})
		 this.addCards();
	  },
	  methods: {
		bottomVisible() {
		  const scrollY = window.scrollY
		  const visible = document.documentElement.clientHeight
		  const pageHeight = document.documentElement.scrollHeight
		  const bottomOfPage = visible + scrollY >= pageHeight
		  return bottomOfPage || pageHeight < visible
		},
		addCards() {
		this.loading = true;
		axios
		  .get('https://api.elderscrollslegends.io/v1/cards',{
			params: {
					pageSize: this.pageSize,
					page:this.page
				}
			})
		  .then(response => {
			let results = response.data.cards;
			this.moreCards = results;
			this.page ++
			//make sure our array is only nested one deep
			for(let i =0; i< this.pageSize; i++){
				this.cards.push(this.moreCards[i])
			}

		  })
		  .catch(error => {
			console.log(error)
			this.errored = true
		  })
		  .finally(() => this.loading = false)
		},
		findCard(){
			if(!this.searching){
				this.page =1;
			}
			this.searching = true;
			this.searchTerm = this.form.search;
			axios.get('https://api.elderscrollslegends.io/v1/cards',{
			params: {
					name:this.searchTerm,
					pageSize: this.pageSize,
					page:this.page
				}
			})
			.then(response=>{
				let returnedCard = response.data.cards;
				this.moreCards = returnedCard;
				this.page ++;
				let resultsCount =returnedCard.length;
				if (resultsCount >= this.pageSize) {					
					for(let j=0; j < this.pageSize; j++){
						this.cards.push(this.moreCards[j])
					}						
				}else if(resultsCount >0){
					var plural = resultsCount===1 ? " match": " matches";
					this.searchResults = returnedCard.length + plural;
					for(let j=0; j < returnedCard.length; j++){
						this.cards.push(this.moreCards[j])
					}						
				}else{
					//no results
					this.searchResults = "No matches.";
				}

			} ) 
		},
		 newSearch(){
			this.cards = [];	
			this.page =1;
			this.searchResults = "";
		},
		  resetPage(){
			  this.searching = false;
			  this.cards = [];
			  this.page = 1;
			  this.addCards();
			  this.searchResults = "";
		  }
	  }
	})
</script>

<style>
	body, .card {
		background-color:#393939;
	}
	.loading {
		background:#bbb;
	}
	#loadingImage {
		text-align: center;
	}
	h1{
		text-align: center;
		color:#fff;
	}
	.searchBar {
		width:100%;
		padding-bottom:1rem;
	}
	.fieldWrap {
		float:left;
	}
	.searchBar button.form-control {
		width:5.5rem;
	}
	.searchResults {
		width:100%;
		color:#fff;
		text-align:center;
	}
	h3.cardName {
		font-size:1.25rem;
	}
	.card {
		text-align:center;
		padding:.25rem;
		border:none;
	}
	.card .innerWrap {
		border:3px solid #fff;
		background-color:#bbb;
		border-radius:.25rem;
		padding:.5rem;
		height:100%;
	}
	.card .cardDescription {
		background-color:#F4F1F1;
		padding:.25rem;
		clear:both;
	}
	.card p{
		text-align:left;
		color:#011F22;
	}
	.imageWrap {
		border:1px solid #ccc;
		border-radius:5px;
		background-color:#ddd;
	}
	img.cardImage{
		height:auto;
		max-width:100%
	}
	@media screen and (max-width:767px){
		img.cardImage{
			width:100%;
		}
		.fieldWrap {
			margin:1rem 0;
		}
	}
</style>
