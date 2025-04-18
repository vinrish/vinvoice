import Vue from "vue";
import store from "./store";
import { i18n } from "./plugins/i18n";
import authenticate from "./auth/authenticate";
import IsConnected from "./auth/IsConnected";
import NProgress from "nprogress";
import Router from "vue-router";
Vue.use(Router);

// create new router

const baseRoutes = [
    {
        path: "/",
        component: () => import("./views/app"),
        redirect: "/app/dashboard",
        name: 'app',

        children: [
            {
                path: "/app/dashboard",
                name: "dashboard",
                component: () =>
                    import(
                        /* webpackChunkName: "dashboard" */ "./views/app/dashboard/dashboard"
                    )

            },

            // Invoices
            {
                path: "/app/invoices",
                component: () =>
                    import(
                        /* webpackChunkName: "invoices" */ "./views/app/pages/invoices"
                        ),
                redirect: "/app/invoices/list",
                children: [
                    {
                        name: "index_invoice",
                        path: "list",
                        component: () =>
                            import(
                                "./views/app/pages/invoices/index_invoice"
                                )
                    },
                    {
                        name: "store_invoice",
                        path: "store",
                        component: () =>
                            import(
                                /* webpackChunkName: "store_invoice" */
                                "./views/app/pages/invoices/create_invoice"
                                )
                    },
                    {
                        name: "edit_invoice",
                        path: "edit/:id",
                        component: () =>
                            import(
                                /* webpackChunkName: "edit_invoice" */
                                "./views/app/pages/invoices/edit_invoice"
                                )
                    },
                    {
                        name: "detail_invoice",
                        path: "detail/:id",
                        component: () =>
                            import(
                                /* webpackChunkName: "detail_invoice" */
                                "./views/app/pages/invoices/detail_invoice"
                                )
                    },
                ]
            },

            // Invoices2
            {
                path: "/app/invoices2",
                component: () =>
                    import(
                        /* webpackChunkName: "invoices2" */ "./views/app/pages/invoices2"
                        ),
                redirect: "/app/invoices2/list",
                children: [
                    {
                        name: "index_invoice",
                        path: "list",
                        component: () =>
                            import(
                                "./views/app/pages/invoices2/index_invoice"
                                )
                    },
                    {
                        name: "store_invoice",
                        path: "store",
                        component: () =>
                            import(
                                /* webpackChunkName: "store_invoice" */
                                "./views/app/pages/invoices2/create_invoice"
                                )
                    },
                    {
                        name: "edit_invoice",
                        path: "edit/:id",
                        component: () =>
                            import(
                                /* webpackChunkName: "edit_invoice" */
                                "./views/app/pages/invoices2/edit_invoice"
                                )
                    },
                    {
                        name: "detail_invoice",
                        path: "detail/:id",
                        component: () =>
                            import(
                                /* webpackChunkName: "detail_invoice" */
                                "./views/app/pages/invoices2/detail_invoice"
                                )
                    },
                ]
            },

            // People
            {
                path: "/app/People",
                component: () =>
                    import(
                        /* webpackChunkName: "People" */ "./views/app/pages/people"
                        ),
                redirect: "/app/People/Customers",
                children: [
                    // Customers
                    {
                        name: "Customers",
                        path: "Customers",
                        component: () =>
                            import(
                                /* webpackChunkName: "Customers" */ "./views/app/pages/people/customers"
                                )
                    },

                    // Customers
                    {
                        name: "Customers_without_ecommerce",
                        path: "Customers_without_ecommerce",
                        component: () =>
                            import(
                                /* webpackChunkName: "Customers_without_ecommerce" */ "./views/app/pages/people/Customers_without_ecommerce"
                                )
                    },

                    // Suppliers
                    {
                        name: "Suppliers",
                        path: "Suppliers",
                        component: () =>
                            import(
                                /* webpackChunkName: "Suppliers" */ "./views/app/pages/people/providers"
                                )
                    },

                    // Users
                    {
                        name: "user",
                        path: "Users",
                        component: () =>
                            import(
                                /* webpackChunkName: "Users" */ "./views/app/pages/people/users"
                                )
                    }
                ]
            },

            // Settings
            {
                path: "/app/settings",
                component: () =>
                    import(
                        /* webpackChunkName: "settings" */ "./views/app/pages/settings"
                    ),
                redirect: "/app/settings/System_settings",
                children: [
                    // Permissions
                    {
                        path: "permissions",
                        component: () =>
                            import(
                                /* webpackChunkName: "permissions" */ "./views/app/pages/settings/permissions"
                            ),
                        redirect: "/app/settings/permissions/list",
                        children: [
                            {
                                name: "groupPermission",
                                path: "list",
                                component: () =>
                                    import(
                                        /* webpackChunkName: "groupPermission" */
                                        "./views/app/pages/settings/permissions/Permissions"
                                    )
                            },
                            {
                                name: "store_permission",
                                path: "store",
                                component: () =>
                                    import(
                                        /* webpackChunkName: "store_permission" */
                                        "./views/app/pages/settings/permissions/Create_permission"
                                    )
                            },
                            {
                                name: "edit_permission",
                                path: "edit/:id",
                                component: () =>
                                    import(
                                        /* webpackChunkName: "edit_permission" */
                                        "./views/app/pages/settings/permissions/Edit_permission"
                                    )
                            }
                        ]
                    },

                     // email_templates
                     {
                        name: "email_templates",
                        path: "email_templates",
                        component: () =>
                            import(
                                /* webpackChunkName: "email_templates" */ "./views/app/pages/settings/email_templates"
                            )
                    },

                    // mail_settings
                     {
                        name: "mail_settings",
                        path: "mail_settings",
                        component: () =>
                            import(
                                /* webpackChunkName: "mail_settings" */ "./views/app/pages/settings/mail_settings"
                            )
                     },

                    // update_settings
                     {
                        name: "update_settings",
                        path: "update_settings",
                        component: () =>
                            import(
                                /* webpackChunkName: "update_settings" */ "./views/app/pages/settings/update_settings"
                            )
                        },

                    // currencies
                    {
                        name: "currencies",
                        path: "Currencies",
                        component: () =>
                            import(
                                /* webpackChunkName: "Currencies" */ "./views/app/pages/settings/currencies"
                            )
                    },

                    // Backup
                    {
                        name: "Backup",
                        path: "Backup",
                        component: () =>
                            import(
                                /* webpackChunkName: "Backup" */ "./views/app/pages/settings/backup"
                            )
                    },

                    // System Settings
                    {
                        name: "system_settings",
                        path: "System_settings",
                        component: () =>
                            import(
                                /* webpackChunkName: "System_settings" */ "./views/app/pages/settings/system_settings"
                            )
                    }

                ]
            },

            {
                name: "profile",
                path: "/app/profile",
                component: () =>
                    import(
                        /* webpackChunkName: "profile" */ "./views/app/pages/profile"
                    )
            }
        ]
    },

    {
        path: "*",
        name: "NotFound",
        component: () =>
            import(
                /* webpackChunkName: "NotFound" */ "./views/app/pages/notFound"
            )
    },

    {
        path: "not_authorize",
        name: "not_authorize",
        component: () =>
            import(
                /* webpackChunkName: "not_authorize" */ "./views/app/pages/NotAuthorize"
            )
    }
];

