<template>
  <div class="main-content">
    <breadcumb :page="$t('Create_Permission')" :folder="$t('Settings')"/>

    <validation-observer ref="Create_Permission">
      <b-form @submit.prevent="Submit_Permission">
        <b-row>
          <b-col lg="12" md="12" sm="12">
            <b-card>
              <b-row>
                <!-- Role Name -->
                <b-col md="6">
                  <validation-provider
                    name="Role name"
                    :rules="{ required: true}"
                    v-slot="validationContext"
                  >
                    <b-form-group :label="$t('RoleName') + ' ' + '*'">
                      <b-form-input
                        :placeholder="$t('Enter_Role_Name')"
                        :state="getValidationState(validationContext)"
                        aria-describedby="Role-feedback"
                        v-model="role.name"
                      ></b-form-input>
                      <b-form-invalid-feedback id="Role-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <!-- Role Description -->
                <b-col md="6">
                  <b-form-group :label="$t('RoleDescription')">
                    <b-form-input
                      :placeholder="$t('Enter_Role_Description')"
                      v-model="role.description"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-row class="mt-4">
                <!--dashboard -->
                <b-col md="4">
                  <b-card no-body class="ul-card__border-radius">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                      <b-button
                        class="card-title mb-0"
                        block
                        href="#"
                        v-b-toggle.panel-dashboard
                        variant="transparent"
                      >{{$t('dashboard')}}</b-button>
                    </b-card-header>
                    <b-collapse
                      id="panel-dashboard "
                      :visible="true"
                      accordion="my-dashboard"
                      role="tabpanel"
                    >
                      <b-card-body>
                        <b-card-text>
                          <b-row>
                            <!--dashboard -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="dashboard"
                                >
                                <span>{{$t('dashboard')}} <i v-b-tooltip.hover.bottom title="if unchecked only welcome message will be displayed in dashboard" class="text-info text-15 font-weight-bold i-Speach-BubbleAsking"></i></span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                          </b-row>
                        </b-card-text>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </b-col>

                <!--Users -->
                <b-col md="4">
                  <b-card no-body class="ul-card__border-radius">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                      <b-button
                        class="card-title mb-0"
                        block
                        href="#"
                        v-b-toggle.panel-UserManagement
                        variant="transparent"
                      >{{$t('UserManagement')}}</b-button>
                    </b-card-header>
                    <b-collapse
                      id="panel-UserManagement "
                      :visible="true"
                      accordion="my-accordion1"
                      role="tabpanel"
                    >
                      <b-card-body>
                        <b-card-text>
                          <b-row>
                            <!--Users View -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="users_view"
                                >
                                <span>{{$t('View')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Users ADD -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="users_add"
                                >
                                <span>{{$t('Add')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Users Edit -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="users_edit"
                                >
                                <span>{{$t('Edit')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Users Delete -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="users_delete"
                                >
                                <span>{{$t('Del')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Users record view -->
                            <b-col md="12">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="record_view"
                                >
                                <span>{{$t('ShowAll')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                          </b-row>
                        </b-card-text>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </b-col>

                <!--  Permissions -->
                <b-col md="4">
                  <b-card no-body class="ul-card__border-radius">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                      <b-button
                        class="card-title mb-0"
                        block
                        href="#"
                        v-b-toggle.panel-Permissions
                        variant="transparent"
                      >{{$t('UserPermissions')}}</b-button>
                    </b-card-header>
                    <b-collapse
                      id="panel-Permissions "
                      :visible="true"
                      accordion="my-accordion2"
                      role="tabpanel"
                    >
                      <b-card-body>
                        <b-card-text>
                          <b-row>
                            <!--Permissions View -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="permissions_view"
                                >
                                <span>{{$t('View')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Permissions ADD -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="permissions_add"
                                >
                                <span>{{$t('Add')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Permissions Edit -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="permissions_edit"
                                >
                                <span>{{$t('Edit')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Permissions Delete -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="permissions_delete"
                                >
                                <span>{{$t('Del')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                          </b-row>
                        </b-card-text>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </b-col>

                <!--  Invoices -->
                <b-col md="4">
                  <b-card no-body class="ul-card__border-radius">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                      <b-button
                        class="card-title mb-0"
                        block
                        href="#"
                        v-b-toggle.panel-Invoices
                        variant="transparent"
                      >{{$t('Invoices')}}</b-button>
                    </b-card-header>
                    <b-collapse
                      id="panel-Invoices"
                      :visible="true"
                      accordion="my-accordion3"
                      role="tabpanel"
                    >
                      <b-card-body>
                        <b-card-text>
                          <b-row>
                            <!--Products View -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="invoice_view"
                                >
                                <span>{{$t('View')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Products ADD -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="invoice_add"
                                >
                                <span>{{$t('Add')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Products Edit -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="invoice_edit"
                                >
                                <span>{{$t('Edit')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Products Delete -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="invoice_delete"
                                >
                                <span>{{$t('Del')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                          </b-row>
                        </b-card-text>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </b-col>

                <!-- Customers -->
                <b-col md="4">
                  <b-card no-body class="ul-card__border-radius">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                      <b-button
                        class="card-title mb-0"
                        block
                        href="#"
                        v-b-toggle.panel-Customers
                        variant="transparent"
                      >{{$t('Customers')}}</b-button>
                    </b-card-header>
                    <b-collapse
                      id="panel-Customers"
                      :visible="true"
                      accordion="my-accordion15"
                      role="tabpanel"
                    >
                      <b-card-body>
                        <b-card-text>
                          <b-row>
                            <!--Customers View -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Customers_view"
                                >
                                <span>{{$t('View')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Customers ADD -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Customers_add"
                                >
                                <span>{{$t('Add')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Customers Edit -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Customers_edit"
                                >
                                <span>{{$t('Edit')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Customers Delete -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Customers_delete"
                                >
                                <span>{{$t('Del')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>

                            <!--Customers Import -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="customers_import"
                                >
                                <span>{{$t('Import_Customers')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                          </b-row>
                        </b-card-text>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </b-col>

                <!-- Suppliers -->
                <b-col md="4">
                  <b-card no-body class="ul-card__border-radius">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                      <b-button
                        class="card-title mb-0"
                        block
                        href="#"
                        v-b-toggle.panel-Suppliers
                        variant="transparent"
                      >{{$t('Suppliers')}}</b-button>
                    </b-card-header>
                    <b-collapse
                      id="panel-Suppliers"
                      :visible="true"
                      accordion="my-accordion16"
                      role="tabpanel"
                    >
                      <b-card-body>
                        <b-card-text>
                          <b-row>
                            <!--Suppliers View -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Suppliers_view"
                                >
                                <span>{{$t('View')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Suppliers ADD -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Suppliers_add"
                                >
                                <span>{{$t('Add')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Suppliers Edit -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Suppliers_edit"
                                >
                                <span>{{$t('Edit')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                            <!--Suppliers Delete -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Suppliers_delete"
                                >
                                <span>{{$t('Del')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>

                            <!--Suppliers Import -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="Suppliers_import"
                                >
                                <span>{{$t('Import_Suppliers')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                          </b-row>
                        </b-card-text>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </b-col>

                <!-- Settings -->
                <b-col md="6">
                  <b-card no-body class="ul-card__border-radius">
                    <b-card-header header-tag="header" class="p-1" role="tab">
                      <b-button
                        class="card-title mb-0"
                        block
                        href="#"
                        v-b-toggle.panel-Settings
                        variant="transparent"
                      >{{$t('Settings')}}</b-button>
                    </b-card-header>
                    <b-collapse
                      id="panel-Settings"
                      :visible="true"
                      accordion="my-accordion18"
                      role="tabpanel"
                    >
                      <b-card-body>
                        <b-card-text>
                          <b-row>
                            <!--Settings System -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="setting_system"
                                >
                                <span>{{$t('SystemSettings')}} + {{$t('update_settings')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>

                             <!--notification_template  -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="notification_template"
                                >
                                <span>{{$t('notification_template')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>

                             <!--mail_settings  -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="mail_settings"
                                >
                                <span>{{$t('mail_settings')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>

                            <!--Currency  -->
                            <b-col md="6">
                              <label class="checkbox checkbox-outline-primary">
                                <input
                                  type="checkbox"
                                  checked
                                  v-model="permissions"
                                  value="currency"
                                >
                                <span>{{$t('Currencies')}}</span>
                                <span class="checkmark"></span>
                              </label>
                            </b-col>
                          </b-row>
                        </b-card-text>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </b-col>
              </b-row>
              <!-- End row -->

               <b-col md="12">
                <b-button variant="primary" type="submit"  :disabled="SubmitProcessing"><i class="i-Yes me-2 font-weight-bold"></i> {{$t('submit')}}</b-button>
                  <div v-once class="typo__p" v-if="SubmitProcessing">
                    <div class="spinner sm spinner-primary mt-3"></div>
                  </div>
                </b-col>

            </b-card>
          </b-col>
        </b-row>
      </b-form>
    </validation-observer>
  </div>
</template>

<script>
import NProgress from "nprogress";

export default {
  metaInfo: {
    title: "Create Permissions"
  },
  data() {
    return {
      SubmitProcessing:false,
      isLoading: true,
      permissions: [],
      role: {
        name: "",
        description: ""
      }
    };
  },

  methods: {
    //------------- Submit Validation Create Permissions
    Submit_Permission() {
      this.$refs.Create_Permission.validate().then(success => {
        if (!success) {
          this.makeToast(
            "danger",
            this.$t("Please_fill_the_form_correctly"),
            this.$t("Failed")
          );
        } else {
          this.Create_Permission();
        }
      });
    },

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


    //------------------------ Create Permissions -------------------\\
    Create_Permission() {
      this.SubmitProcessing = true;
      NProgress.start();
      NProgress.set(0.1);
      axios
        .post("roles", {
          role: this.role,
          permissions: this.permissions
        })
        .then(response => {
          this.SubmitProcessing = false;
          NProgress.done();
          this.makeToast(
            "success",
            this.$t("Create.TitleRole"),
            this.$t("Success")
          );

          this.$router.push({
            name: "groupPermission"
          });
        })
        .catch(error => {
          this.SubmitProcessing = false;
          NProgress.done();
          this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
        });
    }
  }, //end Methods

  created: function() {
  }
};
</script>
