<template>
  <div v-if="Loading">
    <router-view></router-view>

    <customizer></customizer>
  </div>
</template>


<script>
import { mapActions, mapGetters } from "vuex";

export default {
  data() {
    return {
      Loading:false,
    };
  },
  computed: {

    ...mapGetters(["getThemeMode","isAuthenticated"]),
    themeName() {
      return this.getThemeMode.dark ? "dark-theme" : " ";
    },
    rtl() {
      return this.getThemeMode.rtl ? "rtl" : " ";
    }
  },

  metaInfo() {
    return {
      // if no subcomponents specify a metaInfo.title, this title will be used
      title: "Vtech",
      // all titles will be injected into this template
      titleTemplate: "%s | Ultimate Invoicing",
      bodyAttrs: {
        class: [this.themeName, "text-left"]
      },
      htmlAttrs: {
        dir: this.rtl
      },

    };
  },
methods:{
    ...mapActions([
      "refreshUserPermissions",
    ]),

},

 beforeMount() {
        this.refreshUserPermissions();
        setTimeout(() => this.Loading= true, 300);

  }

};
</script>

