<template>
    <div class="main-content">
        <breadcumb :page="$t('All Invoices')" :folder="$t('Invoices')" />
        <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>
        <div v-else>
            <vue-good-table
                mode="remote"
                :columns="columns"
                :totalRows="totalRows"
                :rows="invoices"
                @on-page-change="onPageChange"
                @on-per-page-change="onPerPageChange"
                @on-sort-change="onSortChange"
                @on-search="onSearch"
                :search-options="{
                    placeholder: $t('Search_this_table'),
                    enabled: true,
                 }"
                :select-options="{
                    enabled: true ,
                    clearSelectionText: '',
                }"
                @on-selected-rows-change="selectionChanged"
                :pagination-options="{
                    enabled: true,
                    mode: 'records',
                    nextLabel: 'next',
                    prevLabel: 'prev',
                }"
                :styleClass="showDropdown?'tableOne table-hover vgt-table full-height':'tableOne table-hover vgt-table non-height'"
            >
                <div slot="selected-row-actions">
                    <button class="btn btn-danger btn-sm" @click="delete_by_selected()">{{$t('Del')}}</button>
                </div>
                <div slot="table-actions" class="mt-2 mb-3">
                    <b-button variant="outline-info ripple m-1" size="sm" v-b-toggle.sidebar-right>
                        <i class="i-Filter-2"></i>
                        {{ $t("Filter") }}
                    </b-button>
                    <b-button @click="Invoice_PDF()" size="sm" variant="outline-success ripple m-1">
                        <i class="i-File-Copy"></i> PDF
                    </b-button>
                    <vue-excel-xlsx
                        class="btn btn-sm btn-outline-danger ripple m-1"
                        :data="invoices"
                        :columns="columns"
                        :file-name="'invoices'"
                        :file-type="'xlsx'"
                        :sheet-name="'invoices'"
                    >
                        <i class="i-File-Excel"></i> EXCEL
                    </vue-excel-xlsx>
                    <router-link
                        class="btn-sm btn btn-primary ripple btn-icon m-1"
                        to="/app/invoices/store"
                    >
                        <span class="ul-btn__icon">
                          <i class="i-Add"></i>
                        </span>
                        <span class="ul-btn__text ml-1">{{$t('Add')}}</span>
                    </router-link>
                </div>

                <template slot="table-row" slot-scope="props">
                    <span v-if="props.column.field == 'actions'">
                        <div>
                            <b-dropdown
                                id="dropdown-left"
                                variant="link"
                                text="Left align"
                                toggle-class="text-decoration-none"
                                size="lg"
                                no-caret
                            >
                                <template v-slot:button-content class="_r_btn border-0">
                                    <span class="_dot _r_block-dot bg-dark"></span>
                                    <span class="_dot _r_block-dot bg-dark"></span>
                                    <span class="_dot _r_block-dot bg-dark"></span>
                                </template>
                                <b-dropdown-item title="Show" :to="'/app/invoices/detail/'+props.row.id">
                                    <i class="nav-icon i-Eye font-weight-bold mr-2"></i>
                                    {{$t('Invoice Detail')}}
                                </b-dropdown-item>
                                <b-dropdown-item
                                    title="Edit"
                                    :to="'/app/invoices/edit/'+props.row.id"
                                >
                                    <i class="nav-icon i-Pen-2 font-weight-bold mr-2"></i>
                                    {{$t('Edit Invoice')}}
                                </b-dropdown-item>
                                <b-dropdown-item title="PDF" @click="Invoice_PDF(props.row , props.row.id)">
                                    <i class="nav-icon i-File-TXT font-weight-bold mr-2"></i>
                                    {{$t('DownloadPdf')}}
                                </b-dropdown-item>
                                <b-dropdown-item title="Email" @click="Send_Email(props.row.id)">
                                    <i class="nav-icon i-Envelope-2 font-weight-bold mr-2"></i>
                                    {{$t('email_notification')}}
                                </b-dropdown-item>
                                <b-dropdown-item
                                    title="Delete"
                                    @click="Remove_Invoice(props.row.id)"
                                >
                                    <i class="nav-icon i-Close-Window font-weight-bold mr-2"></i>
                                    {{$t('Delete Invoice')}}
                                </b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </span>
                </template>
            </vue-good-table>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";
