<template>
  <div>
    <h2>Send Mail</h2>
    <form>
      <div>
        <label for="senderName">Sender Name</label>
        <input id="senderName" v-model="senderName" type="text" />
      </div>

      <div>
        <label for="senderEmail">Sender Email</label>
        <input id="senderEmail" v-model="senderEmail" type="text" />
      </div>

      <div>
        <label for="title">Title</label>
        <input id="title" v-model="title" type="text" />
      </div>

      <div>
        <label for="to">Recipients</label>
        <input id="to" v-model="to" type="text" />
      </div>

      <div>
        <label for="body_txt">Content (text)</label>
        <input id="body_txt" v-model="body_txt" type="text" />
      </div>

      <div>
        <label for="body_html">Content (html)</label>
        <input id="body_html" v-model="body_html" type="text" />
      </div>

      <button v-on:click="sendMail" type="button">Send Mail</button>

      <span>{{result}}</span>
    </form>
  </div>
</template>

<script>
import MailService from "../services/api/MailService";
export default {
  data: function() {
    return {
      title: "",
      senderName: "",
      senderEmail: "",
      to: "",
      body_txt: "",
      body_html: "",
      result: ""
    };
  },
  methods: {
    sendMail: function() {
      MailService.sendMail(
        this.title,
        this.senderName,
        this.senderEmail,
        this.to,
        this.body_txt,
        this.body_html
      ).then(mail => {
        if (mail && mail.status && mail.id) {
          this.result = mail.id + " - " + mail.status;
        }
      });
    }
  }
};
</script>
