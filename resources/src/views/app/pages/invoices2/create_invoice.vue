<template>
    <div class="main-content">
        <breadcumb :page="$t('AddPurchase')" :folder="$t('ListPurchases')"/>
        <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>

        <validation-observer ref="create_purchase" v-if="!isLoading">
            <b-form @submit.prevent="Submit_Purchase">
                <b-row>
                    <b-col lg="12" md="12" sm="12">
                        <b-card>
                            <b-row>
                                <!-- date  -->
                                <b-col lg="3" md="3" sm="12" class="mb-3">
                                    <validation-provider
                                        name="date"
                                        :rules="{ required: true}"
                                        v-slot="validationContext"
                                    >
                                        <b-form-group :label="$t('date') + ' ' + '*'">
                                            <b-form-input
                                                :state="getValidationState(validationContext)"
                                                aria-describedby="date-feedback"
                                                type="date"
                                                v-model="purchase.date"
                                            ></b-form-input>
                                            <b-form-invalid-feedback
                                                id="date-feedback"
                                            >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <b-col lg="3" md="3" sm="3" class="mt-4 mb-4">
                                    <label class="radio-style checkbox checkbox-primary mb-3">
                                        <input type="checkbox" value="other" v-model="client.is_walking" name="is_walking">
                                        <h5>{{$t('Walking Customer')}}</h5>
                                        <span class="checkmark"></span>
                                    </label>
                                </b-col>

                                <!-- Supplier -->
                                <b-col lg="3" md="3" sm="12" class="mb-3">
                                    <b-form-group :label="$t('Supplier') + ' ' + '*'">
                                        <v-select
                                            class="text-primary"
                                            v-model="purchase.supplier_id"
                                            :reduce="label => label.value"
                                            :placeholder="$t('Choose_Supplier')"
                                            :options="suppliers.map(suppliers => ({label: suppliers.name, value: suppliers.id}))"
                                        />
                                    </b-form-group>
                                </b-col>

                                <!-- Client -->
                                <b-col lg="3" md="3" sm="12" class="mb-3" v-if="!client.is_walking">
                                    <b-form-group :label="$t('Client') + ' ' + '*'">
                                        <v-select
                                            class="text-primary"
                                            v-model="purchase.client_id"
                                            :reduce="label => label.value"
                                            :placeholder="$t('Choose_Client')"
                                            :options="clients.map(clients => ({label: clients.name, value: clients.id}))"
                                        />
                                    </b-form-group>
                                </b-col>

                                <b-col md="6" v-if="client.is_walking">
                                    <b-row>
                                        <b-col md="6" class="mb-2">
                                            <b-form-group :label="$t('Customer Name') + ' ' + '*'">
                                                <b-form-input
                                                    aria-describedby="Name-feedback"
                                                    label="Name"
                                                    :placeholder="$t('Enter Customer Name')"
                                                    v-model="client.name"
                                                ></b-form-input>
                                            </b-form-group>
                                        </b-col>

                                        <b-col md="6" class="mb-2">
                                            <b-form-group :label="$t('Customer Phone') + ' ' + '*'">
                                                <b-form-input
                                                    aria-describedby="Phone-feedback"
                                                    label="Phone"
                                                    :placeholder="$t('Enter Customer Phone')"
                                                    v-model="client.phone"
                                                ></b-form-input>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                </b-col>


                                <!-- Product -->
                                <b-col md="12">
                                    <b-row>
                                        <b-col md="10">
                                            <h3>Products</h3>
                                        </b-col>
                                        <b-col md="2">
                                            <b-button @click="addItem()" size="sm" variant="outline-success ripple m-1">
                                                <i class="i-Add"></i> Add Product
                                            </b-button>
                                        </b-col>
                                    </b-row>
                                </b-col>

                                <b-col md="12" v-for="(detail, index) in details" :key="detail.id">
                                    <b-card class="mt-3">
                                        <b-card-header>
                                            <b-row>
                                                <i :class="detail.showFields ? 'i-Up-4 text-warning text-25' : 'i-Down-4 text-success text-25'" @click="detail.showFields = !detail.showFields"></i>
                                                <h5 :class="detail.showFields ? 'ml-2 text-warning text-18' : 'ml-2 text-success text-18' ">{{ detail.name || $t('Product Details') }}</h5>
                                            </b-row>
                                        </b-card-header>
                                        <b-collapse :visible="detail.showFields">
                                            <b-row>
                                            <!-- Name -->
                                            <b-col md="3" class="mb-2">
                                                <validation-provider
                                                    name="Name"
                                                    :rules="{required:true , min:3 , max:55}"
                                                    v-slot="validationContext"
                                                >
                                                    <b-form-group :label="$t('Product Name') + ' ' + '*'">
                                                        <b-form-input
                                                            :state="getValidationState(validationContext)"
                                                            aria-describedby="Name-feedback"
                                                            label="Name"
                                                            :placeholder="$t('Enter Product Name')"
                                                            v-model="detail.name"
                                                        ></b-form-input>
                                                        <b-form-invalid-feedback id="Name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                    </b-form-group>
                                                </validation-provider>
                                            </b-col>

                                            <b-col md="3" class="mb-2">
                                                <validation-provider
                                                    name="Serial"
                                                    :rules="{required:true , min:3 , max:55}"
                                                    v-slot="validationContext"
                                                >
                                                    <b-form-group :label="$t('Serial Number') + ' ' + '*'">
                                                        <b-form-input
                                                            :state="getValidationState(validationContext)"
                                                            aria-describedby="Name-feedback"
                                                            label="Name"
                                                            :placeholder="$t('Enter Product Serial')"
                                                            v-model="detail.serial"
                                                        ></b-form-input>
                                                        <b-form-invalid-feedback id="Name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                    </b-form-group>
                                                </validation-provider>
                                            </b-col>

                                            <!-- Product Cost -->
                                            <b-col md="3" class="mb-2">
                                                <validation-provider
                                                    name="Product Cost"
                                                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                                                    v-slot="validationContext"
                                                >
                                                    <b-form-group :label="$t('ProductCost') + ' ' + '*'">
                                                        <b-form-input
                                                            :state="getValidationState(validationContext)"
                                                            aria-describedby="ProductCost-feedback"
                                                            label="Cost"
                                                            :placeholder="$t('Enter_Product_Cost')"
                                                            v-model="detail.unit_cost"
                                                            @input="Calcul_Total"
                                                        ></b-form-input>
                                                        <b-form-invalid-feedback
                                                            id="ProductCost-feedback"
                                                        >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                    </b-form-group>
                                                </validation-provider>
                                            </b-col>

                                            <!-- Quantity -->
                                            <b-col md="2" class="mb-2">
                                                <b-form-group :label="$t('Quantity')">
                                                    <b-input-group>
                                                        <b-input-group-prepend>
                                                                    <span
                                                                        class="btn btn-primary btn-sm"
                                                                        @click="decrement(detail)"
                                                                    >-</span>
                                                        </b-input-group-prepend>
                                                        <input
                                                            @keyup="detail"
                                                            class="form-control"
                                                            :min="0.00"
                                                            v-model.number="detail.quantity"
                                                        >
                                                        <b-input-group-append>
                                                                    <span
                                                                        class="btn btn-primary btn-sm"
                                                                        @click="increment(detail)"
                                                                    >+</span>
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </b-form-group>
                                            </b-col>

                                            <b-col md="6" class="mb-2">
                                                <b-form-group :label="$t('Description')">
                                                    <textarea
                                                        rows="4"
                                                        class="form-control"
                                                        :placeholder="$t('Afewwords')"
                                                        v-model="detail.description"
                                                    ></textarea>
                                                </b-form-group>
                                            </b-col>

                                            <!-- Sub Total -->
                                            <b-col md="3" class="mb-2">
                                                <validation-provider
                                                    name="Product Cost"
                                                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                                                    v-slot="validationContext"
                                                >
                                                    <b-form-group :label="$t('SubTotal') + ' ' + '*'">
                                                        <b-form-input
                                                            :state="getValidationState(validationContext)"
                                                            aria-describedby="ProductCost-feedback"
                                                            label="SubTotal"
                                                            readonly
                                                            :value="calculateSubTotal(detail)"
                                                        ></b-form-input>
                                                        <b-form-invalid-feedback
                                                            id="ProductCost-feedback"
                                                        >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                    </b-form-group>
                                                </validation-provider>
                                            </b-col>

                                            <b-col md="12">
                                                <b-button @click="deleteProduct(detail.id)" size="sm" variant="outline-success ripple m-1">
                                                    <i class="i-Close-Window text-bg-danger"></i> Remove Product
                                                </b-button>
                                            </b-col>

                                        </b-row>
                                        </b-collapse>
                                    </b-card>
                                </b-col>

                                <div class="offset-md-9 col-md-3 mt-4">
                                    <table class="table table-striped table-sm">
                                        <tbody>
                                        <tr>
                                            <td class="bold">{{$t('OrderTax')}}</td>
                                            <td>
                                                <span>{{currentUser.currency}} {{purchase.TaxNet.toFixed(2)}} ({{formatNumber(purchase.tax_rate ,2)}} %)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold">{{$t('Total')}}</span>
                                            </td>
                                            <td>
                                              <span
                                                  class="font-weight-bold"
                                              >{{currentUser.currency}} {{GrandTotal.toFixed(2)}}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Order Tax  -->
                                <b-col lg="4" md="4" sm="12" class="mb-3">
                                    <validation-provider
                                        name="Order Tax"
                                        :rules="{ regex: /^\d*\.?\d*$/}"
                                        v-slot="validationContext"
                                    >
                                        <b-form-group :label="$t('OrderTax')">
                                            <b-input-group append="%">
                                                <b-form-input
                                                    :state="getValidationState(validationContext)"
                                                    aria-describedby="OrderTax-feedback"
                                                    label="Order Tax"
                                                    v-model.number="purchase.tax_rate"
                                                    @keyup="keyup_OrderTax()"
                                                ></b-form-input>
                                            </b-input-group>
                                            <b-form-invalid-feedback
                                                id="OrderTax-feedback"
                                            >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <!-- Status  -->
                                <b-col lg="4" md="4" sm="12" class="mb-3">
                                    <validation-provider name="Status" :rules="{ required: true}">
                                        <b-form-group slot-scope="{ valid, errors }" :label="$t('Status') + ' ' + '*'">
                                            <v-select
                                                class="text-primary"
                                                :class="{'is-invalid': !!errors.length}"
                                                :state="errors[0] ? false : (valid ? true : null)"
                                                v-model="purchase.statut"
                                                :reduce="label => label.value"
                                                :placeholder="$t('Choose_Status')"
                                                :options="
                                                    [
                                                      {label: 'received', value: 'received'},
                                                      {label: 'pending', value: 'pending'},
                                                       {label: 'ordered', value: 'ordered'}
                                                    ]"
                                            ></v-select>
                                            <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <b-col md="12" v-if="client.is_walking">
                                    <b-row>
                                        <b-col md="3" class="mb-2">
                                            <validation-provider
                                                name="Warranty Period"
                                                :rules="{regex: /^\d*\.?\d*$/}"
                                                v-slot="validationContext"
                                            >
                                                <b-form-group :label="$t('Warranty Period') + ' ' + '*'">
                                                    <b-form-input
                                                        :state="getValidationState(validationContext)"
                                                        aria-describedby="WarrantyPeriod-feedback"
                                                        label="WarrantyPeriod"
                                                        v-model.number="purchase.warranty_period"
                                                    ></b-form-input>
                                                    <b-form-invalid-feedback
                                                        id="WarrantyPeriod-feedback"
                                                    >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                                </b-form-group>
                                            </validation-provider>
                                        </b-col>

                                        <b-col lg="3" md="3" sm="12" class="mb-2">
                                            <b-form-group :label="$t('Warranty Type') + ' ' + '*'">
                                                <v-select
                                                    class="text-primary"
                                                    v-model="purchase.warranty_type"
                                                    :reduce="label => label.value"
                                                    :placeholder="$t('Choose_Warranty_Type')"
                                                    :options="
                                                       [
                                                        {label: 'Days', value: 'day'},
                                                        {label: 'Months', value: 'month'},
                                                        {label: 'Years', value: 'year'}
                                                       ]"
                                                ></v-select>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                </b-col>

                                <b-col md="12">
                                    <b-form-group>
                                        <b-button variant="primary" @click="Submit_Purchase" :disabled="SubmitProcessing"><i class="i-Yes me-2 font-weight-bold"></i> {{$t('submit')}}</b-button>
                                        <div v-once class="typo__p" v-if="SubmitProcessing">
                                            <div class="spinner sm spinner-primary mt-3"></div>
                                        </div>
                                    </b-form-group>
                                </b-col>
                            </b-row>
                        </b-card>
                    </b-col>
                </b-row>
            </b-form>
        </validation-observer>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";

