<template>
  <div>
    <h2>Send Mail</h2>
    <form>
      <div>
        <label for="fromName">Sender Name</label>
        <input id="fromName" v-model="fromName" type="text" />
        <span v-if="errors.fromName">{{errors.fromName.join(' ')}}</span>
      </div>

      <div>
        <label for="fromEmail">Sender Email</label>
        <input id="fromEmail" v-model="fromEmail" type="text" />
        <span v-if="errors.fromEmail">{{errors.fromEmail.join(' ')}}</span>
      </div>

      <div>
        <label for="title">Title</label>
        <input id="title" v-model="title" type="text" />
        <span v-if="errors.title">{{errors.title.join(' ')}}</span>
      </div>

      <div>
        <label for="to">Recipients</label>
        <input id="to" v-model="to" type="text" />
        <span v-if="errors.to">{{errors.to.join(' ')}}</span>
      </div>

      <div>
        <label for="body_txt">Content (text)</label>
        <input id="body_txt" v-model="body_txt" type="text" />
        <span v-if="errors.body_txt">{{errors.body_txt.join(' ')}}</span>
      </div>

      <div>
        <label for="body_html">Content (html)</label>
        <input id="body_html" v-model="body_html" type="text" />
        <span v-if="errors.body_html">{{errors.body_html.join(' ')}}</span>
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
      fromName: "",
      fromEmail: "",
      to: "",
      body_txt: "",
      body_html: "",
      result: "",
      errors: []
    };
  },
  methods: {
    sendMail: function() {
      MailService.sendMail(
        this.title,
        this.fromName,
        this.fromEmail,
        this.to,
        this.body_txt,
        this.body_html
      )
        .then(mail => {
          if (mail && mail.status && mail.id) {
            this.result = mail.id + " - " + mail.status;
            this.title = "";
            this.fromName = "";
            this.fromEmail = "";
            this.to = "";
            this.body_txt = "";
            this.body_html = "";
          }
        })
        .catch(error => {
          if (error.response.status == 422) {
            this.errors = error.response.data.errors;
          }
        });
    }
  }
};
</script>
