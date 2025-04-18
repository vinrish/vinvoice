<template>
    <div class="main-content">
        <breadcumb :page="$t('PurchaseDetail')" :folder="$t('ListPurchases')"/>
        <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>
        <b-card v-if="!isLoading">
            <b-row>
                <b-col md="12" class="mb-2">
                    <router-link
                        v-if="currentUserPermissions && currentUserPermissions.includes('invoice_edit')"
                        title="Edit"
                        class="btn btn-success btn-icon ripple btn-sm"
                        :to="{ name:'edit_purchase', params: { id: $route.params.id } }"
                    >
                        <i class="i-Edit"></i>
                        <span>{{$t('EditPurchase')}}</span>
                    </router-link>
                    <button @click="Send_Email()" class="btn btn-info btn-icon ripple btn-sm">
                        <i class="i-Envelope-2"></i>
                        {{$t('Email')}}
                    </button>
                    <button @click="Print_Purchase_PDF()" class="btn btn-primary btn-icon ripple btn-sm">
                        <i class="i-File-TXT"></i> PDF
                    </button>
                    <button @click="print()" class="btn btn-warning btn-icon ripple btn-sm">
                        <i class="i-Billing"></i>
                        {{$t('print')}}
                    </button>
                    <button
                        v-if="currentUserPermissions && currentUserPermissions.includes('invoice_delete')"
                        @click="Delete_Purchase()"
                        class="btn btn-danger btn-icon ripple btn-sm"
                    >
                        <i class="i-Close-Window"></i>
                        {{$t('Del')}}
                    </button>
                </b-col>
            </b-row>
            <div class="invoice mt-5" id="print_Invoice">
                <div class="invoice-print">
                    <b-row class="justify-content-md-center">
                        <h4 class="font-weight-bold">{{$t('PurchaseDetail')}} : {{invoice.ref}}</h4>
                    </b-row>
                    <hr>
                    <b-row class="mt-5">
                        <b-col lg="4" md="4" sm="12" class="mb-4">
                            <h5 class="font-weight-bold">{{$t('Supplier_Info')}}</h5>
                            <div><span class="bold">{{$t('Name')}}</span> : {{invoice.supplier_name}}</div>
                            <div><span class="bold">{{$t('Email')}}</span> : {{invoice.supplier_email}}</div>
                            <div><span class="bold">{{$t('Phone')}}</span> : {{invoice.supplier_phone}}</div>
                            <div><span class="bold">{{$t('Address')}}</span> : {{invoice.supplier_adr}}</div>
                        </b-col>
                        <b-col lg="4" md="4" sm="12" class="mb-4">
                            <h5 class="font-weight-bold">{{$t('Customer_Info')}}</h5>
                            <div><span class="bold">{{$t('Name')}}</span> : {{invoice.customer_name}}</div>
                            <div v-if="invoice.customer_email !== null && invoice.customer_email !== ''"><span class="bold">{{$t('Email')}}</span> : {{invoice.customer_email}}</div>
                            <div><span class="bold">{{$t('Phone')}}</span> : {{invoice.customer_phone}}</div>
                            <div v-if="invoice.customer_adr !== null && invoice.customer_adr !== ''"><span class="bold">{{$t('Address')}}</span> : {{invoice.customer_adr}}</div>
                        </b-col>
                        <b-col lg="4" md="4" sm="12" class="mb-4">
                            <h5 class="font-weight-bold">{{$t('Purchase_Info')}}</h5>
                            <div>{{$t('Reference')}} : {{invoice.ref}}</div>
                            <div>{{$t('Date')}} : {{invoice.date}}</div>
                        </b-col>
                    </b-row>
                    <b-row class="mt-3">
                        <b-col md="12">
                            <h5 class="font-weight-bold">{{$t('Order_Summary')}}</h5>
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead class="bg-gray">
                                    <tr>
                                        <th scope="col">{{$t('ProductName')}}</th>
                                        <th scope="col">{{$t('Details')}}</th>
                                        <th scope="col">{{$t('Unitcost')}}</th>
                                        <th scope="col">{{$t('Quantity')}}</th>
                                        <th scope="col">{{$t('SubTotal')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="detail in details">
                                        <td><span>{{detail.name}}</span>
<!--                                            <p v-show="detail.is_imei && detail.imei_number !==null ">{{$t('IMEI_SN')}} : {{detail.imei_number}}</p>-->
                                        </td>
                                        <td>
                                            <span>{{detail.description}}</span>
                                        </td>
                                        <td>{{currentUser.currency}} {{formatNumber(detail.cost,3)}}</td>
                                        <td>{{formatNumber(detail.quantity,2)}}</td>
                                        <td>{{currentUser.currency}} {{formatNumber(detail.total,2)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </b-col>
                        <div class="offset-md-9 col-md-3 mt-4">
                            <table class="table table-striped table-sm">
                                <tbody>
                                <tr>
                                    <td class="bold">{{$t('OrderTax')}}</td>
                                    <td>
                                        <span>{{currentUser.currency}} {{formatNumber(invoice.TaxNet,2)}} ({{formatNumber(invoice.tax_rate,2)}} %)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="font-weight-bold">{{$t('Total')}}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="font-weight-bold"
                                        >{{currentUser.currency}} {{invoice.GrandTotal}}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <b-col>
                            <p><strong>Warranty</strong> : {{ invoice.warranty}}</p>
                            <p><strong>Expiry</strong> : {{ invoice.warranty_expiry}}</p>
                        </b-col>
                    </b-row>
                </div>
            </div>
        </b-card>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";

export default {
    computed: mapGetters(["currentUserPermissions", "currentUser"]),

    metaInfo: {
        title: "Detail Purchase"
    },

    data() {
        return {
            isLoading: false,
            invoice: {},
            details: [],
            // variants: [],
            company: {},
            email: {
                to: "",
                subject: "",
                message: "",
                supplier_name: "",
                Purchase_Ref: ""
            }
        };
    },

    methods: {
        //----------------------------------- print Purchase -------------------------\\
        Print_Purchase_PDF() {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            let id = this.$route.params.id;
            axios
                .get(`invoice_pdf/${id}`, {
                    responseType: "blob", // important
                    headers: {
                        "Content-Type": "application/json"
                    }
                })

                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute(
                        "download",
                        "invoice_" + this.invoice.ref + ".pdf"
                    );
                    document.body.appendChild(link);
                    link.click();
                    // Complete the animation of the  progress bar.
                    setTimeout(() => NProgress.done(), 500);
                })
                .catch(() => {
                    // Complete the animation of the  progress bar.
                    setTimeout(() => NProgress.done(), 500);
                });
        },

        //------------------------------ Print -------------------------\\
        print() {
            this.$htmlToPaper('print_Invoice');
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

        //----------------------------------- Get Details Purchase ------------------------------\\
        Get_Details() {
            let id = this.$route.params.id;
            axios
                .get(`invoices/${id}`)
                .then(response => {
                    this.invoice = response.data.invoice;
                    this.details = response.data.details;
                    this.company = response.data.company;
                    this.isLoading = false;
                })
                .catch(response => {
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },

        //--------------------------------------------- Send Purchase to Email -------------------------------\\
        Send_Email() {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            let id = this.$route.params.id;
            axios
                .post("purchase_send_email", {
                    id: id,
                })
                .then(response => {
                    // Complete the animation of the  progress bar.
                    setTimeout(() => NProgress.done(), 500);
                    this.makeToast(
                        "success",
                        this.$t("Send.TitleEmail"),
                        this.$t("Success")
                    );
                })
                .catch(error => {
                    // Complete the animation of the  progress bar.
                    setTimeout(() => NProgress.done(), 500);
                    this.makeToast("danger", this.$t("SMTPIncorrect"), this.$t("Failed"));
                });
        },

        //------ Toast
        makeToast(variant, msg, title) {
            this.$root.$bvToast.toast(msg, {
                title: title,
                variant: variant,
                solid: true
            });
        },
    },

    //----------------------------- Created function-------------------

    created: function() {
        this.Get_Details();
    }
}
</script>