export default {
    metaInfo: {
        title: "Create Purchase"
    },
    data() {
        return {
            isLoading: false,
            SubmitProcessing:false,
            Submit_Processing_detail:false,
            clients: [],
            suppliers: [],
            products: [],
            details: [],
            tag: "",
            purchases: [],
            purchase: {
                id: "",
                date: new Date().toISOString().slice(0, 10),
                statut: "received",
                notes: "",
                supplier_id: "",
                client_id: "",
                tax_rate: 0,
                TaxNet: 0,
                discount: 0,
                warranty_period: 0,
                warranty_type: "day",
            },
            client:{
                name: "",
                phone: "",
                is_walking: false
            },
            total: 0,
            GrandTotal: 0,
        }
    },

    computed: {
        ...mapGetters(["currentUserPermissions","currentUser"])
    },

    methods: {
        //--- Submit Validate Create Purchase
        Submit_Purchase() {
            this.$refs.create_purchase.validate().then(success => {
                if (!success) {
                    this.makeToast(
                        "danger",
                        this.$t("Please_fill_the_form_correctly"),
                        this.$t("Failed")
                    );
                } else {
                    this.Create_Purchase();
                }
            });
        },

        //---Validate State Fields
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },

        toggleFields(detail) {
            detail.showFields = !detail.showFields;
        },

        //------ Toast
        makeToast(variant, msg, title) {
            this.$root.$bvToast.toast(msg, {
                title: title,
                variant: variant,
                solid: true
            });
        },

        addItem() {
            const newDetail = {
                id: this.generateUniqueId(),
                name: '',
                serial: '',
                unit_cost: 0,
                description: '',
                quantity: 1,
                subtotal: 0,
                showFields: false,
            };
            this.details.push(newDetail);
            this.Calcul_Total();
        },
        deleteProduct(id) {
            this.details = this.details.filter(detail => detail.id !== id);
            this.Calcul_Total();
        },

        increment(detail) {
            detail.quantity++;
            this.Calcul_Total();
        },

        decrement(detail) {
            if (detail.quantity > 1) {
                detail.quantity--;
                this.Calcul_Total();
            }
        },

        calculateSubTotal(detail) {
            if (!detail.unit_cost || isNaN(detail.unit_cost) || !detail.quantity || isNaN(detail.quantity)) {
                return '0.00';
            }
            return (detail.unit_cost * detail.quantity).toFixed(2);
        },

        generateUniqueId() {
            return '_' + Math.random().toString(36).substr(2, 9);
        },

        //------------------------------Formetted Numbers -------------------------\\
        formatNumber(number, dec) {
            const value = (typeof number === "string"
                    ? number
                    : number.toString()
            ).split(".");
            if (dec <= 0) return value[0];
            let formated = value[1] || "";
            if (formated.length > dec)
                return `${value[0]}.${formated.substr(0, dec)}`;
            while (formated.length < dec) formated += "0";
            return `${value[0]}.${formated}`;
        },

        //---------- keyup OrderTax
        keyup_OrderTax() {
            if (isNaN(this.purchase.tax_rate)) {
                this.purchase.tax_rate = 0;
            } else if(this.purchase.tax_rate == ''){
                this.purchase.tax_rate = 0;
                this.Calcul_Total();
            }else {
                this.Calcul_Total();
            }
        },

        //-----------------------------------------Calcul Total ------------------------------\\
        Calcul_Total() {
            this.total = 0;
            this.details.forEach(detail => {
                const tax = (detail.taxe || 0) * detail.quantity;
                detail.subtotal = (detail.quantity * (detail.Net_cost || detail.unit_cost)) + tax;
                this.total += detail.subtotal;
            });

            const totalWithoutDiscount = this.total - (this.purchase.discount || 0);
            this.purchase.TaxNet = (totalWithoutDiscount * (this.purchase.tax_rate / 100));

            this.GrandTotal = totalWithoutDiscount + this.purchase.TaxNet + (this.purchase.shipping || 0);
            // Ensure to call this to make Vue recognize the update
            this.$nextTick(() => {
                this.GrandTotal = parseFloat(this.GrandTotal.toFixed(2));
            });
        },

        //---------------------------------------Get Elements Purchase ------------------------------\\
        GetElements() {
            axios
                .get("invoices/create")
                .then(response => {
                    this.suppliers = response.data.suppliers;
                    this.clients = response.data.clients;
                    this.isLoading = false;
                })
                .catch(response => {
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },

        //--------------------------------- Create Purchase -------------------------\\
        Create_Purchase() {
            // if (this.verifiedForm()) {
            this.SubmitProcessing = true;
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            axios
                .post("invoices", {
                    date: this.purchase.date,
                    supplier_id: this.purchase.supplier_id,
                    client_id: this.purchase.client_id,
                    statut: this.purchase.statut,
                    notes: this.purchase.notes,
                    tax_rate: this.purchase.tax_rate?this.purchase.tax_rate:0,
                    TaxNet: this.purchase.TaxNet?this.purchase.TaxNet:0,
                    GrandTotal: this.GrandTotal,
                    warranty_period: this.purchase.warranty_period,
                    warranty_type: this.purchase.warranty_type,
                    client: this.client,
                    details: this.details.map(detail => ({
                        product_name: detail.name,
                        description: detail.description,
                        unit_cost: detail.unit_cost,
                        quantity: detail.quantity,
                        serial: detail.serial,
                        subtotal: this.calculateSubTotal(detail)
                    }))
                    // details: this.details
                })
                .then(response => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();

                    this.makeToast(
                        "success",
                        this.$t("Create.TitlePurchase"),
                        this.$t("Success")
                    );

                    this.SubmitProcessing = false;
                    this.$router.push({ name: "index_invoice" });
                })
                .catch(error => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
                    this.SubmitProcessing = false;
                });
            // }
        },

        //-------------------------------- Get Last Detail Id -------------------------\\
        Last_Detail_id() {
            this.product.detail_id = 0;
            var len = this.details.length;
            this.product.detail_id = this.details[len - 1].detail_id + 1;
        },

    },

    //-----------------------------  Created function-------------------
    created() {
        this.GetElements();
    }
}
</script>
