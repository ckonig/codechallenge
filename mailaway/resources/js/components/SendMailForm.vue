<template>
  <div>
    <h2>Send Mail</h2>
    <form>
      <div>
        <label for="senderName">Sender Name</label>
        <input id="senderName" type="text" />
      </div>

      <div>
        <label for="senderEmail">Sender Email</label>
        <input id="senderEmail" type="text" />
      </div>

      <div>
        <label for="title">Title</label>
        <input id="title" type="text" />
      </div>

      <div>
        <label for="to">Recipients</label>
        <input id="to" type="text" />
      </div>

      <div>
        <label for="body_txt">Content (text)</label>
        <input id="body_txt" type="text" />
      </div>

      <div>
        <label for="body_html">Content (html)</label>
        <input id="body_html" type="text" />
      </div>

      <button v-on:click="sendMail" type="button">Send Mail</button>
    </form>
  </div>
</template>

<script>
import MailService from "../services/api/MailService";
export default {
  methods: {
    sendMail: function() {
      MailService.sendMail();
    }
  }
};
</script>
