<template>
  <div>
    <h2>History</h2>
    <div v-for="(mail, index) in mails" v-bind:key="index">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            Mail #{{mail.id}}
            <span
              class="badge badge-pill"
              v-bind:class="{
          'badge-success': mail.status == 'sent',
          'badge-warning': mail.status == 'retry',
          'badge-danger': mail.status == 'cancelled',
          'badge-info': mail.status == 'queued',
          }"
            >{{mail.status}}</span>
          </h5>
          <a href="#" class="card-link">View Details</a>
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
    },
    activeMail() {
      return store.getters.getActiveMail;
    }
  }
};
</script>
