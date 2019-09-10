<template>
  <div>
    <h2>Check Mail</h2>
    <form>
      <div class="form-group">
        <label for="mailid">Mail ID</label>
        <input id="mailid" type="text" v-model="id" />
      </div>
      <div class="form-group">
        <button class="btn btn-primary" v-on:click="checkStatus" type="button">Check Status</button>
        <div v-if="isLoading" class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <span>status: {{status}}</span>
      </div>
    </form>
  </div>
</template>

<script>
import MailService from "../services/api/MailService";

export default {
  data() {
    return {
      status: "",
      id: "",
      isLoading: false
    };
  },
  methods: {
    checkStatus: function() {
      if (this.id) {
          this.isLoading = true;
        MailService.getMail(this.id)
          .then(mail => {
            this.status = mail.status;
            this.isLoading = false;
          })
          .catch(error => {
            if (error.response.status == 405) {
              this.status = "mail not found";
            }
            this.isLoading = false;
          });
      } else {
        this.status = "invalid ID";
      }
    }
  }
};
</script>