const router = new Router({
    mode: "history",
    linkActiveClass: "open",
    routes: baseRoutes,
    scrollBehavior(to, from, savedPosition) {
        return { x: 0, y: 0 };
    }
});

const originalPush = Router.prototype.push;
Router.prototype.push = function push(location, onResolve, onReject) {
    if (onResolve || onReject)
        return originalPush.call(this, location, onResolve, onReject);
    return originalPush.call(this, location).catch(err => err);
};

router.beforeEach((to, from, next) => {
    // If this isn't an initial page load.
    if (to.path) {
        // Start the route progress bar.
        NProgress.start();
        NProgress.set(0.1);
    }
    next();

    if (
        store.state.language.language &&
        store.state.language.language !== i18n.locale
    ) {
        i18n.locale = store.state.language.language;
        next();
    } else if (!store.state.language.language) {
        store.dispatch("language/setLanguage", navigator.languages).then(() => {
            i18n.locale = store.state.language.language;
            next();
        });
    } else {
        next();
    }
});

router.afterEach(() => {
    // Remove initial loading
    const gullPreLoading = document.getElementById("loading_wrap");
    if (gullPreLoading) {
        gullPreLoading.style.display = "none";
    }
    // Complete the animation of the route progress bar.
    setTimeout(() => NProgress.done(), 500);
    // NProgress.done();

    if (window.innerWidth <= 1200) {
        store.dispatch("changeSidebarProperties");
        if (store.getters.getSideBarToggleProperties.isSecondarySideNavOpen) {
            store.dispatch("changeSecondarySidebarProperties");
        }

        if (store.getters.getCompactSideBarToggleProperties.isSideNavOpen) {
            store.dispatch("changeCompactSidebarProperties");
        }
    } else {
        if (store.getters.getSideBarToggleProperties.isSecondarySideNavOpen) {
            store.dispatch("changeSecondarySidebarProperties");
        }
    }
});

async function Check_Token(to, from, next) {
    let token = to.params.token;
    const res = await axios
        .get("password/find/" + token)
        .then(response => response.data);

    if (!res.success) {
        next("/app/sessions/signIn");
    } else {
        return next();
    }
}

export default router;
