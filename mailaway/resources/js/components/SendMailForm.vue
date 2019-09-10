<template>
  <div>
    <h2>Send Mail</h2>
    <form>
      <div class="alert alert-danger" v-if="errorMessage" role="alert">{{errorMessage}}</div>
      <div class="form-group">
        <label for="fromName">Sender Name</label>
        <input
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.fromName }"
          id="fromName"
          v-model="fromName"
          type="text"
        />
        <div v-if="errors.fromName" class="invalid-feedback">{{errors.fromName.join(' ')}}</div>
      </div>

      <div class="form-group">
        <label for="fromEmail">Sender Email</label>
        <input
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.fromEmail }"
          id="fromEmail"
          v-model="fromEmail"
          type="text"
        />
        <div v-if="errors.fromEmail" class="invalid-feedback">{{errors.fromEmail.join(' ')}}</div>
      </div>

      <div class="form-group">
        <label for="title">Title</label>
        <input
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.title }"
          id="title"
          v-model="title"
          type="text"
        />
        <div v-if="errors.title" class="invalid-feedback">{{errors.title.join(' ')}}</div>
      </div>

      <div class="form-group">
        <label for="to">Recipients</label>
        <input
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.to }"
          id="to"
          v-model="to"
          type="text"
        />
        <div v-if="errors.to" class="invalid-feedback">{{errors.to.join(' ')}}</div>
      </div>

      <div class="form-group">
        <label for="body_txt">Content (text)</label>
        <input
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.body_txt }"
          id="body_txt"
          v-model="body_txt"
          type="text"
        />
        <div v-if="errors.body_txt" class="invalid-feedback">{{errors.body_txt.join(' ')}}</div>
      </div>

      <div class="form-group">
        <label for="body_html">Content (html)</label>
        <input
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.body_html }"
          id="body_html"
          v-model="body_html"
          type="text"
        />
        <div v-if="errors.body_html" class="invalid-feedback">{{errors.body_html.join(' ')}}</div>
      </div>

      <div class="form-group">
        <button class="btn btn-primary" v-on:click="sendMail" type="button">Send Mail</button>
        <div v-if="isLoading" class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <span>{{result}}</span>
      </div>
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
      errors: [],
      errorMessage: "",
      isLoading: false
    };
  },
  methods: {
    sendMail: function() {
      this.isLoading = true;
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
            this.isLoading = false;
          }
        })
        .catch(error => {
          if (error.response.status == 422) {
            this.errors = error.response.data.errors;
            this.errorMessage = error.response.data.message;
          }
          this.isLoading = false;
        });
    }
  }
};
</script>
