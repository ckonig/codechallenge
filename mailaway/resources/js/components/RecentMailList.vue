<template>
  <div>
    <h2 v-if="mails.length">History</h2>
    <div v-if="showmodal">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">#{{activeMail.id}}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" @click="showmodal = false">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>{{activeMail.updated_at}}</p>
                  <p>From: {{activeMail.fromName}} ({{activeMail.fromEmail}})</p>
                  <p>Recipients:</p>
                  <ul v-for="(to, index) in JSON.parse(activeMail.to)" v-bind:key="index">
                    <li>{{to}}</li>
                  </ul>
                  <p>{{activeMail.body_txt}}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

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
          <a href="#" v-on:click="addMail(mail.id)" class="card-link">Refresh</a>
          <a href="#" v-on:click="showModal(mail.id)" class="card-link">Show</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import store from "../stores";
export default {
  data() {
    return {
      showmodal: false
    };
  },
  computed: {
    mails() {
      return store.getters.getMails;
    },
    activeMail() {
      return store.getters.getActiveMail;
    }
  },
  methods: {
    addMail(id) {
      this.showmodal = false;
      store.dispatch("addMail", { id });
    },
    showModal(id) {
      store.dispatch("setActiveMail", { id });
      this.showmodal = true;
    }
  }
};
</script>
