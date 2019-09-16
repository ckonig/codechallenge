<template>
  <div>
    <h2 v-if="mails.length">History</h2>
    <div v-for="(mail, index) in mails" v-bind:key="index">
      <div class="card">
        <div class="card-header">
          <div class>
            <div class="row">
              <div class="col-6">
                <h5>#{{mail.id}}</h5>
              </div>
              <div class="col-6 text-right">
                <h5>
                  <span
                    class="badge badge-primary"
                    v-bind:class="{
                        'badge-success': mail.status == 'sent',
                        'badge-warning': mail.status == 'retry',
                        'badge-danger': mail.status == 'cancelled',
                        'badge-info': mail.status == 'queued',
                    }"
                  >{{mail.status}}</span>
                </h5>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="card-subtitle mb-2 text-muted">{{mail.updated_at}}</h6>
          <a href="#" v-on:click="getMail(mail.id)" class="card-link">Refresh</a>
          <a href="#" v-on:click="setActiveMail(mail.id)" class="card-link">Show</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import store from "../stores";
export default {
  computed: {
    mails() {
      return store.getters.getMails;
    }
  },
  methods: {
    getMail(id) {
      store.dispatch("getMail", { id });
    },
    setActiveMail(id) {
      store.dispatch("setActiveMail", { id });
    }
  }
};
</script>
