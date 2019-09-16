<template>
  <form class="form-inline">
    <input
      class="form-control mr-sm-2"
      v-model="id"
      v-bind:class="{ 'is-invalid': hasError }"
      type="search"
      placeholder="Find mail by ID"
      aria-label="Search"
    />

    <button
      class="btn btn-primary my-2 my-sm-0"
      v-on:click="getMail"
      v-bind:class="{'btn-outline-danger': hasError}"
      v-bind:disabled="isLoading"
      type="button"
    >
      <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <span v-if="!isLoading">Search</span>
    </button>
  </form>
</template>

<script>
import store from "../stores";
export default {
  data() {
    return {
      status: "",
      id: ""
    };
  },
  computed: {
    isLoading() {
      return store.getters.isLoadingMail;
    },
    hasError() {
        return store.getters.hasLoadMailError;
    }
  },
  methods: {
    getMail() {
      store.dispatch("getMail", { id: this.id });
    }
  }
};
</script>
