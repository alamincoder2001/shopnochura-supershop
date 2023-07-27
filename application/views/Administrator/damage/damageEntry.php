<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
    }

    .v-select input[type=search],
    .v-select input[type=search]:focus {
        margin: 0px;
    }

    .v-select .vs__selected-options {
        overflow: hidden;
        flex-wrap: nowrap;
    }

    .v-select .selected-tag {
        margin: 2px 0px;
        white-space: nowrap;
        position: absolute;
        left: 0px;
    }

    .v-select .vs__actions {
        margin-top: -5px;
    }

    .v-select .dropdown-menu {
        width: auto;
        overflow-y: auto;
    }
</style>

<div id="damage" class="row">
    <div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 col-xs-4 control-label"> Damage Invoice </label>
                <div class="col-md-2 col-xs-8 no-padding-left">
                    <input type="text" id="invoiceNo" class="form-control" v-model="damage.invoiceNo" readonly />
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-1 col-xs-4 control-label no-padding-right"> Supplier </label>
                <div class="col-md-3 col-xs-8">
                    <v-select :options="suppliers" v-model="selectedSupplier" label="display_name" placeholder="Select Supplier"></v-select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-xs-12">
                    <input class="form-control" id="damageDate" type="date" v-model="damage.damageDate" v-bind:disabled="userType == 'u' ? true : false" />
                </div>
            </div>
        </div>
    </div>


    <div class="col-xs-12 col-md-9 col-lg-9">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title">Damage Information</h4>
                <div class="widget-toolbar">
                    <a href="#" data-action="collapse">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>

                    <a href="#" data-action="close">
                        <i class="ace-icon fa fa-times"></i>
                    </a>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main">

                    <div class="row">
                        <div class="col-md-5 col-xs-12">
                            <form v-on:submit.prevent="addToCart">
                                <div class="form-group">
                                    <label class="col-xs-3 control-label no-padding-right"> Product </label>
                                    <div class="col-xs-8">
                                        <v-select v-bind:options="products" v-model="selectedProduct" label="display_text" v-on:input="productOnChange"></v-select>
                                    </div>
                                    <div class="col-xs-1" style="padding: 0;">
                                        <a href="<?= base_url('product') ?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label no-padding-right"> Sale Rate </label>
                                    <div class="col-xs-9">
                                        <input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" v-model="selectedProduct.Product_SellingPrice" v-on:input="productTotal" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label no-padding-right"> Quantity </label>
                                    <div class="col-xs-9">
                                        <input type="number" step="0.01" id="quantity" min="0" placeholder="Qty" class="form-control" ref="quantity" v-model="selectedProduct.pcs" v-on:input="productTotal" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label no-padding-right"> Amount </label>
                                    <div class="col-xs-9">
                                        <input type="text" id="productTotal" placeholder="Amount" class="form-control" v-model="selectedProduct.total" readonly />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label no-padding-right"> </label>
                                    <div class="col-xs-9">
                                        <button type="submit" class="btn btn-default pull-right">Add to Cart</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="col-md-7 col-xs-12">
                            <table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
                                <thead>
                                    <tr class="">
                                        <th style="width:10%;color:#000;">Sl</th>
                                        <th style="width:35%;color:#000;">Product Name</th>
                                        <th style="width:7%;color:#000;">Qty</th>
                                        <th style="width:8%;color:#000;">Rate</th>
                                        <th style="width:15%;color:#000;">Total</th>
                                        <th style="width:10%;color:#000;">Action</th>
                                    </tr>
                                </thead>
                                <tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
                                    <tr v-for="(product, sl) in cart" :style="{background: product.is_free == 'true'?'#ffd17e':''}" :title="product.is_free == 'false'?'':'Free Product'">
                                        <td>{{ sl + 1 }}</td>
                                        <td>{{ product.productCode }} - {{ product.name }}</td>
                                        <td>{{ product.quantity }}</td>
                                        <td>{{ product.purchaseRate }}</td>
                                        <td>{{ product.total }}</td>
                                        <td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
                                    </tr>

                                    <tr>
                                        <td colspan="6"></td>
                                    </tr>

                                    <tr style="font-weight: bold;">
                                        <td colspan="3">Note</td>
                                        <td colspan="3">Total</td>
                                    </tr>

                                    <tr>
                                        <td colspan="3"><textarea style="width: 100%;font-size:13px;" placeholder="Note" v-model="damage.note"></textarea></td>
                                        <td colspan="3" style="padding-top: 15px;font-size:18px;">{{ damage.Total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xs-12 col-md-3 col-lg-3">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title">Amount Details</h4>
                <div class="widget-toolbar">
                    <a href="#" data-action="collapse">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>

                    <a href="#" data-action="close">
                        <i class="ace-icon fa fa-times"></i>
                    </a>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table style="color:#000;margin-bottom: 0px;border-collapse: collapse;">
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-xs-12 control-label no-padding-right">Total</label>
                                                <div class="col-xs-12">
                                                    <input type="number" id="total" class="form-control" v-model="damage.Total" readonly />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <input type="button" class="btn btn-default btn-sm" value="Save Damage" v-on:click="saveDamage" style="color: black!important;margin-top: 0px;width:100%;padding:5px;font-weight:bold;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#damage',
        data() {
            return {
                damage: {
                    damageId: parseInt('<?php echo $damageId; ?>'),
                    invoiceNo: '<?php echo $invoice; ?>',
                    AddBy: '<?php echo $this->session->userdata("FullName"); ?>',
                    supplierId: '',
                    Total: 0.00,
                    damageDate: moment().format('YYYY-MM-DD'),
                    note: ''
                },
                cart: [],
                suppliers: [],
                selectedSupplier: {
                    Supplier_SlNo: '',
                    Supplier_Code: '',
                    Supplier_Name: '',
                    display_name: 'Select Supplier',
                    Supplier_Mobile: '',
                    Supplier_Address: '',
                    Supplier_Type: ''
                },
                products: [],
                selectedProduct: {
                    Product_SlNo: '',
                    display_text: 'Select Product',
                    Product_Name: '',
                    Unit_Name: '',
                    converted_name: 'PCS',
                    quantity: 0,
                    boxQty: 0,
                    pcs: 0,
                    Product_Purchase_Rate: 0,
                    Product_SellingPrice: 0.00,
                    vat: 0.00,
                    total: 0.00,
                    is_free: 'false'
                },
                userType: '<?php echo $this->session->userdata("accountType"); ?>'
            }
        },
        async created() {
            await this.getSuppliers();
            this.getProducts();
            if (this.damage.damageId != 0) {
                await this.getDamage();
            }
        },
        methods: {
            async getSuppliers() {
                await axios.get('/get_suppliers').then(res => {
                    this.suppliers = res.data;
                })
            },
            getProducts() {
                axios.post('/get_products').then(res => {
                    this.products = res.data
                })
            },

            productTotal() {
                let boxQty = this.selectedProduct.boxQty ? this.selectedProduct.per_unit_convert * this.selectedProduct.boxQty : 0;
                let pcsQty = this.selectedProduct.pcs ? this.selectedProduct.pcs : 0;
                this.selectedProduct.quantity = parseFloat(boxQty) + parseFloat(pcsQty);

                this.selectedProduct.total = parseFloat(parseFloat(this.selectedProduct.Product_Purchase_Rate) * this.selectedProduct.quantity).toFixed(2);
            },
            async productOnChange() {
                if (this.selectedProduct.Product_SlNo == '') {
                    return;
                }

                this.$refs.quantity.focus();
            },
            addToCart() {
                let product = {
                    productId: this.selectedProduct.Product_SlNo,
                    productCode: this.selectedProduct.Product_Code,
                    categoryName: this.selectedProduct.ProductCategory_Name,
                    name: this.selectedProduct.Product_Name,
                    salesRate: this.selectedProduct.Product_SellingPrice,
                    vat: this.selectedProduct.vat,
                    quantity: this.selectedProduct.quantity,
                    boxQty: this.selectedProduct.boxQty,
                    pcs: this.selectedProduct.pcs,
                    total: this.selectedProduct.total,
                    purchaseRate: this.selectedProduct.Product_Purchase_Rate,
                }


                let cartInd = this.cart.findIndex(p => p.productId == product.productId);
                if (cartInd > -1) {
                    this.cart.splice(cartInd, 1);
                }

                this.cart.unshift(product);
                this.clearProduct();
                this.calculateTotal();
            },
            removeFromCart(ind) {
                this.cart.splice(ind, 1);
                this.calculateTotal();
            },
            clearProduct() {
                this.selectedProduct = {
                    Product_SlNo: '',
                    display_text: 'Select Product',
                    Product_Name: '',
                    Unit_Name: '',
                    converted_name: 'PCS',
                    quantity: 0,
                    boxQty: 0,
                    pcs: 0,
                    Product_Purchase_Rate: 0,
                    Product_SellingPrice: 0.00,
                    vat: 0.00,
                    total: 0.00,
                }
                this.productStock = '';
                this.productStockText = '';
            },
            calculateTotal() {
                this.damage.Total = this.cart.reduce((prev, curr) => {
                    return prev + parseFloat(curr.total)
                }, 0).toFixed(2);
            },
            async saveDamage() {
                if (this.selectedSupplier.Supplier_SlNo == '') {
                    alert('Select Supplier');
                    return;
                }
                if (this.cart.length == 0) {
                    alert('Cart is empty');
                    return;
                }

                this.damage.supplierId = this.selectedSupplier.Supplier_SlNo;

                let url = "/add_supplierwise_damage";
                if (this.damage.damageId != 0) {
                    url = "/update_supplierwise_damage";
                }

                let data = {
                    damage: this.damage,
                    cart: this.cart
                }

                axios.post(url, data).then(async res => {
                    let r = res.data;
                    if (r.success) {
                        let conf = confirm(`${r.message}, Do you want to view invoice?`);
                        if (conf) {
                            alert(r.message);
                        } else {
                            location.href = "/supplierwise_damage";
                        }
                    } else {
                        alert(r.message);
                    }
                })
            },

            async getDamage() {
                await axios.post('/get_supplierwise_damage', {
                    damageId: this.damage.damageId
                }).then(res => {
                    let r = res.data;
                    let damage = r.damages[0];

                    this.damage = {
                        damageId: damage.id,
                        invoiceNo: damage.Damage_invoiceNo,
                        AddBy: damage.AddBy,
                        supplierId: damage.supplierId,
                        Total: damage.Damage_Total,
                        damageDate: damage.Damage_Date,
                        note: damage.note
                    }

                    this.selectedSupplier = {
                        Supplier_SlNo: damage.supplierId,
                        display_name: `${damage.Supplier_Code}-${damage.Supplier_Name}`,
                    }

                    r.damageDetails.forEach(product => {
                        let cartProduct = {
                            productId: product.Damage_ProductId,
                            productCode: product.Product_Code,
                            categoryName: product.ProductCategory_Name,
                            name: product.Product_Name,
                            salesRate: product.Product_SellingPrice,
                            quantity: product.Damage_Quantity,
                            total: product.Damage_TotalAmount,
                            purchaseRate: product.Damage_Price,
                        }

                        this.cart.push(cartProduct);
                    })
                })
            }
        }
    })
</script>