import jsPDF from "jspdf";
import "jspdf-autotable";

export default {
    metaInfo: {
        title: "Invoices"
    },
    data() {
        return {
            isLoading: false,
            serverParams: {
                sort: {
                    field: "id",
                    type: "desc"
                },
                page: 1,
                perPage: 10
            },
            selectedIds: [],
            search: "",
            totalRows: "",
            showDropdown: false,
            Invoice_id: "",
            suppliers: [],
            clients: [],
            details: [],
            invoices: [],
            invoice: {},
            limit: "10",
            email: {
                to: "",
                subject: "",
                message: "",
                client_name: "",
                invoice_Ref: ""
            },
            Filter_Supplier: "",
            Filter_status: "",
            Filter_Payment: "",
            Filter_warehouse: "",
            Filter_Ref: "",
            Filter_date: "",
        }
    },

    mounted() {
        this.$root.$on("bv::dropdown::show", bvEvent => {
            this.showDropdown = true;
        });
        this.$root.$on("bv::dropdown::hide", bvEvent => {
            this.showDropdown = false;
        });
    },

    computed: {
        ...mapGetters(["currentUserPermissions", "currentUser"]),
        columns() {
            return [
                {
                    label: this.$t("date"),
                    field: "date",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Reference"),
                    field: "ref",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Supplier"),
                    field: "provider_name",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Client"),
                    field: "client_name",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Total"),
                    field: "grand_total",
                    // type: "decimal",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Warranty"),
                    field: "warranty",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Created By"),
                    field: "created_by",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Action"),
                    field: "actions",
                    html: true,
                    tdClass: "text-right",
                    thClass: "text-right",
                    sortable: false
                }
            ];
        },
    },

    methods: {
        //---Validate State Fields
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },

        //------ Toast
        makeToast(variant, msg, title) {
            this.$root.$bvToast.toast(msg, {
                title: title,
                variant: variant,
                solid: true
            });
        },

        //---------------------------------------- Set To Strings-------------------------\\
        setToStrings() {
            // Simply replaces null values with strings=''
            if (this.Filter_Supplier === null) {
                this.Filter_Supplier = "";
            }
            else if (this.Filter_warehouse === null) {
                this.Filter_warehouse = "";
            } else if (this.Filter_status === null) {
                this.Filter_status = "";
            } else if (this.Filter_Payment === null) {
                this.Filter_Payment = "";
            }
        },

        //------------------------------------------------ Get All Purchases -------------------------------\\
        Get_Purchases(page) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            this.setToStrings();
            axios
                .get(
                    "invoices?page=" +
                    page +
                    "&ref=" +
                    this.Filter_Ref +
                    "&date=" +
                    this.Filter_date +
                    "&provider_id=" +
                    this.Filter_Supplier +
                    // "&statut=" +
                    // this.Filter_status +
                    // "&warehouse_id=" +
                    // this.Filter_warehouse +
                    // "&payment_statut=" +
                    // this.Filter_Payment +
                    "&SortField=" +
                    this.serverParams.sort.field +
                    "&SortType=" +
                    this.serverParams.sort.type +
                    "&search=" +
                    this.search +
                    "&limit=" +
                    this.limit
                )
                .then(response => {
                    this.invoices = response.data.invoices;
                    this.suppliers = response.data.suppliers;
                    this.customers = response.data.customers;
                    this.totalRows = response.data.totalRows;

                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    this.isLoading = false;
                })
                .catch(response => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    this.isLoading = false;
                });
        },

        //--------------------------- Invoice Purchase -------------------------------\\
        Invoice_PDF(invoice, id) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);

            axios
                .get("invoice_pdf/" + id, {
                    responseType: "blob", // important
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "Invoice-" + invoice.ref + ".pdf");
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
    },

    //-----------------------------Created function-------------------
    created: function() {
        this.Get_Purchases(1);
    }
}
</script>
