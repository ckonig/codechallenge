<template>
  <div v-if="showForm">
    <h2>Send Mail</h2>
    <form>
      <div class="alert alert-danger" v-if="errorMessage" role="alert">{{errorMessage}}</div>
      <div class="alert alert-info text-center" v-if="isLoading" role="alert">
        <div class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div class="alert alert-success" v-if="result" role="alert">{{result}}</div>
      <div class="form-row">
        <div class="col">
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
        </div>
        <div class="col">
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
        </div>
      </div>
      <div class="form-row">
        <div class="col-4">
          <label for="newRecipient">Add Recipient</label>
          <input class="form-control" v-model="newRecipient" id="newRecipient" type="text" />
        </div>
        <div class="col-2">
          <label>&nbsp;</label>
          <button type="button" v-on:click="addRecipient" class="form-control btn btn-secondary">Add</button>
        </div>
        <div class="col-6">
          <div class="form-group">
            <label for="to">Recipients</label>
            <input
              class="form-control"
              v-bind:class="{ 'is-invalid': errors.to }"
              id="to"
              v-model="to"
              type="text"
            />
            <div v-if="errors.to" class="invalid-feedback">{{errors.to}}</div>
          </div>
        </div>
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
        <label for="body_txt">Content (text)</label>
        <textarea
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.body_txt }"
          id="body_txt"
          v-model="body_txt"
          type="text"
          rows="4"
        ></textarea>
        <div v-if="errors.body_txt" class="invalid-feedback">{{errors.body_txt.join(' ')}}</div>
      </div>

      <div class="form-group">
        <label for="body_html">Content (html)</label>
        <textarea
          class="form-control"
          v-bind:class="{ 'is-invalid': errors.body_html }"
          id="body_html"
          v-model="body_html"
          type="text"
          rows="4"
        ></textarea>
        <div v-if="errors.body_html" class="invalid-feedback">{{errors.body_html.join(' ')}}</div>
      </div>

      <div class="form-group">
        <button class="btn btn-primary" v-on:click="sendMail" type="button">Send Mail</button>
        <button class="btn btn-secondary" v-on:click="resetDraft" type="button">Reset Form</button>
      </div>
    </form>
  </div>
</template>

<script>
import store from "../stores";
import MailService from "../services/api/MailService";
export default {
  data: function() {
    return {
      newRecipient: "",
      title: "",
      fromName: "",
      fromEmail: "",
      newRecipient: "",
      to: "",
      body_txt: "",
      body_html: ""
    };
  },
  computed: {
    result() {
      return store.getters.draft.result;
    },
    errors() {
      return store.getters.draft.errors;
    },
    errorMessage() {
      return store.getters.draft.errorMessage;
    },
    isLoading() {
      return store.getters.draft.isLoading;
    },
    showForm() {
        //@todo the form should not know about this separate concern, use route navigation instead
        return !store.getters.getActiveMail;
    }
  },
  methods: {
    resetDraft: function() {
      this.newRecipient = "";
      this.title = "";
      this.fromName = "";
      this.fromEmail = "";
      this.newRecipient = "";
      this.to = "";
      this.body_txt = "";
      this.body_html = "";
      store.dispatch("resetDraft", {});
    },
    sendMail: function() {
      store.dispatch("sendDraft", { draft: { ...this } });
    },
    addRecipient: function() {
      if (this.to) {
        this.to += ";";
      }
      this.to += this.newRecipient;
      this.newRecipient = "";
    }
  }
};
</script>
