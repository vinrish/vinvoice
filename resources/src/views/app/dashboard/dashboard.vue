<template>
  <!-- ============ Body content start ============= -->
  <div class="main-content">
    <div v-if="loading" class="loading_page spinner spinner-primary mr-3"></div>
    <div v-else-if="!loading && currentUserPermissions && currentUserPermissions.includes('dashboard')">

      <b-row>
        <!-- ICON BG -->

        <b-col lg="4" md="6" sm="12">
          <router-link tag="a" class to="/app/People/Customers">
            <b-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">
              <i class="i-User"></i>
              <div class="content">
                <p class="text-muted mt-2 mb-0">{{$t('Customers')}}</p>
                <p
                  class="text-primary text-24 line-height-1 mb-2"
                >{{ clients }}</p>
              </div>
            </b-card>
          </router-link>
        </b-col>

        <b-col lg="4" md="6" sm="12">
          <router-link tag="a" class to="/app/People/Suppliers">
            <b-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">
              <i class="i-Administrator"></i>
              <div class="content">
                <p class="text-muted mt-2 mb-0">{{$t('Suppliers')}}</p>
                <p
                  class="text-primary text-24 line-height-1 mb-2"
                >{{ suppliers }}</p>
              </div>
            </b-card>
          </router-link>
        </b-col>

        <b-col lg="4" md="6" sm="12">
          <router-link tag="a" class to="/app/invoices/list">
            <b-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">
              <i class="i-File-Clipboard"></i>
              <div class="content">
                <p class="text-muted mt-2 mb-0">{{$t('Invoices')}}</p>
                <p
                  class="text-primary text-24 line-height-1 mb-2"
                >{{ invoices }}</p>
              </div>
            </b-card>
          </router-link>
        </b-col>
      </b-row>
    </div>

    <div v-else>
      <h4>{{$t('Welcome_to_your_Dashboard')}}</h4>
    </div>

  </div>

  <!-- ============ Body content End ============= -->
</template>
<script>
import { mapGetters } from "vuex";

import ECharts from "vue-echarts/components/ECharts.vue";

// import ECharts modules manually to reduce bundle size
import "echarts/lib/chart/pie";
import "echarts/lib/chart/bar";
import "echarts/lib/chart/line";
import "echarts/lib/component/tooltip";
import "echarts/lib/component/legend";

export default {
  components: {
    "v-chart": ECharts
  },
  metaInfo: {
    // if no subcomponents specify a metaInfo.title, this title will be used
    title: "Dashboard"
  },
  data() {
    return {
        loading: true,
        invoices: 0,
        clients: 0,
        suppliers: 0,
    };
  },
  computed: {
    ...mapGetters(["currentUserPermissions", "currentUser"]),
    columns_sales() {
      return [
        {
          label: this.$t("Reference"),
          field: "Ref",
          tdClass: "gull-border-none text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("Customer"),
          field: "client_name",
          tdClass: "gull-border-none text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("warehouse"),
          field: "warehouse_name",
          tdClass: "text-left",
          thClass: "text-left"
        },
        {
          label: this.$t("Status"),
          field: "statut",
          html: true,
          tdClass: "gull-border-none text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("Total"),
          field: "GrandTotal",
          type: "decimal",
          tdClass: "gull-border-none text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("Paid"),
          field: "paid_amount",
          type: "decimal",
          tdClass: "gull-border-none text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("Due"),
          field: "due",
          type: "decimal",
          tdClass: "gull-border-none text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("PaymentStatus"),
          field: "payment_status",
          html: true,
          sortable: false,
          tdClass: "text-left gull-border-none",
          thClass: "text-left"
        }
      ];
    },
    columns_stock() {
      return [
        {
          label: this.$t("ProductCode"),
          field: "code",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("ProductName"),
          field: "name",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("warehouse"),
          field: "warehouse",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("Quantity"),
          field: "quantity",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("AlertQuantity"),
          field: "stock_alert",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        }
      ];
    },
    columns_products() {
      return [
        {
          label: this.$t("ProductName"),
          field: "name",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("TotalSales"),
          field: "total_sales",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        },
        {
          label: this.$t("TotalAmount"),
          field: "total",
          tdClass: "text-left",
          thClass: "text-left",
          sortable: false
        }
      ];
    }
  },
  methods: {

    //---------------------------------- Report Dashboard With Echart
    all_dashboard_data() {
      axios
        .get(
          "/dashboard_data")
        .then(response => {
            this.invoices = response.data.invoices;
            this.clients = response.data.clients;
            this.suppliers = response.data.suppliers;

            this.loading = false;
        })
        .catch(response => {});
    },

    //------------------------------Get Month -------------------------\\
    GetMonth() {
      var months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
      ];
      var now = new Date();
      this.CurrentMonth = months[now.getMonth()];
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
    }
  },
  async mounted() {
    await this.all_dashboard_data();
    this.GetMonth();
  }
};
</script>
