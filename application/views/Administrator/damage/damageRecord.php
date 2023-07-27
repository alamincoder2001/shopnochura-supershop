<style>
    .v-select{
		margin-top:-2.5px;
        float: right;
        min-width: 180px;
        margin-left: 5px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
        height: 25px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
	#searchForm select{
		padding:0;
		border-radius: 4px;
	}
	#searchForm .form-group{
		margin-right: 5px;
	}
	#searchForm *{
		font-size: 13px;
	}
	.record-table{
		width: 100%;
		border-collapse: collapse;
	}
	.record-table thead{
		background-color: #0097df;
		color:white;
	}
	.record-table th, .record-table td{
		padding: 3px;
		border: 1px solid #454545;
	}
    .record-table th{
        text-align: center;
    }
</style>
<div id="damageRecord">
	<div class="row" style="border-bottom: 1px solid #ccc;padding: 3px 0;">
		<div class="col-md-12">
			<form class="form-inline" id="searchForm" @submit.prevent="getdamageRecord">
				<div class="form-group">
					<label>Search Type</label>
					<select class="form-control" v-model="searchType" @change="onChangeRecord">
						<option value="">All</option>
						<option value="supplier">By Supplier</option>
						<option value="quantity">By Quantity</option>
					</select>
				</div>

				<div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'supplier' && suppliers.length > 0 ? '' : 'none'}">
					<label>Supplier</label>
					<v-select v-bind:options="suppliers" v-model="selectedSupplier" label="display_name"></v-select>
				</div>

				<div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'quantity' && products.length > 0 ? '' : 'none'}">
					<label>Product</label>
					<v-select v-bind:options="products" v-model="selectedProduct" label="display_text"></v-select>
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="dateFrom">
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="dateTo">
				</div>

				<div class="form-group" style="margin-top: -5px;">
					<input type="submit" value="Search">
				</div>
			</form>
		</div>
	</div>

	<div class="row" style="margin-top:15px;display:none;" v-bind:style="{display: damages.length > 0 ? '' : 'none'}">
		<div class="col-md-12" style="margin-bottom: 10px;">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent">

				<table class="record-table" style="display:none" :style="{display: damages.length > 0 ? '': 'none'}">
					<thead>
						<tr>
							<th>Invoice No.</th>
							<th>Date</th>
							<th>Supplier Name</th>
							<th>Total</th>
							<th>Note</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="damage in damages">
							<td>{{ damage.Damage_invoiceNo }}</td>
							<td>{{ damage.Damage_Date }}</td>
							<td>{{ damage.Supplier_Name }}</td>
							<td style="text-align:right;">{{ damage.Damage_Total }}</td>
							<td style="text-align:left;">{{ damage.note }}</td>
							<td style="text-align:center;">
								<a href="" title="Damage Invoice" v-bind:href="`/supplierwise_damage_invoice/${damage.id}`" target="_blank"><i class="fa fa-file-text"></i></a>
								<?php if($this->session->userdata('accountType') != 'u'){?>
								<a href="" title="Edit Damage" title="Damage Invoice" v-bind:href="`/supplierwise_damage/${damage.id}`"><i class="fa fa-edit"></i></a>
								<a href="" title="Delete Damage" @click.prevent="deleteDamage(damage.id)"><i class="fa fa-trash"></i></a>
								<?php }?>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr style="font-weight:bold;">
							<td colspan="3" style="text-align:right;">Total</td>
							<td style="text-align:right;">{{ damages.reduce((prev, curr)=>{return prev + parseFloat(curr.Damage_Total)}, 0) }}</td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#damageRecord',
		data(){
			return {
				searchType: '',
				dateFrom: moment().format('YYYY-MM-DD'),
				dateTo: moment().format('YYYY-MM-DD'),
				suppliers: [],
				selectedSupplier: null,
				products: [],
				selectedProduct: null,
				damages: [],
			}
		},
		methods: {
			getProducts(){
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},
			getSuppliers(){
				axios.get('/get_suppliers').then(res => {
					this.suppliers = res.data;
				})
			},
			getCategories(){
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			onChangeRecord(){
				this.selectedProduct = null;
				this.selectedSupplier = null;
				this.damages = [];
				if (this.searchType == 'quantity') {
					this.getProducts();
				}
				if (this.searchType == 'supplier') {
					this.getSuppliers();
				}
			},
			getdamageRecord(){
				let filter = {
					userFullName: this.selectedUser == null || this.selectedUser.FullName == '' ? '' : this.selectedUser.FullName,
					supplierId: this.selectedSupplier == null ? '' : this.selectedSupplier.Supplier_SlNo,
					dateFrom: this.dateFrom,
					dateTo: this.dateTo
				}

				let url = '/get_supplierwise_damage';

				axios.post(url, filter)
				.then(res => {
					this.damages = res.data.damages;
					console.log(res.data);
				})
			},
			deleteDamage(damageId){
				let deleteConf = confirm('Are you sure?');
				if(deleteConf == false){
					return;
				}
				axios.post('/delete_supplierwise_damage', {damageId: damageId})
				.then(res => {
					let r = res.data;
					alert(r.message);
					if(r.success){
						this.getdamageRecord();
					}
				})
			},
			async print(){

				let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Purchase Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								${userText} ${supplierText} ${productText} ${categoryText}
							</div>
							<div class="col-xs-6 text-right">
								${dateText}
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

				reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
				reportWindow.document.body.innerHTML += reportContent;
				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			}
		}
	})
</script>