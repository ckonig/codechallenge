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
      MailService.getMail(this.id).then(mail => {
        console.log(mail);
        this.status = mail.status;
      });
    }
  }
};
</script>
