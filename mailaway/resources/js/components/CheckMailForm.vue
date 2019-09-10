<template>
  <div>
    <h2>Check Mail</h2>
    <form>
      <div>
        <label for="mailid">Mail ID</label>
        <input id="mailid" type="text" v-model="id" />
      </div>
      <button v-on:click="checkStatus" type="button">Check Status</button>
      <span>status: {{status}}</span>
    </form>
  </div>
</template>

<script>
import MailService from "../services/api/MailService";

export default {
  data() {
    return {
      status: "",
      id: ""
    };
  },
  methods: {
    checkStatus: function() {
      if (this.id) {
        MailService.getMail(this.id)
          .then(mail => {
            this.status = mail.status;
          })
          .catch(error => {
            if (error.response.status == 405) {
              this.status = "mail not found";
            }
          });
      } else {
        this.status = "invalid ID";
      }
    }
  }
};
</script>
