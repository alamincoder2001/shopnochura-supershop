<div id="purchasedamageInvoice">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<purchasedamage-invoice v-bind:damage_id="damageId"></purchasedamage-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/purchasedamageInvoice.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script>
	new Vue({
		el: '#purchasedamageInvoice',
		components: {
			purchasedamageInvoice
		},
		data(){
			return {
				damageId: parseInt('<?php echo $damageId;?>')
			}
		}
	})
</script>

